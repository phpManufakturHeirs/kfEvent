<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Data\Setup;

use Silex\Application;
use phpManufaktur\Event\Data\Event\Event;
use phpManufaktur\Event\Data\Event\Group;
use phpManufaktur\Event\Data\Event\Description;
use phpManufaktur\Event\Data\Event\ExtraType;
use phpManufaktur\Event\Data\Event\Extra;

class Setup
{

    protected $app = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function exec()
    {
        try {
            $Event = new Event($this->app);
            $Event->createTable();

            $Group = new Group($this->app);
            $Group->createTable();

            $Description = new Description($this->app);
            $Description->createTable();

            $ExtraType = new ExtraType($this->app);
            $ExtraType->createTable();

            $Extra = new Extra($this->app);
            $Extra->createTable();



        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}