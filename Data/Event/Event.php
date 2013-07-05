<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Data\Event;

use Silex\Application;

class Event
{

    protected $app = null;
    protected static $table_name = null;


    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'event_event';
    }

    /**
     * Create the EVENT table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `event_id` INT(11) NOT NULL AUTO_INCREMENT,
        `event_type` ENUM('EVENT', 'DATE', 'TASK') NOT NULL DEFAULT 'EVENT',
        `event_group` INT(11) NOT NULL DEFAULT '-1',
        `event_organizer` INT(11) NOT NULL DEFAULT '-1',
        `event_location` INT(11) NOT NULL DEFAULT '-1',
        `event_date_from` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_date_to` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_publish_from` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_publish_to` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_costs` FLOAT NOT NULL DEFAULT '0',
        `event_participants_max` INT(11) NOT NULL DEFAULT '-1',
        `event_participants_total` INT(11) NOT NULL DEFAULT '0',
        `event_deadline` DATETIME NOT NULL DEFAULT '0000-00-00 00',
        `event_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `event_timestamp` TIMESTAMP,
        PRIMARY KEY (`event_id`)
        )
    COMMENT='The main event table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'event_event'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }


}