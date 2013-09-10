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
use phpManufaktur\Event\Data\Event\Subscription;

class ConfirmSubscription extends Basic
{

    protected function initParameters(Application $app, $parameter_id=-1)
    {
        // init parent
        parent::initParameters($app, $parameter_id);
    }

    public function exec(Application $app, $guid)
    {
        // init parent
        $this->initParameters($app);

        $config = $app['utils']->readConfiguration(MANUFAKTUR_PATH.'/Event/config.event.json');
        if (!isset($config['permalink']['cms']['url']) || empty($config['permalink']['cms']['url'])) {
            throw new \Exception('Missing the permanent link definition in config.event.json!');
        }

        $this->setMessage('test');
        return $this->createIFrame('/event/message');

        $SubscriptionData = new Subscription($app);
        if (false === ($SubscriptionData->selectGUID($guid))) {

        }
    }
}
