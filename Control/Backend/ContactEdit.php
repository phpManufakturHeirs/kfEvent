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
use phpManufaktur\Contact\Control\Dialog\Simple\Contact as SimpleContactEdit;

class ContactEdit extends Backend {

    protected $SimpleContactEdit = null;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->SimpleContactEdit = new SimpleContactEdit($this->app, array(
            'template' => array(
                'namespace' => '@phpManufaktur/Event/Template',
                'message' => 'backend/message.twig',
                'contact' => 'backend/contact.edit.twig'
            ),
            'route' => array(
                'action' => '/admin/event/contact/edit?usage='.self::$usage
            )
        ));
    }

    public function setContactID($contact_id)
    {
        $this->SimpleContactEdit->setContactID($contact_id);
    }

    public function exec()
    {
        $extra = array(
            'usage' => self::$usage,
            'toolbar' => $this->getToolbar('contact_edit')
        );
        return $this->SimpleContactEdit->exec($extra);
    }

} // class Backend