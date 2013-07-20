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
use phpManufaktur\Contact\Control\Dialog\Simple\ContactList as SimpleContactList;

class ContactList extends Backend {

    protected $SimpleContactList = null;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $options = array(
            'template' => array(
                'namespace' => '@phpManufaktur/Event/Template',
                'settings' => 'backend/contact.list.json',
                'message' => 'backend/message.twig',
                'list' => 'backend/contact.list.twig'
            ),
            'route' => array(
                'pagination' => '/admin/event/contact/list/page/{page}?order={order}&direction={direction}&usage='.self::$usage,
                'contact' => array(
                    'person' => '/admin/event/contact/person/edit/id/{contact_id}?usage='.self::$usage,
                    'company' => '/admin/event/contact/company/edit/id/{contact_id}?usage='.self::$usage
                )
            )
        );
        $this->SimpleContactList = new SimpleContactList($this->app, $options);
    }

    public function setCurrentPage($page)
    {
        $this->SimpleContactList->setCurrentPage($page);
    }

    public function exec()
    {
        $extra = array(
            'usage' => self::$usage,
            'toolbar' => $this->getToolbar('contact_list')
        );
        return $this->SimpleContactList->exec($extra);
    }

}