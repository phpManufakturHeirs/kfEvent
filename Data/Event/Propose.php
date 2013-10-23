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

class Propose
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'event_propose';
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
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `submitter_id` INT(11) NOT NULL DEFAULT '-1',
        `submitted_when` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `submitter_status` ENUM('PENDING', 'CONFIRMED') NOT NULL DEFAULT 'PENDING',
        `submitter_status_when` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `new_event_id` INT(11) NOT NULL DEFAULT '-1',
        `new_organizer_id` INT(11) NOT NULL DEFAULT '-1',
        `new_location_id` INT(11) NOT NULL DEFAULT '-1',
        `admin_status` ENUM('PENDING','REJECTED','CONFIRMED') NOT NULL DEFAULT 'PENDING',
        `admin_status_when` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `timestamp` TIMESTAMP,
        PRIMARY KEY (`id`)
        )
    COMMENT='Propose events'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'event_propose'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Delete table - switching check for foreign keys off before executing
     *
     * @throws \Exception
     */
    public function dropTable()
    {
        try {
            $table = self::$table_name;
            $SQL = <<<EOD
    SET foreign_key_checks = 0;
    DROP TABLE IF EXISTS `$table`;
    SET foreign_key_checks = 1;
EOD;
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Drop table 'event_propose'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }


}
