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

use Silex\Application;
use phpManufaktur\Event\Control\Backend\Backend;
use phpManufaktur\Contact\Control\Dialog\Simple\ContactPerson as SimpleContactPerson;

class ContactPerson extends Backend {

    protected $SimpleContactPerson = null;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->SimpleContactPerson = new SimpleContactPerson($this->app, array(
            'template' => array(
                'namespace' => '@phpManufaktur/Event/Template',
                'message' => 'backend/message.twig',
                'contact' => 'backend/contact.person.twig'
            ),
            'route' => array(
                'action' => '/admin/event/contact/person/edit?usage='.self::$usage,
                'category' => '/admin/event/contact/category/list?usage='.self::$usage,
                'title' => '/admin/event/contact/title/list?usage='.self::$usage,
                'tag' => '/admin/event/contact/tag/list?usage='.self::$usage
            )
        ));
    }

    public function setContactID($contact_id)
    {
        $this->SimpleContactPerson->setContactID($contact_id);
    }

    public function exec()
    {
        $extra = array(
            'usage' => self::$usage,
            'toolbar' => $this->getToolbar('contact_edit')
        );
        return $this->SimpleContactPerson->exec($extra);
    }

}