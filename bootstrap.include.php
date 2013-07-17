<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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

// scan the /Locale directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Event/Data/Locale');

// scan the /Locale/Custom directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Event/Data/Locale/Custom');

/**
 * Will be called by the iframe embedded within the CMS.
 * Get the base informations of the CMS, autologin the user into kitFramework
 * and execute the protected admin dialog for WinCalc
 *
 * @todo autologin does not check the user
 * @todo improve the checking and setting of roles
 */
$app->get('/event/cms/{cms}', function ($cms) use ($app) {
    // get the CMS info parameters
    $cms = json_decode(base64_decode($cms), true);

    // save them partial into session
    $app['session']->set('CMS_TYPE', $cms['type']);
    $app['session']->set('CMS_VERSION', $cms['version']);
    $app['session']->set('CMS_LOCALE', $cms['locale']);
    $app['session']->set('CMS_USER_NAME', $cms['username']);

    // auto login into the admin area and then exec propangas24
    $secureAreaName = 'admin';
    $user = new User($cms['username'],'', array('ROLE_USER'), true, true, true, true);
    $token = new UsernamePasswordToken($user, null, $secureAreaName, $user->getRoles());
    $app['security']->setToken($token);
    $app['session']->set('_security_'.$secureAreaName, serialize($token) );

    $usage = ($cms['target'] == 'cms') ? $cms['type'] : 'framework';

    // sub request to the starting point of Event
    $subRequest = Request::create('/admin/event/about', 'GET', array('usage' => $usage));
    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
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

$app->match('/admin/event/setup', function() use($app) {
    $Setup = new Setup($app);
    $Setup->exec();
    return "Setup succesfull.";
});