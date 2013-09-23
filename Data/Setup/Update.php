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

    public function exec(Application $app)
    {
        return $app['translator']->trans('Successfull updated the extension %extension%.',
            array('%extension%' => 'Event'));
    }
}
