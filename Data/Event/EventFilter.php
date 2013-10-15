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


class EventFilter
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


    public function filter($filter=array(), &$messages=array(), &$SQL='')
    {
        try {
            // SQL body
            $SQL = "SELECT `event_id` FROM `".FRAMEWORK_TABLE_PREFIX."event_event`, `".
                FRAMEWORK_TABLE_PREFIX."contact_overview` WHERE `event_location`=`contact_id` ";

            if (empty($filter)) {
                // no filter defined - select all active events one week back and four weeks ahead

                // set actual date
                $this->Carbon->now();
                $today = $this->Carbon->toDateTimeString();
                // go a week back
                $this->Carbon->subWeek();
                $start_date = $this->Carbon->toDateTimeString();
                // add 5 weeks (4 weeks from today)
                $this->Carbon->addWeeks(5);
                $end_date = $this->Carbon->toDateTimeString();

                $SQL .= "AND `event_publish_from` <= '$today' AND `event_publish_to` >= '$today' ";
                $SQL .= "AND (`event_date_from` >= '$start_date' OR `event_date_to` >= '$start_date') ";
                $SQL .= "AND `event_date_from` <= '$end_date' ";
            }

            $strict_mode = (isset($filter['mode']) && (strtolower($filter['mode']) == 'strict')) ? true : false;
            $ignore_publish = (isset($filter['publish']) && (strtolower($filter['publish']) == 'ignore')) ? true : false;

            if (isset($filter['actual'])) {
                $skip = false;

                if (!strpos($filter['actual'], ',')) {
                    if ((strtolower($filter['actual']) == 'current') ||
                        (is_numeric($filter['actual']) && (intval($filter['actual']) == 0))) {
                        // use the current day and add 14 days ahead
                        $this->Carbon->startOfDay();
                        $start_date = $this->Carbon->toDateTimeString();
                        $this->Carbon->addDays(14);
                        $this->Carbon->endOfDay();
                        $end_date = $this->Carbon->toDateTimeString();
                    }
                    elseif (is_numeric($filter['actual'])) {
                        $this->Carbon->startOfDay();
                        $this->Carbon->addDays(intval($filter['actual']));
                        $start_date = $this->Carbon->toDateTimeString();
                        $this->Carbon->addDays(14);
                        $this->Carbon->endOfDay();
                        $end_date = $this->Carbon->toDateTimeString();
                    }
                    else {
                        // filter is not valid
                        $skip = true;
                        $messages[] = $this->app['translator']->trans("The filter 'actual' must be numeric or contain the keyword 'current' as first parameter.");
                    }
                }
                else {
                    list($start, $end) = explode(',', $filter['actual']);
                    $start = strtolower(trim($start));
                    $end = trim($end);
                    if (($start == 'current') || (is_numeric($start) && (intval($start) == 0))) {
                        $this->Carbon->startOfDay();
                        $start_date = $this->Carbon->toDateTimeString();
                    }
                    elseif (is_numeric($start)) {
                        $this->Carbon->startOfDay();
                        $this->Carbon->addDays(intval($start));
                        $start_date = $this->Carbon->toDateTimeString();
                    }
                    else {
                        // filter is not valid
                        $skip = true;
                        $messages[] = $this->app['translator']->trans("The filter 'actual' must be numeric or contain the keyword 'current' as first parameter.");
                    }
                    if (!$skip && is_numeric($end)) {
                        if (intval($end) < 1) {
                            // filter is not valid
                            $skip = true;
                            $messages[] = $this->app['translator']->trans("The second parameter for the filter 'actual' must be positive integer value.");
                        }
                        else {
                            $this->Carbon->addDays(intval($end));
                            $this->Carbon->endOfDay();
                            $end_date = $this->Carbon->toDateTimeString();
                        }
                    }
                    elseif (!is_numeric($end)) {
                        // filter is not valid
                        $skip = true;
                        $messages[] = $this->app['translator']->trans("The second parameter for the filter 'actual' must be a positive integer value.");
                    }
                }

                if (!$skip) {
                    if ($strict_mode) {
                        $SQL .= "AND `event_date_from` BETWEEN '$start_date' AND '$end_date' ";
                    }
                    else {
                        $SQL .= "AND (('$start_date' BETWEEN `event_date_from` AND `event_date_to`) OR ";
                        $SQL .= "(`event_date_from` BETWEEN '$start_date' AND '$end_date') OR ";
                        $SQL .= "(`event_date_to` BETWEEN '$start_date' AND '$end_date')) ";
                    }
                    if (!$ignore_publish) {
                        $SQL .= "AND ('$start_date' BETWEEN `event_publish_from` AND `event_publish_to`) ";
                    }
                    // unset the date filters
                    unset($filter['month']);
                    unset($filter['year']);
                    unset($filter['day']);
                }
            }

            if (isset($filter['day'])) {
                // filter for the DAY
                $skip = false;
                if (strtolower($filter['day']) == 'current') {
                    // set actual date
                    $this->Carbon->startOfDay();
                    $start_date = $this->Carbon->toDateTimeString();
                    $this->Carbon->endOfDay();
                    $end_date = $this->Carbon->toDateTimeString();
                }
                elseif (is_numeric($filter['day'])) {
                    // filter for the given DAY
                    $this->Carbon->startOfDay();
                    $day = intval($filter['day']);
                    $month = (isset($filter['month']) && is_numeric($filter['month'])) ? intval($filter['month']) : $this->Carbon->month;
                    $year = (isset($filter['year']) && is_numeric($filter['year'])) ? intval($filter['year']) : $this->Carbon->year;
                    $this->Carbon->setDate($year, $month, $day);
                    $start_date = $this->Carbon->toDateTimeString();
                    $this->Carbon->endOfDay();
                    $end_date = $this->Carbon->toDateTimeString();
                }
                else {
                    // filter is not valid
                    $skip = true;
                    $messages[] = $this->app['translator']->trans("The filter for 'day' must be numeric or contain the keyword 'current'");
                }

                if (!$skip) {
                    if ($strict_mode) {
                        $SQL .= "AND `event_date_from` BETWEEN '$start_date' AND '$end_date' ";
                    }
                    else {
                        $SQL .= "AND (('$start_date' BETWEEN `event_date_from` AND `event_date_to`) OR ";
                        $SQL .= "(`event_date_from` BETWEEN '$start_date' AND '$end_date') OR ";
                        $SQL .= "(`event_date_to` BETWEEN '$start_date' AND '$end_date')) ";
                    }
                    if (!$ignore_publish) {
                        $SQL .= "AND ('$start_date' BETWEEN `event_publish_from` AND `event_publish_to`) ";
                    }
                    // unset the date filters
                    unset($filter['month']);
                    unset($filter['year']);
                    unset($filter['day']);
                }
            }

            if (isset($filter['month'])) {
                // filter for the month
                $skip = false;

                if (strtolower($filter['month']) == 'current') {
                    // set the actual date
                    $this->Carbon->startOfDay();
                    $year = (isset($filter['year']) && is_numeric($filter['year'])) ? intval($filter['year']) : $this->Carbon->year;
                    if ($year < 100) {
                        $year += 2000;
                    }
                    $this->Carbon->year($year);
                    // first day of the month
                    $this->Carbon->startOfMonth();
                    $start_date = $this->Carbon->toDateTimeString();
                    $this->Carbon->endOfMonth();
                    $end_date = $this->Carbon->toDateTimeString();
                }
                elseif (is_numeric($filter['month'])) {
                    $this->Carbon->startOfDay();
                    $this->Carbon->month(intval($filter['month']));
                    $year = (isset($filter['year']) && is_numeric($filter['year'])) ? intval($filter['year']) : $this->Carbon->year;
                    if ($year < 100) {
                        $year += 2000;
                    }
                    $this->Carbon->year($year);
                    // first day of the month
                    $this->Carbon->startOfMonth();
                    $start_date = $this->Carbon->toDateTimeString();
                    $this->Carbon->endOfMonth();
                    $end_date = $this->Carbon->toDateTimeString();
                }
                else {
                    // filter is not valid
                    $skip = true;
                    $messages[] = $this->app['translator']->trans("The filter for 'month' must be numeric or contain the keyword 'current'");
                }

                if (!$skip) {
                    if ($strict_mode) {
                        $SQL .= "AND `event_date_from` BETWEEN '$start_date' AND '$end_date' ";
                    }
                    else {
                        $SQL .= "AND ((`event_date_from` BETWEEN '$start_date' AND '$end_date') OR ";
                        $SQL .= "(`event_date_to` BETWEEN '$start_date' AND '$end_date')) ";
                    }

                    if ($ignore_publish) {
                        $SQL .= "AND ('$start_date' BETWEEN `event_publish_from` AND `event_publish_to`) ";
                    }
                    // unset the date filters
                    unset($filter['month']);
                    unset($filter['year']);
                    unset($filter['day']);
                }
            }

            if (isset($filter['year'])) {
                // filter for the year
                $skip = false;

                if (strtolower($filter['year']) == 'current') {
                    // set the date, based from the actual day
                    $this->Carbon->startOfDay();
                    $this->Carbon->setDateTime($this->Carbon->year, 1, 1, 0, 0, 0);
                    $start_date = $this->Carbon->toDateTimeString();
                    $this->Carbon->setDateTime($this->Carbon->year, 12, 31, 23, 59, 59);
                    $end_date = $this->Carbon->toDateTimeString();
                }
                elseif (is_numeric($filter['year'])) {
                    $year = ($filter['year'] < 100) ? 2000 + intval($filter['year']) : intval($filter['year']);
                    $this->Carbon->setDateTime($year, 1, 1, 0, 0, 0);
                    $start_date = $this->Carbon->toDateTimeString();
                    $this->Carbon->setDateTime($year, 12, 31, 23, 59, 59);
                    $end_date = $this->Carbon->toDateTimeString();
                }
                else {
                    // filter is not valid
                    $skip = true;
                    $messages[] = $this->app['translator']->trans("The filter for 'year' must be numeric or contain the keyword 'current'");
                }

                if (!$skip) {
                    if ($strict_mode) {
                        $SQL .= "AND `event_date_from` BETWEEN '$start_date' AND '$end_date' ";
                    }
                    else {
                        $SQL .= "AND ((`event_date_from` BETWEEN '$start_date' AND '$end_date') OR ";
                        $SQL .= "(`event_date_to` BETWEEN '$start_date' AND '$end_date')) ";
                    }

                    if ($ignore_publish) {
                        $SQL .= "AND ('$start_date' BETWEEN `event_publish_from` AND `event_publish_to`) ";
                    }
                    // unset the date filters
                    unset($filter['month']);
                    unset($filter['year']);
                    unset($filter['day']);
                }
            }

            if (isset($filter['country'])) {
                $SQL .= "AND `address_country_code`='".strtoupper($filter['country'])."' ";
            }

            if (isset($filter['zip'])) {
                $SQL .= "AND `address_zip` LIKE '".$filter['zip']."%' ";
            }

            if (isset($filter['city'])) {
                $SQL .= "AND `address_city` LIKE '".$this->app['utils']->utf8_entities($filter['city'])."%' ";
            }

            if (isset($filter['status'])) {
                // filter for the STATUS
                $SQL .= "AND `event_status`='".strtoupper($filter['status'])."' ";
            }
            else {
                // set STATUS to ACTIVE
                $SQL .= "AND `event_status`='ACTIVE' ";
            }

            if (isset($filter['order_by'])) {
                // filter for the ORDER BY statement
                if (strpos($filter['order_by'], ',')) {
                    // order by multiple fields (simple solution)
                    $order_bys = explode(',', $filter['order_by']);
                    $SQL .= "ORDER BY ";
                    $start = true;
                    foreach ($order_bys as $order_by) {
                        if (!$start) $SQL .= ", ";
                        $SQL .= "`".trim($order_by)."`";
                        $start = false;
                    }
                    $SQL .= " ";
                }
                else {
                    // simply order by one field
                    $SQL .= "ORDER BY `".strtolower($filter['order_by'])."` ";
                }
            }
            else {
                // default ORDER BY
                $SQL .= "ORDER BY `event_date_from` ";
            }
            if (isset($filter['order_direction'])) {
                if (in_array(strtoupper($filter['order_direction']), array('ASC', 'DESC'))) {
                    $SQL .= strtoupper($filter['order_direction'])." ";
                }
                else {
                    // invalid value, set default
                    $SQL .= "ASC ";
                    $messages[] = $this->app['translator']->trans("The value for 'order_direction' can be 'ASC' (ascending) or 'DESC' (descending)");
                }
            }

            if (isset($filter['limit'])) {
                if (is_numeric($filter['limit'])) {
                    $SQL .= "LIMIT ".intval($filter['limit']);
                }
                else {
                    // LIMIT must be numeric
                    $messages[] = $this->app['translator']->trans("The 'limit' filter must be a integer value.");
                }
            }


            // execute the filter and get all event IDs
            //$this->app['db']->query("SET NAMES utf8");
            $results = $this->app['db']->fetchAll($SQL);
            $events = array();
            foreach ($results as $result) {
                // loop through the events and get all details
                $events[] = $this->Event->selectEvent($result['event_id']);
            }
            // return the event array or false
            return (!empty($events)) ? $events : false;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
}
