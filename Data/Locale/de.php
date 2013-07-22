<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

if ('á' != "\xc3\xa1") {
    // the language files must be saved as UTF-8 (without BOM)
    throw new \Exception('The language file ' . __FILE__ . ' is damaged, it must be saved UTF-8 encoded!');
}

return array(
    '- delete field -'
        => '- Feld löschen -',
    '- new extra field -'
        => '- neues Zusatzfeld -',
    '- new group -'
        => '- neue Gruppe -',

    'Add category'
        => 'Kategorie hinzufügen',
    'Add extra field'
        => 'Zusatzfeld hinzufügen',
    'Add group'
        => 'Gruppe hinzufügen',
    'Add tag'
        => 'Markierung hinzufügen',
    'Add title'
        => 'Titel hinzufügen',

    'Contact list'
        => 'Kontakte, Übersicht',
    'Create a new extra field'
        => 'Ein neues Zusatzfeld anlegen',
    'Create a new group'
        => 'Eine neue Gruppe anlegen',

    'Date'
        => 'Datum',
    'Date and Time'
        => 'Datum und Uhrzeit',
    'delete this extra field'
        => 'dieses Zusatzfeld löschen',

    'Field name'
        => 'Bezeichner',
    'Field name (translated)'
        => 'Bezeichner (übersetzt)',
    'Description'
        => 'Beschreibung',
    'Description (translated)'
        => 'Beschreibung (übersetzt)',

    'Event date from'
        => 'Beginn der Veranstaltung',
    'Event date to'
        => 'Ende der Veranstaltung',
    'Extra field'
        => 'Zusatzfeld',
    'Extra fields'
        => 'Zusatzfelder',

    'Field type'
        => 'Feld Typ',
    'Float'
        => 'Dezimalzahl',

    'go back'
        => 'Zurück',
    'Group'
        => 'Gruppe',
    'Group name'
        => 'Gruppen Bezeichner',
    'Group name (translated)'
        => 'Gruppen Bezeichner (übersetzt)',
    'Groups'
        => 'Gruppen',

    'Int'
        => 'Ganzzahl',
    'Integer'
        => 'Ganzzahl',

    'Location Tags'
        => 'Veranstaltungsorte',

    'Name'
        => 'Bezeichner',
    'Name (translated)'
        => 'Bezeichner (übersetzt)',

    'Organizer Tags'
        => 'Veranstalter',

    'Please select at minimum one tag for the %type%.'
        => 'Bitte legen Sie mindestens eine Markierung für %type% fest!',
    'Participant Tags'
        => 'Teilnehmer',

    'Text - 256 characters'
        => 'Text - max. 256 Zeichen',
    'Text - HTML'
        => 'Text - HTML formatiert',
    'Text - plain'
        => 'Text - unformatiert',
    'The group list is empty, please define a group!'
        => 'Es existieren keine Gruppen, bitte legen Sie eine Gruppe an!',
    'The identifier %identifier% already exists!'
        => 'Der Bezeichner %identifier% existiert bereits!',
    'The record with the ID %id% does not exists!'
        => 'Der Datensatz mit der ID %id% existiert nicht!',
    'The record with the ID %id% was successfull deleted.'
        => 'Der Datensatz mit der ID %id% wurde gelöscht.',
    'The record with the ID %id% was successfull inserted.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich eingefügt.',
    'The record with the ID %id% was successfull updated.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich aktualisiert.',
    'Type'
        => 'Typ'

);