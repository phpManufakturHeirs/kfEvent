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
    protected $app = null;

    /**
     * Return a rendered message dialog for kitEvent
     *
     * @param string $message
     * @param array $message_params
     * @param string $title
     * @param array $title_params
     * @param boolean $log_message
     */
    protected function promptMessage($message, $message_params=array(), $title='', $title_params=array(), $log_message=false)
    {
        if ($log_message) {
            // log this message
            $this->app['monolog']->addInfo(strip_tags($this->app['translator']->trans($message, $message_params, 'messages', 'en')));
        }
        return $this->app['twig']->render($this->app['utils']->templateFile(
            '@phpManufaktur/Event/Template',
            'command/message.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'message' => $this->app['translator']->trans($message, $message_params),
                'title' => $this->app['translator']->trans($title, $title_params)
            ));
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

                default:
                    return $this->promptMessage('The mode %mode% is unknown, please check the parameters for the kitCommand!',
                        array('%mode%' => $parameters['mode']), 'kitCommand ~~ event ~~');
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

}
