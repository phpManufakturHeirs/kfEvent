<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

use phpManufaktur\Basic\Control\CMS\EmbeddedAdministration;
use phpManufaktur\Event\Control\Backend\GroupList as EventGroupList;
use phpManufaktur\Event\Control\Backend\GroupEdit as EventGroupEdit;
use phpManufaktur\Event\Control\Backend\ExtraFieldList as EventExtraFieldList;
use phpManufaktur\Event\Control\Backend\ExtraFieldEdit as EventExtraFieldEdit;
use phpManufaktur\Event\Control\Backend\EventEdit;

// scan the /Locale directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Event/Data/Locale');
// scan the /Locale/Custom directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Event/Data/Locale/Custom');

// setup routine for kfEvent
$app->match('/admin/event/setup', 'phpManufaktur\Event\Data\Setup\Setup::exec');

/**
 * Use the EmbeddedAdministration feature to connect the extension with the CMS
 *
 * @link https://github.com/phpManufaktur/kitFramework/wiki/Extensions-%23-Embedded-Administration
 */
$app->get('/event/cms/{cms_information}', function ($cms_information) use ($app) {
    $administration = new EmbeddedAdministration($app);
    return $administration->route('/admin/event/about', $cms_information);
});

// About dialog
$app->get('/admin/event/about', 
    'phpManufaktur\Event\Control\Backend\About::exec');

// Contact List
$app->match('/admin/event/contact/list', 
    'phpManufaktur\Event\Control\Backend\ContactList::exec');
$app->match('/admin/event/contact/list/page/{page}', 
    'phpManufaktur\Event\Control\Backend\ContactList::exec');

// Contact create and edit
$app->match('/admin/event/contact/select', 
    'phpManufaktur\Event\Control\Backend\ContactSelect::exec');
$app->match('/admin/event/contact/edit/id/{contact_id}', 
    'phpManufaktur\Event\Control\Backend\ContactSelect::exec');

// Create and Edit Person contacts
$app->match('/admin/event/contact/person/edit', 
    'phpManufaktur\Event\Control\Backend\ContactPerson::exec');
$app->match('/admin/event/contact/person/edit/id/{contact_id}', 
    'phpManufaktur\Event\Control\Backend\ContactPerson::exec');

// Create and Edit Company contacts
$app->match('/admin/event/contact/company/edit', 
    'phpManufaktur\Event\Control\Backend\ContactCompany::exec');
$app->match('/admin/event/contact/company/edit/id/{contact_id}', 
    'phpManufaktur\Event\Control\Backend\ContactCompany::exec');

// Category List
$app->match('/admin/event/contact/category/list', 
    'phpManufaktur\Event\Control\Backend\Contact\CategoryList::exec');

// Category Edit
$app->match('/admin/event/contact/category/edit', 
    'phpManufaktur\Event\Control\Backend\Contact\CategoryEdit::exec');
$app->match('/admin/event/contact/category/edit/id/{category_id}', 
    'phpManufaktur\Event\Control\Backend\Contact\CategoryEdit::exec');

// Title List
$app->match('/admin/event/contact/title/list', 
    'phpManufaktur\Event\Control\Backend\Contact\TitleList::exec');

// Title Edit
$app->match('/admin/event/contact/title/edit', 
    'phpManufaktur\Event\Control\Backend\Contact\TitleEdit::exec');
$app->match('/admin/event/contact/title/edit/id/{title_id}', 
    'phpManufaktur\Event\Control\Backend\Contact\TitleEdit::exec');

// Tag List
$app->match('/admin/event/contact/tag/list', 
    'phpManufaktur\Event\Control\Backend\Contact\TagList::exec');

// Tag Edit
$app->match('/admin/event/contact/tag/edit', 
    'phpManufaktur\Event\Control\Backend\Contact\TagEdit::exec');
$app->match('/admin/event/contact/tag/edit/id/{tag_id}', 
    'phpManufaktur\Event\Control\Backend\Contact\TagEdit::exec');

$app->match('/admin/event/group/list', 
    
    function() use($app) {
    $group = new EventGroupList($app);
    return $group->exec();
});
$app->match('/admin/event/group/edit', function() use($app) {
    $group = new EventGroupEdit($app);
    return $group->exec();
});
$app->match('/admin/event/group/edit/id/{group_id}', function($group_id) use($app) {
    $group = new EventGroupEdit($app);
    $group->setGroupID($group_id);
    return $group->exec();
});

$app->match('/admin/event/extra/field/list', function() use($app) {
    $field = new EventExtraFieldList($app);
    return $field->exec();
});
$app->match('/admin/event/extra/field/edit', function() use($app) {
    $field = new EventExtraFieldEdit($app);
    return $field->exec();
});
$app->match('/admin/event/extra/field/edit/id/{type_id}', function($type_id) use($app) {
    $field = new EventExtraFieldEdit($app);
    $field->setTypeID($type_id);
    return $field->exec();
});

$app->match('/admin/event/edit', function() use($app) {
    $event = new EventEdit($app);
    return $event->exec();
});

$app->match('/admin/event/list', function() use($app) {

});


