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
use phpManufaktur\Event\Data\Event\Event;
use Carbon\Carbon;


class EventSearch
{
    protected $app = null;
    protected $Event = null;
    protected $Carbon = null;

    /**
     * Constructor for the Event filter
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->Event = new Event($app);
        $this->Carbon = new Carbon();
    }


    public function search($search_term, $groups=null, $status='DELETED', $status_operator='!=', $order_by='description_title', $order_direction='ASC', $return_detail=false)
    {
        try {
            $SQL = ($return_detail) ? "SELECT event.event_id FROM " : "SELECT * FROM ";
            $SQL .= "`".FRAMEWORK_TABLE_PREFIX."event_event` AS event, `".FRAMEWORK_TABLE_PREFIX."event_description` AS description, ".
                "`".FRAMEWORK_TABLE_PREFIX."contact_overview` AS organizer, ".
                "`".FRAMEWORK_TABLE_PREFIX."contact_overview` AS location ".
                "WHERE event.event_id=description.event_id AND event.event_organizer=organizer.contact_id AND ".
                "event.event_location=location.contact_id AND (";
            $search = trim($search_term);
            $search_array = array();
            if (strpos($search, ' ')) {
                $dummy = explode(' ', $search_term);
                foreach ($dummy as $item) {
                    $search_array[] = trim($item);
                }
            }
            else {
                $search_array[] = trim($search_term);
            }
            $start = true;
            $skipped = false;
            foreach ($search_array as $search) {
                if (!$skipped) {
                    if ($start) {
                        $SQL .= "(";
                        $start = false;
                    }
                    elseif (strtoupper($search) == 'AND') {
                        $SQL .= ") AND (";
                        $skipped = true;
                        continue;
                    }
                    elseif (strtoupper($search) == 'NOT') {
                        $SQL .= ") AND NOT (";
                        $skipped = true;
                        continue;
                    }
                    elseif (strtoupper($search) == 'OR') {
                        $SQL .= ") OR (";
                        $skipped = true;
                        continue;
                    }
                    else {
                        $SQL .= ") OR (";
                    }
                }
                else {
                    $skipped = false;
                }

                $SQL .= "event.event_id = '$search' OR "
                    ."location.contact_name LIKE '%$search%' OR "
                    ."location.person_first_name LIKE '%$search%' OR "
                    ."location.person_last_name LIKE '%$search%' OR "
                    ."location.person_nick_name LIKE '%$search%' OR "
                    ."location.company_name LIKE '%$search%' OR "
                    ."location.company_department LIKE '%$search%' OR "
                    ."location.communication_phone LIKE '%$search%' OR "
                    ."location.communication_email LIKE '%$search%' OR "
                    ."location.address_street LIKE '%$search%' OR "
                    ."location.address_zip LIKE '%$search%' OR "
                    ."location.address_city LIKE '%$search%' OR "
                    ."location.address_area LIKE '%$search%' OR "
                    ."location.address_state LIKE '%$search%' OR "
                    ."location.address_country_code = '$search' OR "
                    ."organizer.contact_name LIKE '%$search%' OR "
                    ."organizer.person_first_name LIKE '%$search%' OR "
                    ."organizer.person_last_name LIKE '%$search%' OR "
                    ."organizer.person_nick_name LIKE '%$search%' OR "
                    ."organizer.company_name LIKE '%$search%' OR "
                    ."organizer.company_department LIKE '%$search%' OR "
                    ."organizer.communication_phone LIKE '%$search%' OR "
                    ."organizer.communication_email LIKE '%$search%' OR "
                    ."organizer.address_street LIKE '%$search%' OR "
                    ."organizer.address_zip LIKE '%$search%' OR "
                    ."organizer.address_city LIKE '%$search%' OR "
                    ."organizer.address_area LIKE '%$search%' OR "
                    ."organizer.address_state LIKE '%$search%' OR "
                    ."organizer.address_country_code = '$search' OR "
                    ."description.description_title LIKE '%$search%' OR "
                    ."description.description_short LIKE '%$search%' OR "
                    ."description.description_long LIKE '%$search%' OR "
                    ."event.event_date_from LIKE '%$search%' OR "
                    ."event.event_date_to LIKE '%$search%'";
            }
            $SQL .= ")) AND ";

            if (is_array($groups)) {
                // select specified event groups
                $SQL .= "(";
                $start = true;
                foreach ($groups as $group) {
                    if ($start) {
                        $start = false;
                    }
                    else {
                        $SQL .= " OR ";
                    }
                    $SQL .= "(`group_id` = '$group')";
                }
                $SQL .= ") AND ";
            }

            // finish the SQL
            $SQL .= "`event_status` $status_operator '$status' ORDER BY $order_by $order_direction";

            $results = $this->app['db']->fetchAll($SQL);

            $events = array();
            if ($return_detail) {
                // return a detailed record with all information
                $EventData = new Event($this->app);
                foreach ($results as $result) {
                    $events[] = $EventData->selectEvent($result['event_id'], false);
                }
            }
            else {
                foreach ($results as $result) {
                    $event = array();
                    foreach ($result as $key => $value) {
                        $event[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $events[] = $event;
                }
            }

            return (!empty($events)) ? $events : false;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
