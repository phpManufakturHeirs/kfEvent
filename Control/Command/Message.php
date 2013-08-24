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

use phpManufaktur\Basic\Control\kitCommand\Basic;

class Message extends Basic
{

    /**
     * Return a rendered message dialog for kitEvent
     *
     * @param string $message
     * @param array $message_params
     * @param string $title
     * @param array $title_params
     * @param boolean $log_message
     */
    public function render($message, $message_params=array(), $title='kitCommand ~~ event ~~', $title_params=array(), $log_message=false)
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

}
