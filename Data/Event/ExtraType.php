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

class ExtraType
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'event_extra_type';
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
        `extra_type_id` INT(11) NOT NULL AUTO_INCREMENT,
        `group_id` INT(11) NOT NULL DEFAULT '-1',
        `extra_type_type` ENUM('TEXT','HTML','VARCHAR','INT','FLOAT','DATE','DATETIME','TIME') NOT NULL DEFAULT 'VARCHAR',
        `extra_type_name` VARCHAR(64) NOT NULL DEFAULT '',
        `extra_type_description` TEXT NOT NULL,
        `extra_type_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `extra_type_timestamp` TIMESTAMP,
        PRIMARY KEY (`extra_type_id`),
        INDEX (`group_id`)
        )
    COMMENT='The table for definition of types for extra fields'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'event_extra_type'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }


}