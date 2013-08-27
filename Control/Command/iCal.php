<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Command;

use Silex\Application;
use phpManufaktur\Event\Data\Event\Event as EventData;
use Carbon\Carbon;

require_once MANUFAKTUR_PATH.'/Event/Control/Include/iCalcreator/iCalcreator.class.php';

class iCal
{
    protected $app = null;

    public function CreateICalFile(Application $app, $event_id)
    {
        try {
            // get the data for the given event ID
            $EventData = new EventData($app);
            if (false === ($event = $EventData->selectEvent($event_id))) {
                throw new \Exception("The event ID $event_id does not exists!");
            }
            // init iCalcreator
            $vCal = new \vcalendar(array(
                'unique_id' => 'kit2.phpmanufaktur.de',
                'language' => $app['translator']->getLocale()
            ));
            $evt = &$vCal->newComponent('vevent');
            $evt->setProperty('class', 'PUBLIC');
            $evt->setProperty('priority', 0);
            $evt->setProperty('status', 'CONFIRMED');
            $evt->setProperty('summary', $event['description_title']);
            $evt->setProperty('description', $event['description_short']);

            // init Carbon
            $Carbon = new Carbon($event['event_date_from']);
            $evt->setProperty('dtstart',$Carbon->year,$Carbon->month,$Carbon->day, $Carbon->hour, $Carbon->minute, $Carbon->second);
            $Carbon->setTimestamp(strtotime($event['event_date_to']));
            $evt->setProperty('dtend',$Carbon->year,$Carbon->month,$Carbon->day, $Carbon->hour, $Carbon->minute, $Carbon->second);

            if ($event['contact']['location']['contact']['contact_type'] == 'COMPANY') {
                $location = $event['contact']['location']['company'][0]['company_name'];
            }
            else {
                $location = $event['contact']['location']['person'][0]['person_last_name'];
            }
            $evt->setProperty('location', $location);
            $ical = $vCal->createCalendar();
echo $ical;
            //list($year, $month, $day, $hour, $minute, $second) = explode('-', date('Y-m-d-H-i-s', strtotime($event['event_date_from'])));
            //$evt->setProperty('dtstart', $year, $month, $day, $hour, $minute, $second);
            //list($year, $month, $day, $hour, $minute, $second) = explode('-', date('Y-m-d-H-i-s', strtotime($event['evt_event_date_to'])));
            //$evt->setProperty('dtend', $year, $month, $day, $hour, $minute, $second);
            //$evt->setProperty('location', $event['item_location']);
            //$ical = $vCal->createCalendar();


        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
