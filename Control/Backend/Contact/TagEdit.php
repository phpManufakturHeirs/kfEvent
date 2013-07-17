<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/propangas24
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Backend\Contact;

use Silex\Application;
use phpManufaktur\Event\Control\Backend\Backend;
use phpManufaktur\Contact\Control\Dialog\Simple\TagEdit as SimpleTagEdit;

class TagEdit extends Backend {

    protected $SimpleTagEdit = null;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->SimpleTagEdit = new SimpleTagEdit($this->app, array(
            'template' => array(
                'namespace' => '@phpManufaktur/Event/Template',
                'message' => 'backend/message.twig',
                'edit' => 'backend/contact.tag.edit.twig'
            ),
            'route' => array(
                'action' => '/admin/event/contact/tag/edit?usage='.self::$usage
            )
        ));
    }

    /**
     * @param number $tag_id
     */
    public function setTagID($tag_id)
    {
        $this->SimpleTagEdit->setTagID($tag_id);
    }

    public function exec()
    {
        $extra = array(
            'usage' => self::$usage,
            'toolbar' => $this->getToolbar('contact_edit')
        );
        return $this->SimpleTagEdit->exec($extra);
    }

}