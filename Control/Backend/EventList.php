<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Backend;

use Silex\Application;
use phpManufaktur\Event\Control\Backend\Backend;
use phpManufaktur\Event\Data\Event\Event as EventData;

class EventList extends Backend {

    protected static $event_id = -1;
    protected $EventData = null;

    /**
     * Constructor
     *
     * @param Application $app can be NULL
     */
    public function __construct(Application $app=null)
    {
        parent::__construct($app);
        if (!is_null($app)) {
            $this->initialize($app);
        }
    }

    /**
     * Initialize the parent class Backend and the class EventList
     *
     * @see \phpManufaktur\Event\Control\Backend\Backend::initialize()
     * @param Application $app
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);
        $this->EventData = new EventData($this->app);
    }

    /**
     * Execute class as controller
     *
     * @param Application $app
     * @return string rendered Event List
     */
    public function exec(Application $app)
    {
        $this->initialize($app);
        // cleanup events
        $this->EventData->cleanupEvents();
        // select all events
        $events = $this->EventData->selectAll();
        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Event/Template', 'backend/event.list.twig'),
            array(
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('event_list'),
                'message' => $this->getMessage(),
                'events' => $events
            ));
    }

}
