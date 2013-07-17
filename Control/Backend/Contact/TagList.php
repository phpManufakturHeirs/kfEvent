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
use phpManufaktur\Contact\Control\Dialog\Simple\TagList as SimpleTagList;

class TagList extends Backend {

    protected $SimpleTagList = null;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->SimpleTagList = new SimpleTagList($this->app, array(
            'template' => array(
                'namespace' => '@phpManufaktur/Event/Template',
                'message' => 'backend/message.twig',
                'list' => 'backend/contact.tag.list.twig'
            ),
            'route' => array(
                'edit' => '/admin/event/contact/tag/edit/id/{tag_id}?usage='.self::$usage
            )
        ));
    }

    public function exec()
    {
        $extra = array(
            'usage' => self::$usage,
            'toolbar' => $this->getToolbar('contact_edit')
        );
        return $this->SimpleTagList->exec($extra);
    }

}