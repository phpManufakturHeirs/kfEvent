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

class Subscription
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'event_subscription';
    }

    /**
     * Create the SUBSCRIPTION table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $table_event = FRAMEWORK_TABLE_PREFIX.'event_event';
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `subscription_id` INT(11) NOT NULL AUTO_INCREMENT,
        `event_id` INT(11) NOT NULL DEFAULT '-1',
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `message_id` INT(11) NOT NULL DEFAULT '-1',
        `subscription_participants` INT(11) NOT NULL DEFAULT '0',
        `subscription_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `subscription_guid` VARCHAR(64) NOT NULL DEFAULT '',
        `subscription_confirmation` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `subscription_status` ENUM('PENDING','CONFIRMED','CANCELED','LOCKED','DELETED') NOT NULL DEFAULT 'PENDING',
        `subscription_timestamp` TIMESTAMP,
        PRIMARY KEY (`subscription_id`),
        INDEX (`event_id`, `contact_id`, `message_id`),
        CONSTRAINT
            FOREIGN KEY (`event_id`)
            REFERENCES $table_event (`event_id`)
            ON DELETE CASCADE
        )
    COMMENT='Images associated to Events'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'event_subscription'", array(__METHOD__, __LINE__));
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
            $this->app['monolog']->addInfo("Drop table 'event_subscription'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
    /**
     * Get a default record for a subscription
     *
     * @param integer $event_id
     * @return multitype:number string unknown
     */
    public function getDefaultRecord($event_id=-1)
    {
        return array(
            'subscription_id' => -1,
            'event_id' => $event_id,
            'contact_id' => -1,
            'message_id' => -1,
            'subscription_participants' => 0,
            'subscription_date' => '0000-00-00 00:00:00',
            'subscription_guid' => '',
            'subscription_confirmation' => '0000-00-00 00:00:00',
            'subscription_status' => 'PENDING',
            'subscription_timestamp' => '0000-00-00 00:00:00'
        );
    }

    /**
     * Insert a new subscription
     *
     * @param array $data
     * @param reference integer $subscription_id
     * @throws \Exception
     */
    public function insert($data, &$subscription_id=null)
    {
        try {
            $insert = array();
            foreach ($data as $key => $value) {
                if (($key == 'subscription_id') || ($key == 'subscription_timestamp')) continue;
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
            }
            if (!isset($data['event_id'])) {
                throw new \Exception("Missing the Event ID, can't insert the subscription!");
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $subscription_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Select all subsriptions for the given Event ID
     *
     * @param integer $event_id
     * @throws \Exception
     * @return multitype:multitype:unknown
     */
    public function selectByEventID($event_id)
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `event_id`='$event_id'";
            $results = $this->app['db']->fetchAll($SQL);
            $subscription = array();
            foreach ($results as $image) {
                $record = array();
                foreach ($image as $key => $value) {
                    $record[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                $subscription[] = $record;
            }
            return $subscription;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
