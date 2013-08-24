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
use phpManufaktur\Basic\Control\kitCommand\Basic;

class Action extends Basic
{
    protected $Message = null;
    protected $Event = null;

    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);
        $this->Message = new Message($app);
        $this->Event = new Event($app);
    }

    /**
     * Action handler for the kitCommand ~~ event ~~
     *
     * @param Application $app
     * @throws \Exception
     * @return string dialog or result
     */
    public function exec(Application $app)
    {
        try {
            $this->initParameters($app);

            // get the kitCommand parameters
            $parameters = $this->getCommandParameters();

            if (!isset($parameters['mode'])) {
                // there is no 'mode' parameter set, so we show the "Welcome" page
                return $this->createIFrame('/basic/help/event/welcome');
            }

            switch ($parameters['mode']) {
                case 'event':
                    return $this->Event->exec();
                default:
                    return $this->Message->render('The mode <b>%mode%</b> is unknown, please check the parameters for the kitCommand!',
                        array('%mode%' => $parameters['mode']));
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

}
