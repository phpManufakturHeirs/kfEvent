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
use phpManufaktur\Event\Data\Event\Description;

class Event
{

    protected $app = null;
    protected static $table_name = null;
    protected $Description = null;
    protected $ExtraGroup = null;
    protected $ExtraType = null;
    protected $Extra = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'event_event';
        $this->Description = new Description($app);
        $this->ExtraGroup = new ExtraGroup($app);
        $this->ExtraType = new ExtraType($app);
        $this->Extra = new Extra($app);
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
    public function __select($event_id)
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
                // check for extra fields
                $event['extra_fields'] = $this->Extra->selectByEventID($event_id);
                // return complete event record
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
    public function __insert($data, &$event_id=null)
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
    
    public function insertEvent($data, &$event_id=null)
    {
        try {
            $insert_event = array();
            $insert_description = array();
            $keys_event = array_keys($this->getDefaultRecord());
            $keys_description = array_keys($this->Description->getDefaultRecord());
            
            foreach ($data as $key => $value) {
                if (($key == 'event_id') || ($key == 'event_timestamp') || ($key == 'description_timestamp')) continue;
                if (in_array($key, $keys_event)) {
                    $insert_event[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                elseif (in_array($key, $keys_description)) {
                    $insert_description[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
            }
            // insert event record
            $this->app['db']->insert(self::$table_name, $insert_event);
           
            $event_id = $this->app['db']->lastInsertId();
            
            
            if ($event_id > 0) {
                // check the description fields
                $insert_description['event_id'] = $event_id;
                if (!isset($insert_description['description_title']))
                    $insert_description['description_title'] = '';
                if (!isset($insert_description['description_short']))
                    $insert_description['description_short'] = '';
                if (!isset($insert_description['description_long']))
                    $insert_description['description_long'] = '';
                // insert a description record
                $this->Description->insert($insert_description);
            }
            // insert additional fields
            
            // select all type IDs for this event group
            $extra_fields = $this->ExtraGroup->selectTypeIDByGroupID($data['group_id']);
            // loop through the extra fields
            foreach ($extra_fields as $extra_field) {
                // get the extra type information
                $extra_type = $this->ExtraType->select($extra_field['extra_type_id']);
                // create empty extra record for this event ID
                $data = array(
                    'extra_type_id' => $extra_field['extra_type_id'],
                    'extra_type_name' => $extra_type['extra_type_name'],
                    'group_id' => $data['group_id'],
                    'event_id' => $event_id,
                    'extra_type_type' => $extra_type['extra_type_type'],
                    'extra_text' => '',
                    'extra_html' => '',
                    'extra_varchar' => '',
                    'extra_int' => '0',
                    'extra_float' => '0',
                    'extra_date' => '0000-00-00',
                    'extra_datetime' => '0000-00-00 00:00:00',
                    'extra_time' => '00:00:00'
                );
                $this->Extra->insert($data);
            }
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
    
    public function updateEvent($data, $event_id)
    {
        try {
            $update_event = array();
            $update_description = array();
            $keys_event = array_keys($this->getDefaultRecord());
            $keys_description = array_keys($this->Description->getDefaultRecord());
            foreach ($data as $key => $value) {
                if (($key == 'event_id') || ($key == 'event_timestamp') || ($key == 'description_timestamp')) continue;
                if (in_array($key, $keys_event)) {
                    $update_event[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
                }
                elseif (in_array($key, $keys_description)) {
                    $update_description[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
                }
            }
            if (!empty($update_event)) {
                // update event
                $this->app['db']->update(self::$table_name, $update_event, array('event_id' => $event_id));
            }
            if (!empty($update_description)) {
                // update description
                $this->app['db']->update(FRAMEWORK_TABLE_PREFIX.'event_description', $update_description, array('event_id' => $event_id));
            }
            // update extra fields
            $this->Extra->updateByEventID($data, $event_id);
                
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}