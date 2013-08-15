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
use phpManufaktur\Basic\Control\CMS\InstallAdminTool;
use phpManufaktur\Event\Data\Event\Images;

class Setup
{

    protected $app = null;

    public function exec(Application $app)
    {
        $this->app = $app;

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

            $Images = new Images($this->app);
            $Images->createTable();

            // setup kit_framework_event as Add-on in the CMS
            $admin_tool = new InstallAdminTool($this->app);
            $admin_tool->exec(MANUFAKTUR_PATH.'/Event/extension.json', '/event/cms');

            Return "The setup was successful";

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
