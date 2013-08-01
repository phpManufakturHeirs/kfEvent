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
use phpManufaktur\Event\Data\Setup\Setup;
use phpManufaktur\Event\Control\Backend\About;
use phpManufaktur\Event\Control\Backend\ContactList as EventContactList;
use phpManufaktur\Event\Control\Backend\ContactSelect as EventContactSelect;
use phpManufaktur\Event\Control\Backend\ContactPerson as EventContactPerson;
use phpManufaktur\Event\Control\Backend\ContactCompany as EventContactCompany;
use phpManufaktur\Event\Control\Backend\Contact\CategoryList as EventCategoryList;
use phpManufaktur\Event\Control\Backend\Contact\CategoryEdit as EventCategoryEdit;
use phpManufaktur\Event\Control\Backend\Contact\TitleList as EventTitleList;
use phpManufaktur\Event\Control\Backend\Contact\TitleEdit as EventTitleEdit;
use phpManufaktur\Event\Control\Backend\Contact\TagList as EventTagList;
use phpManufaktur\Event\Control\Backend\Contact\TagEdit as EventTagEdit;
use phpManufaktur\Event\Control\Backend\GroupList as EventGroupList;
use phpManufaktur\Event\Control\Backend\GroupEdit as EventGroupEdit;
use phpManufaktur\Event\Control\Backend\ExtraFieldList as EventExtraFieldList;
use phpManufaktur\Event\Control\Backend\ExtraFieldEdit as EventExtraFieldEdit;
use phpManufaktur\Event\Control\Backend\EventEdit;
use phpManufaktur\Event\Control\Backend\EventList;

// scan the /Locale directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Event/Data/Locale');

// scan the /Locale/Custom directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Event/Data/Locale/Custom');

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
$app->get('/admin/event/about', function () use ($app) {
    $About = new About($app);
    return $About->exec();
});

// Contact List
$app->match('/admin/event/contact/list', function() use($app) {
    $ContactList = new EventContactList($app);
    return $ContactList->exec();
});
$app->match('/admin/event/contact/list/page/{page}', function($page) use ($app) {
    $ContactList = new EventContactList($app);
    $ContactList->setCurrentPage($page);
    return $ContactList->exec();
});

// Contact create and edit
$app->match('/admin/event/contact/select', function() use($app) {
    $select = new EventContactSelect($app);
    return $select->exec();
});
$app->match('/admin/event/contact/edit/id/{contact_id}', function($contact_id) use($app) {
    $select = new EventContactSelect($app);
    $select->setContactID($contact_id);
    return $select->exec();
});
$app->match('/admin/event/contact/person/edit', function() use($app) {
    $contact =  new EventContactPerson($app);
    return $contact->exec();
});
$app->match('/admin/event/contact/person/edit/id/{contact_id}', function($contact_id) use($app) {
    $contact =  new EventContactPerson($app);
    $contact->setContactID($contact_id);
    return $contact->exec();
});
$app->match('/admin/event/contact/company/edit', function() use($app) {
    $contact =  new EventContactCompany($app);
    return $contact->exec();
});
$app->match('/admin/event/contact/company/edit/id/{contact_id}', function($contact_id) use($app) {
    $contact =  new EventContactCompany($app);
    $contact->setContactID($contact_id);
    return $contact->exec();
});
$app->match('/admin/event/contact/category/list', function() use($app) {
    $category = new EventCategoryList($app);
    return $category->exec();
});
$app->match('/admin/event/contact/category/edit', function() use($app) {
    $category = new EventCategoryEdit($app);
    return $category->exec();
});
$app->match('/admin/event/contact/category/edit/id/{category_id}', function($category_id) use($app) {
    $category = new EventCategoryEdit($app);
    $category->setCategoryID($category_id);
    return $category->exec();
});
$app->match('/admin/event/contact/title/list', function() use($app) {
    $title = new EventTitleList($app);
    return $title->exec();
});
$app->match('/admin/event/contact/title/edit', function() use($app) {
    $title = new EventTitleEdit($app);
    return $title->exec();
});
$app->match('/admin/event/contact/title/edit/id/{title_id}', function($title_id) use($app) {
    $title = new EventTitleEdit($app);
    $title->setTitleID($title_id);
    return $title->exec();
});
$app->match('/admin/event/contact/tag/list', function() use($app) {
    $tag = new EventTagList($app);
    return $tag->exec();
});
$app->match('/admin/event/contact/tag/edit', function() use($app) {
    $tag = new EventTagEdit($app);
    return $tag->exec();
});
$app->match('/admin/event/contact/tag/edit/id/{tag_id}', function($tag_id) use($app) {
    $tag = new EventTagEdit($app);
    $tag->setTagID($tag_id);
    return $tag->exec();
});

$app->match('/admin/event/group/list', function() use($app) {
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

$app->match('/admin/event/setup', function() use($app) {
    $Setup = new Setup($app);
    $Setup->exec();
    return "Setup succesfull.";
});
