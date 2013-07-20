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

class Group
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'event_group';
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
        `group_id` INT(11) NOT NULL AUTO_INCREMENT,
        `group_name` VARCHAR(64) NOT NULL DEFAULT '',
        `group_description` TEXT NOT NULL,
        `group_organizer_contact_tags` VARCHAR(512) NOT NULL DEFAULT '',
        `group_location_contact_tags` VARCHAR(512) NOT NULL DEFAULT '',
        `group_participant_contact_tags` VARCHAR(512) NOT NULL DEFAULT '',
        `group_extra_fields` VARCHAR(512) NOT NULL DEFAULT '',
        `group_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `group_timestamp` TIMESTAMP,
        PRIMARY KEY (`group_id`)
        )
    COMMENT='The group definition table for Events'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'event_group'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function getDefaultRecord()
    {
        return array(
            'group_id' => -1,
            'group_name' => '',
            'group_description' => '',
            'group_organizer_contact_tags' => '',
            'group_location_contact_tags' => '',
            'group_participant_contact_tags' => '',
            'group_extra_fields' => '',
            'group_status' => 'ACTIVE',
            'group_timestamp' => '0000-00-00 00:00:00'
        );
    }

    public function selectAll($status='DELETED', $status_operator='!=')
    {
        try {
            $results = $this->app['db']->fetchAll("SELECT * FROM `".self::$table_name."` WHERE `group_status`{$status_operator}'{$status}' ORDER BY `group_name` ASC");
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

    /**
     * Select a group record by the given group_id
     * Return FALSE if the record does not exists
     *
     * @param integer $group_id
     * @throws \Exception
     * @return multitype:array|boolean
     */
    public function select($group_id)
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `group_id`='$group_id'";
            $result = $this->app['db']->fetchAssoc($SQL);
            if (is_array($result) && isset($result['group_id'])) {
                $group = array();
                foreach ($result as $key => $value) {
                    $group[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                return $group;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Check if the desired Group name already existst. Optionally exclude the
     * given group id from the check
     *
     * @param string $group_name
     * @param integer $exclude_group_id
     * @throws \Exception
     * @return boolean
     */
    public function existsGroupName($group_name, $exclude_group_id=null)
    {
        try {
            $SQL = "SELECT `group_name` FROM `".self::$table_name."` WHERE `group_name`='$group_name'";
            if (is_numeric($exclude_group_id)) {
                $SQL .= " AND `group_id` != '$exclude_group_id'";
            }
            $result = $this->app['db']->fetchColumn($SQL);
            return (strtoupper($result) == strtoupper($group_name)) ? true : false;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Insert a new group record
     *
     * @param array $data
     * @param reference integer $group_id
     * @throws \Exception
     */
    public function insert($data, &$group_id=null)
    {
        try {
            $insert = array();
            foreach ($data as $key => $value) {
                if (($key == 'group_id') || ($key == 'group_timestamp')) continue;
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $group_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Update the group record for the given ID
     *
     * @param array $data
     * @param integer $group_id
     * @throws \Exception
     */
    public function update($data, $group_id)
    {
        try {
            $update = array();
            foreach ($data as $key => $value) {
                if (($key == 'group_id') || ($key == 'group_timestamp') || ($key == 'group_name')) continue;
                $update[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            if (!empty($update)) {
                $this->app['db']->update(self::$table_name, $update, array('group_id' => $group_id));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
}