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

class Description
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'event_description';
    }

    /**
     * Create the EVENT table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $table_event = FRAMEWORK_TABLE_PREFIX.'event_event';
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `description_id` INT(11) NOT NULL AUTO_INCREMENT,
        `event_id` INT(11) NOT NULL DEFAULT '-1',
        `description_title` VARCHAR(255) NOT NULL DEFAULT '',
        `description_short` TEXT NOT NULL DEFAULT '',
        `description_long` TEXT NOT NULL DEFAULT '',
        `description_timestamp` TIMESTAMP,
        PRIMARY KEY (`description_id`),
        UNIQUE (`event_id`),
        CONSTRAINT
            FOREIGN KEY (`event_id`)
            REFERENCES $table_event (`event_id`)
            ON DELETE CASCADE
        )
    COMMENT='The descriptions table for Events'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'event_description'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }


}