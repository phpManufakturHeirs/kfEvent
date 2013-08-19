<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/Event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Import\kitEvent;

use Silex\Application;
use phpManufaktur\Event\Control\Import\Dialog;
use phpManufaktur\Contact\Data\Import\KeepInTouch\KeepInTouch as KeepInTouchData;
use phpManufaktur\Contact\Control\Contact;

class kitEvent extends Dialog {

    protected static $kit_release = null;
    protected static $import_is_possible = false;
    protected $KeepInTouch = null;
    protected $Contact = null;
    protected static $script_start = null;
    protected static $max_execution_time = 60; // 60 seconds

    /**
     * Initialize the class
     *
     * @see \phpManufaktur\Event\Control\Import\Dialog::initialize()
     */
    protected function initialize(Application $app)
    {
        self::$script_start = microtime(true);
        parent::initialize($app);
        $this->KeepInTouch = new KeepInTouchData($app);
        if ($this->KeepInTouch->existsKIT()) {
            // KIT exists, check the version
            self::$kit_release = $this->KeepInTouch->getKITrelease();
            if (!is_null(self::$kit_release)) {
                if (version_compare(self::$kit_release, '0.72', '>=')) {
                    // check for kitEvent

                }
            }
        }
        $this->Contact = new Contact($app);
        // increase the execution time to 60 seconds
        ini_set('max_execution_time', self::$max_execution_time);
    }

    /**
     * First step to import data from a kitEvent installation
     *
     * @param Application $app
     * @return string rendered dialog
     */
    public function start(Application $app)
    {
        // initialize the class
        $this->initialize($app);

        $records = 0;

        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Event/Template', 'import/start.kitevent.twig'),
            array(
                'message' => $this->getMessage(),
                'records' => $records,
                'import_is_possible' => self::$import_is_possible,
                'kit_release' => self::$kit_release
            ));
    }

    public function execute(Application $app)
    {
        // initialize the class
        $this->initialize($app);
        return 'execute';
    }
}
