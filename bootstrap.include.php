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
use phpManufaktur\Event\Control\Backend\ContactEdit as EventContactEdit;

// scan the /Locale directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Event/Data/Locale');

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
$app->match('/admin/event/contact/edit', function() use($app) {
    $ContactEdit = new EventContactEdit($app);
    return $ContactEdit->exec();
});
$app->match('/admin/event/contact/edit/id/{contact_id}', function($contact_id) use($app) {
    $ContactEdit = new EventContactEdit($app);
    $ContactEdit->setContactID($contact_id);
    return $ContactEdit->exec();
});

$app->match('/admin/event/setup', function() use($app) {
    $Setup = new Setup($app);
    $Setup->exec();
    return "Setup succesfull.";
});