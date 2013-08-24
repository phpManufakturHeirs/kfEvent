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

class Event extends Basic
{
    protected $EventData = null;
    protected $Message = null;
    protected static $parameter = null;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        self::$parameter = $this->getCommandParameters();
        $this->EventData = new EventData($app);
        $this->Message = new Message($app);
    }

    protected function getEventByID($event_id)
    {
        if (false === ($event = $this->EventData->selectEvent($event_id))) {
            return $this->Message->render('The record with the ID %id% does not exists!', array('%id%' => $event_id));
        }

        $view = 'small';
        if (isset(self::$parameter['view'])) {
            if (in_array(strtolower(self::$parameter['view']), array('small', 'detail', 'custom'))) {
                $view = strtolower(self::$parameter['view']);
            }
            else {
                // undefined view!
                return $this->Message->render('The view <b>%view%</b> does not exists!',
                    array('%view%' => strtolower(self::$parameter['view'])));
            }
        }
        // return the event dialog
        return $this->app['twig']->render($this->app['utils']->templateFile(
            '@phpManufaktur/Event/Template',
            "command/event.view.$view.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'event' => $event
            ));
    }

    public function exec()
    {
        try {
            if (isset(self::$parameter['id'])) {
                return $this->getEventByID(self::$parameter['id']);
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
