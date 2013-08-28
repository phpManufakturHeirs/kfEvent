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
use Carbon\Carbon;
use phpManufaktur\Event\Data\Event\Event as EventData;

require_once MANUFAKTUR_PATH.'/Event/Control/Include/iCalcreator/iCalcreator.class.php';

class iCal
{
    protected $app = null;
    protected static $config = null;
    protected $EventData = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        // get the configuration
        self::$config = $this->app['utils']->readConfiguration(MANUFAKTUR_PATH.'/Event/config.event.json');
    }

    /**
     * Create a iCal file from the given event.
     * Uses the settings in /Event/config.event.json
     *
     * @param array $event
     * @throws \Exception
     * @return boolean
     */
    public function CreateICalFile(EventData $EventData, $event_id)
    {
        try {
            if (!self::$config['ical']['active']) {
                // iCal is not active
                $this->app['monolog']->addDebug("Skipped iCal creation for event ID $event_id");
                return true;
            }
            // get the event
            $event = $EventData->selectEvent($event_id);

            // init iCalcreator
            $vCal = new \vcalendar(array(
                'unique_id' => 'kit2.phpmanufaktur.de',
                'language' => $this->app['translator']->getLocale()
            ));
            $evt = &$vCal->newComponent('vevent');
            $evt->setProperty('class', 'PUBLIC');
            $evt->setProperty('priority', 0);
            $evt->setProperty('status', 'CONFIRMED');
            $evt->setProperty('summary', $event['description_title']);

            // no html tags, convert all entities
            $description = html_entity_decode(strip_tags($event['description_short']));
            $evt->setProperty('description', $description);

            // init Carbon
            $Date = new Carbon($event['event_date_from']);
            $evt->setProperty('dtstart',$Date->year,$Date->month,$Date->day, $Date->hour, $Date->minute, $Date->second);
            $Date->setTimestamp(strtotime($event['event_date_to']));
            $evt->setProperty('dtend',$Date->year,$Date->month,$Date->day, $Date->hour, $Date->minute, $Date->second);

            // set location
            if ($event['contact']['location']['contact']['contact_type'] == 'COMPANY') {
                $location = $event['contact']['location']['company'][0]['company_name'];
            }
            else {
                $location = $event['contact']['location']['person'][0]['person_last_name'];
            }
            $evt->setProperty('location', $location);

            // create the calender
            $ical_data = $vCal->createCalendar();

            // check directory
            $path = FRAMEWORK_PATH.self::$config['ical']['framework']['path'];
            if (!$this->app['filesystem']->exists($path)) {
                $this->app['filesystem']->mkdir($path);
            }
            // create ical file
            $ical_file = sprintf('%s/%d.ics', $path, $event['event_id']);
            if (!file_put_contents($ical_file, $ical_data)) {
                throw new \Exception("Can't create the file $ical_file.");
            }

            // add a log entry
            $this->app['monolog']->addInfo("Created $ical_file");

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
