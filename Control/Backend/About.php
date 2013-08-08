<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/propangas24
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Backend;

use phpManufaktur\Event\Control\Backend\Backend;
use Silex\Application;

class About extends Backend {

    /**
     * Show the about dialog for Event
     *
     * @return string rendered dialog
     */
    public function exec(Application $app)
    {
        $this->initialize($app);
        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Event/Template', 'backend/about.twig'),
            array(
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('about')
            ));
    }

}