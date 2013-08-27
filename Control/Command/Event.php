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
    protected static $permalink_parent_url = null;
    protected static $event_id = -1;
    protected static $redirect_route_event_id = '/event/id/{event_id}';

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::initParameters()
     */
    protected function initParameters(Application $app, $parameter_id=-1)
    {
        // init parent
        parent::initParameters($app, $parameter_id);

        // init Event
        self::$parameter = $this->getCommandParameters();
        $this->EventData = new EventData($app);
        $this->Message = new Message($app);
    }

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::setRedirectRoute()
     */
    public function setRedirectRoute($route)
    {
        parent::setRedirectActive(true);
        parent::setRedirectRoute($route);

        $this->Message->setRedirectActive($this->getRedirectActive());
        $this->Message->setRedirectRoute($this->getRedirectRoute());
    }

    protected function selectID($event_id, $view='small')
    {
        // first set the redirect route
        $this->setRedirectRoute(str_replace('{event_id}', $event_id, self::$redirect_route_event_id));

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

        // return the event dialog
        return $this->app['twig']->render($this->app['utils']->templateFile(
            '@phpManufaktur/Event/Template',
            "command/event.view.$view.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'event' => $event,
                'url' => array(
                    'detail' => $detail_url
                ),
                'parameter' => self::$parameter
            ));
    }

    /**
     * Check if a parent URL for redirecting permanent links is defined
     *
     * @return boolean
     */
    protected function getPermaLinkParent()
    {
        if (file_exists(MANUFAKTUR_PATH.'/Event/event.json')) {
            $config = $this->app['utils']->readJSON(MANUFAKTUR_PATH.'/Event/event.json');
            if (isset($config['permalink_parent_url']) && !empty($config['permalink_parent_url'])) {
                self::$permalink_parent_url = $config['permalink_parent_url'];
                return true;
            }
        }
        return false;
    }

    /**
     * Will be called from outside as permanent link for the event '/event/id/{event_id}'.
     * Generate extra parameter and grant a redirection into a parent CMS page
     *
     * @param Application $app
     * @param integer $event_id
     * @return string event dialog
     */
    public function ControllerSelectID(Application $app, $event_id, $view='small')
    {
        $this->initParameters($app);

        if ($this->getPermaLinkParent()) {
            $this->setCMSpageURL(self::$permalink_parent_url);
            return $this->selectID($event_id, $view);
        }
        else {
            throw new \Exception("Please specifiy a parent URL in event.json.");
        }
    }

    protected function checkParameterMap()
    {
        if (isset(self::$parameter['map'])) {
            $map = strtolower(trim(self::$parameter['map']));
            self::$parameter['map'] = array(
                'map' => false,
                'link' => false
            );
            if (empty($map)) {
                self::$parameter['map'] = array(
                    'map' => true,
                    'link' => true
                );
            }
            elseif (strpos($map, ',')) {
                $values = explode(',', $map);
                foreach ($values as $key => $value) {
                    $value = trim($value);
                    if (in_array($value, array('map', 'link'))) {
                        self::$parameter['map'][$value] = true;
                    }
                }
            }
            elseif (in_array($map, array('map', 'link'))) {
                self::$parameter['map'][$map] = true;
            }
        }
        else {
            self::$parameter['map'] = array(
                'map' => false,
                'link' => false
            );
        }
    }

    public function exec($parameter)
    {
        try {
            $this->setCommandParameters($parameter);
            self::$parameter = $this->getCommandParameters();

            // general check for parameters
            $this->checkParameterMap();

            if (isset(self::$parameter['id'])) {
                return $this->selectID(self::$parameter['id']);
            }
            else {
                // no parameter which can be processed
                $Message = new Message($this->app);
                return $Message->render('Missing a second parameter corresponding to the mode <i>item</i>');
            }

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

}
