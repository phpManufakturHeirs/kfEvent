<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/event
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Data\Event;

use Silex\Application;
use Carbon\Carbon;

class RecurringEvent
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'event_recurring_event';
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
        `recurring_id` INT(11) NOT NULL AUTO_INCREMENT,
        `parent_event_id` INT(11) NOT NULL DEFAULT -1,
        `recurring_type` ENUM('NONE','DAY','WEEK','MONTH','YEAR') NOT NULL DEFAULT 'NONE',
        `day_type` ENUM('DAILY','WORKDAYS') NOT NULL DEFAULT 'DAILY',
        `day_sequence` INT(11) NOT NULL DEFAULT 1,
        `week_sequence` INT(11) NOT NULL DEFAULT 1,
        `week_days` SET('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY') NOT NULL DEFAULT 'MONDAY,WEDNESDAY',
        `month_type` ENUM('SEQUENCE','PATTERN') NOT NULL DEFAULT 'SEQUENCE',
        `month_sequence` INT(11) NOT NULL DEFAULT 1,
        `month_sequence_day` INT(11) NOT NULL DEFAULT 1,
        `month_sequence_month` INT(11) NOT NULL DEFAULT 1,
        `month_pattern_type` ENUM('FIRST','SECOND','THIRD','FOURTH','LAST') NOT NULL DEFAULT 'FIRST',
        `month_pattern_day` ENUM('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY') NOT NULL DEFAULT 'MONDAY',
        `month_pattern_sequence` INT(11) NOT NULL DEFAULT 1,
        `year_repeat` INT(11) NOT NULL DEFAULT 1,
        `year_type` ENUM('SEQUENCE','PATTERN') NOT NULL DEFAULT 'SEQUENCE',
        `year_sequence_day` INT(11) NOT NULL DEFAULT 1,
        `year_sequence_month` ENUM('JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE','JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER') NOT NULL DEFAULT 'JANUARY',
        `year_pattern_type` ENUM('FIRST','SECOND','THIRD','FOURTH','LAST') NOT NULL DEFAULT 'FIRST',
        `year_pattern_day` ENUM('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY') NOT NULL DEFAULT 'MONDAY',
        `year_pattern_month` ENUM('JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE','JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER') NOT NULL DEFAULT 'JANUARY',
        `recurring_date_end` DATE NOT NULL DEFAULT '0000-00-00',
        `recurring_timestamp` TIMESTAMP,
        PRIMARY KEY (`recurring_id`),
        INDEX (`parent_event_id`)
        )
    COMMENT='Control table for recurring events'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'event_recurring_event'", array(__METHOD__, __LINE__));
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
        $this->app['db.utils']->dropTable(self::$table_name);
    }

}
