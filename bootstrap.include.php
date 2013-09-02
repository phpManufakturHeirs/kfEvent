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

// not really needed but make error control more easy ...
global $app;

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

// Extra fields List
$app->match('/admin/event/contact/extra/list',
    'phpManufaktur\Event\Control\Backend\Contact\ExtraList::exec');

// Create and edit extra fields
$app->match('/admin/event/contact/extra/edit',
    'phpManufaktur\Event\Control\Backend\Contact\ExtraEdit::exec');
$app->match('/admin/event/contact/extra/edit/id/{type_id}',
    'phpManufaktur\Event\Control\Backend\Contact\ExtraEdit::exec');

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

// Event Group List
$app->match('/admin/event/group/list',
    'phpManufaktur\Event\Control\Backend\GroupList::exec');

// Event Group Edit
$app->match('/admin/event/group/edit',
    'phpManufaktur\Event\Control\Backend\GroupEdit::exec');
$app->match('/admin/event/group/edit/id/{group_id}',
    'phpManufaktur\Event\Control\Backend\GroupEdit::exec');

// Extra Field List
$app->match('/admin/event/extra/field/list',
    'phpManufaktur\Event\Control\Backend\ExtraFieldList::exec');

// Extra Field Edit
$app->match('/admin/event/extra/field/edit',
    'phpManufaktur\Event\Control\Backend\ExtraFieldEdit::exec');
$app->match('/admin/event/extra/field/edit/id/{type_id}',
    'phpManufaktur\Event\Control\Backend\ExtraFieldEdit::exec');

// Create or Edit Event
$app->match('/admin/event/edit',
    'phpManufaktur\Event\Control\Backend\EventEdit::exec');
$app->match('/admin/event/edit/id/{event_id}',
    'phpManufaktur\Event\Control\Backend\EventEdit::exec');

// add image to event
$app->match('/admin/event/image/add/event/{event_id}',
    'phpManufaktur\Event\Control\Backend\EventEdit::addImage');
// delete image from event
$app->match('/admin/event/image/delete/id/{image_id}/event/{event_id}',
    'phpManufaktur\Event\Control\Backend\EventEdit::deleteImage');

// Show the Event List
$app->match('/admin/event/list',
    'phpManufaktur\Event\Control\Backend\EventList::exec');
$app->match('/admin/event/list/page/{page}',
    'phpManufaktur\Event\Control\Backend\EventList::exec');

// Import events from kitEvent
$app->match('/admin/event/import/kitevent',
    'phpManufaktur\Event\Control\Import\kitEvent\kitEvent::start');
$app->match('/admin/event/import/kitevent/start',
    'phpManufaktur\Event\Control\Import\kitEvent\kitEvent::start');
$app->match('/admin/event/import/kitevent/import',
    'phpManufaktur\Event\Control\Import\kitEvent\kitEvent::import');

// rebuild all iCal files
$app->get('/admin/event/ical/rebuild',
    'phpManufaktur\Event\Control\Command\EventICal::ControllerRebuildAllICalFiles');
// rebuild all QR-Code files
$app->get('/admin/event/qrcode/rebuild',
    'phpManufaktur\Event\Control\Command\EventQRCode::ControllerRebuildAllQRCodeFiles');

// kitCommand: event - create the iFrame and execute route /event/action
$app->post('/command/event',
    'phpManufaktur\Event\Control\Command\EventFrame::exec')
->setOption('info', MANUFAKTUR_PATH.'/Event/command.event.json');

// the default action handler for kitCommand: event
$app->get('/event/action',
    'phpManufaktur\Event\Control\Command\Action::exec');

// select the given event id
$app->get('/event/id/{event_id}',
    'phpManufaktur\Event\Control\Command\Event::ControllerSelectID');
// select the given event id and determine the view mode
$app->get('/event/id/{event_id}/view/{view}',
    'phpManufaktur\Event\Control\Command\Event::ControllerSelectID');

// process permanent link for the given event ID
$app->get('/event/perma/id/{event_id}',
    'phpManufaktur\Event\Control\Command\Event::ControllerSelectPermaLinkID');


// download of a ical file, also from the protected area
$app->get('/event/ical/{event_id}',
    'phpManufaktur\Event\Control\Command\EventICal::ControllerGetICalFile');

// download a qr-code, also from the protected area
$app->get('/event/qrcode/{event_id}',
    'phpManufaktur\Event\Control\Command\EventQRCode::ControllerGetQRCodeFile');

$app->get('/event/subscribe/{event_id}',
    'phpManufaktur\Event\Control\Command\Subscribe::exec');
