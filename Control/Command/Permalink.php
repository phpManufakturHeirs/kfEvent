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

class Permalink extends Basic
{

    protected $app = null;
    protected static $permalink_parent_url = null;

    protected function getPermaLinkParent()
    {
        if (file_exists(MANUFAKTUR_PATH.'/Event/event.json')) {
            $config = $this->app['utils']->readJSON(MANUFAKTUR_PATH.'/Event/event.json');
            if (isset($config['permalink_parent_url']) && !empty($config['permalink_parent_url'])) {
                self::$permalink_parent_url = $config['permalink_parent_url'];
                return true;
            }
        }
        return false;
    }

    public function exec(Application $app, $id)
    {
        $this->initParameters($app);

        if ($this->getPermaLinkParent()) {
            $this->setCMSpageURL(self::$permalink_parent_url);
            $this->setRedirectActive(true);
            $this->setRedirectRoute("/event/id/$id");
            return $this->app['twig']->render($this->app['utils']->templateFile(
            '@phpManufaktur/Event/Template',
            'command/message.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'message' => 'message:'.$id,
                'title' => 'title'
            ));
        }
        else {
            throw new \Exception("Please specifiy a parent URL in event.json.");
        }
    }
}
