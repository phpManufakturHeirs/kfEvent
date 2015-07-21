<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.info>
 * @link http://www.phpmanufaktur.info/de/kitframework/erweiterungen/event.php
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Command;

use Silex\Application;
use phpManufaktur\Basic\Control\kitCommand\Basic;

class EventFrame extends Basic
{

    public function exec(Application $app)
    {
        $this->initParameters($app);
        return $this->createIFrame('/event/action');
    }
}
