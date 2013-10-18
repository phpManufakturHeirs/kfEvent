<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Data\Setup;

use Silex\Application;

class Update
{

    protected $app = null;

    /**
     * Release 2.0.14
     */
    protected function release_2014()
    {
        if (file_exists(MANUFAKTUR_PATH.'/Event/config.event.json')) {
            $config = $this->app['utils']->readConfiguration(MANUFAKTUR_PATH.'/Event/config.event.json');
            if (!isset($config['rating']['active'])) {
                $config['rating'] = array(
                    'active' => true,
                    'type' => 'small',
                    'length' => 5,
                    'show_rate_info' => false
                    );
                // write the formatted config file to the path
                file_put_contents(MANUFAKTUR_PATH.'/Event/config.event.json', $this->app['utils']->JSONFormat($config));
                $this->app['monolog']->addDebug('Added rating -> active to /Event/config.event.json');
            }
        }
    }

    /**
     * Execute the update for Event
     *
     * @param Application $app
     */
    public function exec(Application $app)
    {
        $this->app = $app;

        // Release 2.0.14
        $this->release_2014();

        return $app['translator']->trans('Successfull updated the extension %extension%.',
            array('%extension%' => 'Event'));
    }
}
