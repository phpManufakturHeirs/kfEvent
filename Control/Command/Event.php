<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Command;

use Silex\Application;
use phpManufaktur\Basic\Control\kitCommand\Basic;
use phpManufaktur\Event\Data\Event\Event as EventData;
use phpManufaktur\Basic\Data\CMS\Page;

class Event extends Basic
{
    protected $EventData = null;
    protected $Message = null;
    protected static $parameter = null;
    protected static $event_id = -1;
    protected static $config;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::initParameters()
     */
    protected function initParameters(Application $app, $parameter_id=-1)
    {
        // init parent
        parent::initParameters($app, $parameter_id);

        // init Event
        $parameters = $this->getCommandParameters();

        // check the CMS GET parameters
        $GET = $this->app['request']->query->get('GET', array());
        foreach ($GET as $key => $value) {
            if (($key == 'pid') || ($key == 'parameter_id')) continue;
            $parameters[$key] = $value;
        }
        $this->setCommandParameters($parameters);

        self::$parameter = $this->getCommandParameters();

        // general check for parameters
        self::$parameter['map'] = (isset(self::$parameter['map'])) ? true : false;
        $this->checkParameterLink();
        self::$parameter['qrcode'] = (isset(self::$parameter['qrcode'])) ? true : false;

        $this->EventData = new EventData($app);
        $this->Message = new Message($app);

        // get the configuration
        self::$config = $this->app['utils']->readConfiguration(MANUFAKTUR_PATH.'/Event/config.event.json');
    }

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::setRedirectRoute()
     */
    public function setRedirectRoute($route)
    {
        parent::setRedirectActive(true);
        parent::setRedirectRoute($route);

        $this->Message->setRedirectActive(false);
        $this->Message->setRedirectRoute($this->getRedirectRoute());
    }

    protected function selectID($event_id, $view='small')
    {
        if (false === ($event = $this->EventData->selectEvent($event_id))) {
            return $this->Message->render('The record with the ID %id% does not exists!', array('%id%' => $event_id));
        }

        // check the view
        if (isset(self::$parameter['view'])) {
            $view = strtolower(self::$parameter['view']);
        }
        if (!in_array($view, array('small', 'detail', 'custom'))) {
            // undefined view!
            return $this->Message->render('The view <b>%view%</b> does not exists!',
                array('%view%' => $view));
        }
        $qrcode_width = 0;
        $qrcode_height = 0;
        if (isset(self::$parameter['qrcode']) && self::$config['qrcode']['active']) {
            if (self::$config['qrcode']['settings']['content'] == 'ical') {
                if (file_exists(FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['ical']."/$event_id.png")) {
                    list($qrcode_width, $qrcode_height) = getimagesize(FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['ical']."/$event_id.png");
                }
            }
            else {
                if (file_exists(FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['link']."/$event_id.png")) {
                    list($qrcode_width, $qrcode_height) = getimagesize(FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['link']."/$event_id.png");
                }
            }
        }
        // set redirect route
        $this->setRedirectRoute("/event/id/$event_id");
        $this->setRedirectActive(true);

        // return the event dialog
        return $this->app['twig']->render($this->app['utils']->templateFile(
            '@phpManufaktur/Event/Template',
            "command/event.view.$view.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'event' => $event,
                'qrcode' => array(
                    'active' => (($qrcode_width > 0) && ($qrcode_height > 0)),
                    'url' => FRAMEWORK_URL.'/event/qrcode/'.$event_id,
                    'width' => $qrcode_width,
                    'height' => $qrcode_height
                ),
                'parameter' => self::$parameter,
                'config' => self::$config
            ));
    }

    /**
     * Will be called from outside as permanent link for the event '/event/id/{event_id}'.
     * Generate extra parameter and grant a redirection into a parent CMS page
     *
     * @param Application $app
     * @param integer $event_id
     * @return string event dialog
     */
    public function ControllerSelectID(Application $app, $event_id, $view='detail')
    {
        $this->initParameters($app);
        self::$parameter['view'] = $view;
        self::$parameter['id'] = $event_id;

        return $this->selectID($event_id, $view);

        if (isset(self::$config['permalink']['cms']['url']) && !empty(self::$config['permalink']['cms']['url'])) {
            $this->setCMSpageURL(self::$config['permalink']['cms']['url']);
            $this->setRedirectActive(true);
            return $this->selectID($event_id, $view);
        }
        else {
            throw new \Exception("Please specifiy a permalink URL for the CMS in config.event.json.");
        }
    }

    protected function checkParameterLink()
    {
        if (!isset(self::$parameter['link'])) {
            $links = array();
        }
        elseif (strpos(self::$parameter['link'], ',')) {
            $links = array();
            foreach (explode(',', self::$parameter['link']) as $link) {
                $links[] = strtolower(trim($link));
            }
        }
        else {
            $links = array(strtolower(trim(self::$parameter['link'])));
        }

        $event_id = isset(self::$parameter['id']) ? self::$parameter['id'] : null;

        self::$parameter['link'] = array();

        $available_links = array('detail', 'ical', 'map', 'perma', 'permanent', 'subscribe');
        foreach ($available_links as $link) {
            self::$parameter['link'][$link]['target'] = '_self';
            switch ($link) {
                case 'detail':
                    self::$parameter['link'][$link]['active'] = in_array($link, $links);
                    if (isset(self::$parameter['redirect'])) {
                        self::$parameter['link'][$link]['target'] = '_top';
                        if (is_numeric(self::$parameter['redirect'])) {
                            // get the URL from the CMS PAGE ID
                            $Page = new Page($this->app);
                            $url = $Page->getURL(self::$parameter['redirect']);
                        }
                        else {
                            // use the submitted URL
                            $url = self::$parameter['redirect'];
                        }
                    }
                    else {
                        // use the route to event ID
                        $url = FRAMEWORK_URL."/event/id/".self::$parameter['id']."/view/detail";
                    }
                    self::$parameter['link'][$link]['url'] = sprintf('%s%s%s', $url, strpos($url, '?') ? '&' : '?',
                        http_build_query(array('pid' => $this->getParameterID())));
                    break;
                case 'ical':
                    self::$parameter['link'][$link]['active'] = in_array($link, $links);
                    self::$parameter['link'][$link]['url'] = !is_null($event_id) ? FRAMEWORK_URL."/event/ical/$event_id" : null;
                    break;
                case 'map':
                    self::$parameter['link'][$link]['active'] = in_array($link, $links);
                    // the url for the map will be set within the template
                    self::$parameter['link'][$link]['url'] = null;
                    break;
                case 'perma':
                case 'permanent':
                    self::$parameter['link']['permanent']['active'] = in_array($link, $links);
                    self::$parameter['link']['permanent']['url'] = !is_null($event_id) ? FRAMEWORK_URL."/event/id/$event_id" : null;
                    break;
                case 'subscribe':
                    self::$parameter['link'][$link]['active'] = in_array($link, $links);
                    self::$parameter['link'][$link]['url'] = !is_null($event_id) ? FRAMEWORK_URL."/event/subscribe/$event_id?pid=".$this->getParameterID() : null;
                    break;
                default:
                    throw new \Exception("The link $link is not defined!");
            }
        }
    }

    public function exec(Application $app)
    {
        $this->initParameters($app);
        if (isset(self::$parameter['id'])) {
            return $this->selectID(self::$parameter['id']);
        }
        else {
            // no parameter which can be processed
            $Message = new Message($this->app);
            return $Message->render('Missing a second parameter corresponding to the mode <i>item</i>');
        }
    }

}
