<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/propangas24
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

    public function __construct(Application $app=null)
    {
        parent::__construct($app);
        if (!is_null($app)) {
            $this->initialize($app);
        }
    }

    protected function initialize(Application $app)
    {
        parent::initialize($app);
        $this->EventData = new EventData($this->app);
    }
    
    public function exec(Application $app)
    {
        $this->initialize($app);
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