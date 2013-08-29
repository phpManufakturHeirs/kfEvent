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

require_once MANUFAKTUR_PATH.'/Event/Control/Include/phpqrcode/qrlib.php';

class EventQRCode
{
    protected $app = null;
    protected static $config = null;

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

    public function create($event_id)
    {
        if (!isset(self::$config['qrcode']['active']) || !isset(self::$config['qrcode']['framework']['path']['link']) ||
            !isset(self::$config['qrcode']['framework']['path']['ical']) || !isset(self::$config['qrcode']['settings']['content']) ||
            !isset(self::$config['qrcode']['settings']['size']) || !isset(self::$config['qrcode']['settings']['error_correction']) ||
            !isset(self::$config['qrcode']['settings']['margin'])) {
            throw new \Exception("The QR-Code settings in config.event.json are invalid, missing items, please check the configuration.");
        }

        if (!self::$config['qrcode']['active']) {
            // QR-Code is not active
            $this->app['monolog']->addDebug("Skipped QR-Code creation for event ID $event_id");
            return true;
        }

        if (strtolower(self::$config['qrcode']['settings']['content']) == 'ical') {
            // use iCal data as content for the QR-Code
            $this->app['monolog']->addInfo("Start creation of a QR-Code with iCal content for event ID $event_id");
            if (!isset(self::$config['ical']['active']) || !self::$config['ical']['active']) {
                // ical creation must be active!
                throw new \Exception("To create QR-Codes with iCal information as content the iCal creation must also enabled in config.event.json.");
            }
            // create ical filename
            if (!isset(self::$config['ical']['framework']['path']) || empty(self::$config['ical']['framework']['path'])) {
                throw new \Exception("Missing the path to iCal files, please check the config.event.json.");
            }
            $ical_file = FRAMEWORK_PATH.self::$config['ical']['framework']['path']."/$event_id.ics";
            if (!file_exists($ical_file)) {
                throw new \Exception("The iCal file $ical_file does not exists!");
            }
            if (false === ($content = file_get_contents($ical_file))) {
                throw new \Exception("Can't read the iCal file $ical_file.");
            }
            $qrcode_path = FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['ical'];
        }
        else {
            // use a permalink as content for the QR-Code
            if (!isset(self::$config['permalink']['cms']['url']) || empty(self::$config['permalink']['cms']['url'])) {
                throw new \Exception("To create a QR-Code with a permalink, the permalink target must be defined in the config.event.json.");
            }
            $content = FRAMEWORK_URL."/event/id/$event_id";
            $qrcode_path = FRAMEWORK_PATH.self::$config['qrcode']['framework']['path']['link'];
        }

        // check the directory
        if (!$this->app['filesystem']->exists($qrcode_path)) {
            $this->app['filesystem']->mkdir($qrcode_path);
        }
        $qrcode_file = $qrcode_path."/$event_id.png";

        // get the settings
        $error_correction = self::$config['qrcode']['settings']['error_correction'];
        $size = self::$config['qrcode']['settings']['size'];
        $margin = self::$config['qrcode']['settings']['margin'];

        $QRCode = new \QRcode();
        $QRCode->png($content, $qrcode_file, $error_correction, $size, $margin);

        $this->app['monolog']->addInfo("QR-Code for event ID $event_id successfull created.");
    }
}
