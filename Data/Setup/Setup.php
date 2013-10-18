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
use phpManufaktur\Event\Data\Event\Subscription;

class Setup
{

    /**
     * Check if the config file exists and create it with default values
     *
     * @param Application $app
     */
    protected function createConfigFile(Application $app)
    {
        if (!file_exists(MANUFAKTUR_PATH.'/Event/config.event.json')) {
            $config = array(
                'general' => array(
                    'max_execution_time' => 60
                ),
                'event' => array(
                    'microdata' => array(
                        'offer_count_unlimited' => 20
                    ),
                    'subscription' => array(
                        'confirm' => array(
                            'double_opt_in' => false,
                            'mail_to' => array(
                                'contact',
                                'provider',
                                'organizer'
                            )
                        )
                    )
                ),
                'contact' => array(
                    'confirm' => array(
                        'double_opt_in' => true,
                        'mail_to' => array(
                            'contact',
                            'provider'
                        )
                    )
                ),
                'permalink' => array(
                    'cms' => array(
                        'url' => ''
                    )
                ),
                'ical' => array(
                    'active' => true,
                    'framework' => array(
                        'path' => '/media/protected/event/ical'
                    )
                ),
                'qrcode' => array(
                    "active" => false,
                    "framework" => array(
                        "path" => array(
                            "link" => '/media/protected/event/qrcode/link',
                            "ical" => '/media/protected/event/qrcode/ical'
                        )
                    ),
                    "settings" => array(
                        "content" => "link",
                        "size" => 3,
                        "error_correction" => 1,
                        "margin" => 2
                    )
                ),
                'rating' => array(
                    'active' => true,
                    'type' => 'small',
                    'length' => 5,
                    'step' => true,
                    'rate_max' => 5,
                    'show_rate_info' => false
                )
            );
            // write the formatted config file to the path
            file_put_contents(MANUFAKTUR_PATH.'/Event/config.event.json', $app['utils']->JSONFormat($config));
        }
    }

    /**
     * Execute all steps needed to setup the Event application
     *
     * @param Application $app
     * @throws \Exception
     * @return string with result
     */
    public function exec(Application $app)
    {
        try {
            // check if the config file exists and create default settings
            $this->createConfigFile($app);

            $Group = new Group($app);
            $Group->createTable();

            $Event = new Event($app);
            $Event->createTable();

            $Description = new Description($app);
            $Description->createTable();

            $ExtraType = new ExtraType($app);
            $ExtraType->createTable();

            $ExtraGroup = new ExtraGroup($app);
            $ExtraGroup->createTable();

            $Extra = new Extra($app);
            $Extra->createTable();

            $OrganizerTag = new OrganizerTag($app);
            $OrganizerTag->createTable();

            $LocationTag = new LocationTag($app);
            $LocationTag->createTable();

            $ParticipantTag = new ParticipantTag($app);
            $ParticipantTag->createTable();

            $Images = new Images($app);
            $Images->createTable();

            $Subscription = new Subscription($app);
            $Subscription->createTable();

            // setup kit_framework_event as Add-on in the CMS
            $admin_tool = new InstallAdminTool($app);
            $admin_tool->exec(MANUFAKTUR_PATH.'/Event/extension.json', '/event/cms');

            return $app['translator']->trans('Successfull installed the extension %extension%.',
                array('%extension%' => 'Event'));

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
