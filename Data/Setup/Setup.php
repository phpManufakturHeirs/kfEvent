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
use phpManufaktur\Event\Data\Event\ExtraGroup;
use phpManufaktur\Event\Data\Event\OrganizerTag;
use phpManufaktur\Event\Data\Event\LocationTag;
use phpManufaktur\Event\Data\Event\ParticipantTag;

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
            $Group = new Group($this->app);
            $Group->createTable();

            $Event = new Event($this->app);
            $Event->createTable();

            $Description = new Description($this->app);
            $Description->createTable();

            $ExtraType = new ExtraType($this->app);
            $ExtraType->createTable();

            $ExtraGroup = new ExtraGroup($this->app);
            $ExtraGroup->createTable();

            $Extra = new Extra($this->app);
            $Extra->createTable();

            $OrganizerTag = new OrganizerTag($this->app);
            $OrganizerTag->createTable();

            $LocationTag = new LocationTag($this->app);
            $LocationTag->createTable();

            $ParticipantTag = new ParticipantTag($this->app);
            $ParticipantTag->createTable();

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}