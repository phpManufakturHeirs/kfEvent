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
        $group_table = FRAMEWORK_TABLE_PREFIX.'event_group';
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `event_id` INT(11) NOT NULL AUTO_INCREMENT,
        `group_id` INT(11) NOT NULL DEFAULT '-1',
        `event_type` ENUM('EVENT', 'DATE', 'TASK') NOT NULL DEFAULT 'EVENT',
        `event_organizer` INT(11) NOT NULL DEFAULT '-1',
        `event_location` INT(11) NOT NULL DEFAULT '-1',
        `event_date_from` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_date_to` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_publish_from` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_publish_to` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_costs` FLOAT NOT NULL DEFAULT '0',
        `event_participants_max` INT(11) NOT NULL DEFAULT '-1',
        `event_participants_total` INT(11) NOT NULL DEFAULT '0',
        `event_deadline` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `event_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `event_timestamp` TIMESTAMP,
        PRIMARY KEY (`event_id`),
        FOREIGN KEY (`group_id`) REFERENCES $group_table(`group_id`) ON DELETE CASCADE
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

    /**
     * Get a default (empty) record for a event
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return array(
            'event_id' => -1,
            'group_id' => -1,
            'event_type' => 'EVENT',
            'event_organizer' => -1,
            'event_location' => -1,
            'event_date_from' => '0000-00-00 00:00:00',
            'event_date_to' => '0000-00-00 00:00:00',
            'event_publish_from' => '0000-00-00 00:00:00',
            'event_publish_to' => '0000-00-00 00:00:00',
            'event_costs' => 0,
            'event_participants_max' => -1,
            'event_participants_total' => 0,
            'event_deadline' => '0000-00-00 00:00:00',
            'event_status' => 'ACTIVE',
            'event_timestamp' => '0000-00-00 00:00:00'
        );
    }

    /**
     * Select a evetn record by the given event_id
     * Return FALSE if the record does not exists
     *
     * @param integer $event_id
     * @throws \Exception
     * @return multitype:array|boolean
     */
    public function select($event_id)
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `event_id`='$event_id'";
            $result = $this->app['db']->fetchAssoc($SQL);
            if (is_array($result) && isset($result['event_id'])) {
                $event = array();
                foreach ($result as $key => $value) {
                    $event[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                return $event;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
    
    public function selectEvent($event_id)
    {
        try {
            $event = self::$table_name;
            $desc = FRAMEWORK_TABLE_PREFIX.'event_description';
            $SQL = "SELECT * FROM `$event`, `$desc` WHERE $event.event_id=$desc.event_id AND $event.event_id='$event_id'";
            $result = $this->app['db']->fetchAssoc($SQL);
            if (is_array($result) && isset($result['event_id'])) {
                $event = array();
                foreach ($result as $key => $value) {
                    $event[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                return $event;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Insert a new event record
     *
     * @param array $data
     * @param reference integer $event_id
     * @throws \Exception
     */
    public function insert($data, &$event_id=null)
    {
        try {
            $insert = array();
            foreach ($data as $key => $value) {
                if (($key == 'event_id') || ($key == 'event_timestamp')) continue;
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $event_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
    
    public function selectAll($status='DELETED', $status_operator='!=')
    {
        try {
            $event = self::$table_name;
            $desc = FRAMEWORK_TABLE_PREFIX.'event_description';
            $SQL = "SELECT * FROM `$event`, `$desc` WHERE $event.event_id=$desc.event_id AND `event_status`{$status_operator}'{$status}' ORDER BY `event_date_from` DESC";
            $results = $this->app['db']->fetchAll($SQL);
            $groups = array();
            if (is_array($results)) {
                foreach ($results as $result) {
                    $record = array();
                    foreach ($result as $key => $value) {
                        $record[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $groups[] = $record;
                }
            }
            return $groups;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}