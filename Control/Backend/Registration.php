<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Backend;

use phpManufaktur\Event\Control\Backend\Backend;
use Silex\Application;

class Registration extends Backend {

    /**
     * Show the about dialog for Event
     *
     * @return string rendered dialog
     */
    public function controllerList(Application $app)
    {
        $this->initialize($app);
        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template', 'backend/registration.list.twig'),
            array(
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('registration')
            ));
    }

}
