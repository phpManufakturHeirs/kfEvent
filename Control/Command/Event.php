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
    protected static $redirect_route_event_id = '/event/id/{event_id}';
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

        // check the CMS GET parameters
        $GET = $this->getCMSgetParameters();
        if (isset($GET['command']) && ($GET['command'] == 'event')) {
            foreach ($GET as $key => $value) {
                if ($key == 'command') continue;
                $parameters[$key] = $value;
            }
            $this->setCommandParameters($parameters);
        }
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

        if (isset(self::$parameter['redirect'])) {
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
            // use the URL of the parent CMS page
            $url = $this->getCMSpageURL();
        }

        $detail_url = sprintf('%s%s%s', $url, (strpos($url, '?') === false) ? '?' : '&',
                http_build_query(array(
                    'command' => 'event',
                    'action' => 'event',
                    'view' => 'detail',
                    'id' => $event_id
                )));

        $qrcode_url = '';
        $qrcode_width = 0;
        $qrcode_height = 0;
        if (isset(self::$parameter['qrcode']) && self::$config['qrcode']['active']) {
            if (self::$config['qrcode']['settings']['content'] == 'ical') {
                if (file_exists(FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['ical']."/$event_id.png")) {
                    list($qrcode_width, $qrcode_height) = getimagesize(FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['ical']."/$event_id.png");
                    $qrcode_url = FRAMEWORK_URL.'/event/qrcode/'.$event_id;
                }
            }
            else {
                if (file_exists(FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['link']."/$event_id.png")) {
                    list($qrcode_width, $qrcode_height) = getimagesize(FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['link']."/$event_id.png");
                    $qrcode_url = FRAMEWORK_URL.'/event/qrcode/'.$event_id;
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
                'url' => array(
                    'detail' => $detail_url,
                    'permanent' => FRAMEWORK_URL.str_replace('{event_id}', $event_id, self::$redirect_route_event_id),
                    'ical' => FRAMEWORK_URL.self::$config['ical']['framework']['path'].'/'.$event_id.'.ics',
                ),
                'qrcode' => array(
                    'url' => $qrcode_url,
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
        if (isset(self::$config['permalink']['cms']['url']) && !empty(self::$config['permalink']['cms']['url'])) {
            $this->setCMSpageURL(self::$config['permalink']['cms']['url']);
            //$this->setRedirectActive(true);

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

        $available_links = array('detail', 'ical', 'map', 'permanent');
        self::$parameter['link'] = array();
        foreach ($available_links as $link) {
            self::$parameter['link'][$link] = in_array($link, $links) ? true : false;
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
