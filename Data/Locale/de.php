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

    'About'
        => '?',
    'Add a image'
        => 'Ein Bild hinzufügen',
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

    'by copying from a existing event'
        => 'durch Kopieren einer existierenden Veranstaltung',
    'by selecting a event group'
        => 'durch Auswahl einer Veranstaltungsgruppe',

    'Checking the GUID identifier'
        => 'Überprüfung der GUID Kennung',
    'Contact list'
        => 'Kontakte, Übersicht',
    'Costs'
        => 'Teilnahmegebühr',
    'Create a new contact'
        => 'Einen neuen Kontakt anlegen',
    'Create a new event'
        => 'Eine neue Veranstaltung erstellen',
    'Create a new extra field'
        => 'Ein neues Zusatzfeld anlegen',
    'Create a new group'
        => 'Eine neue Gruppe anlegen',

    // event data columns
    'description_long'
        => 'Beschreibung',
    'description_short'
        => 'Zusammenfassung',
    'description_title'
        => 'Titel',

    'Date'
        => 'Datum',
    'Date and Time'
        => 'Datum und Uhrzeit',
    'Deadline'
        => 'Anmeldeschluß',
    'delete this extra field'
        => 'dieses Zusatzfeld löschen',
    'Delete this image'
        => 'Dieses Bild löschen',
    'Description'
        => 'Beschreibung',
    'Description (translated)'
        => 'Beschreibung (übersetzt)',
    'Detected a kitEvent installation (Release: %release%) with %count% active or locked events.'
        => 'Es wurde eine kitEvent Installation (Release: %release%) mit %count% Veranstaltungen gefunden, die importiert werden können.',

    // event data columns
    'event_costs'
        => 'Kosten',
    'event_date_from'
        => 'Datum von',
    'event_date_to'
        => 'Datum bis',
    'event_deadline'
        => 'Anmeldeschluß',
    'event_id'
        => 'ID',
    'event_participants_max'
        => 'max. Tln.',
    'event_participants_total'
        => 'Anmeldungen',
    'event_publish_from'
        => 'Veröffentlichen ab',
    'event_publish_to'
        => 'Veröffentlichen bis',
    'event_status'
        => 'Status',
    'event_timestamp'
        => 'Zeitstempel',

    'Event'
        => 'Veranstaltung',
    'Event date from'
        => 'Beginn der Veranstaltung',
    'Event date to'
        => 'Ende der Veranstaltung',
    'Extra field'
        => 'Zusatzfeld',
    'Extra fields'
        => 'Zusatzfelder',
    'Event list'
        => 'Veranstaltungen, Übersicht',
    'Event location'
        => 'Veranstaltungsort',

    'Field name'
        => 'Bezeichner',
    'Field name (translated)'
        => 'Bezeichner (übersetzt)',
    'Field type'
        => 'Feld Typ',
    'Float'
        => 'Dezimalzahl',
    'free of charge'
        => 'kostenlos',

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

    'Import events from kitEvent'
        => 'Veranstaltungen aus kitEvent importieren',
    'Information about the Event extension'
        => 'Informationen über die Event Extension',
    'Int'
        => 'Ganzzahl',
    'Integer'
        => 'Ganzzahl',

    'List of all active events'
        => 'Übersicht über alle aktiven Veranstaltungen',
    'List of all available contacts (Organizer, Locations, Participants)'
        => 'Übersicht über alle verfügbaren Kontakte (Veranstalter, Orte, Teilnehmer)',
    'List of all available event groups'
        => 'Übersicht über alle verfügbaren Veranstaltungsgruppen',
    'List of all registrations for events'
        => 'Übersicht über alle Anmeldungen zu Veranstaltungen',
    'Location'
        => 'Veranstaltungsort',
    'Location Tags'
        => 'Veranstaltungsorte',
    'Locations'
        => 'Veranstaltungsorte',
    'Long description'
        => 'Langbeschreibung',

    'Name'
        => 'Bezeichner',
    'Name (translated)'
        => 'Bezeichner (übersetzt)',

    'Organizer'
        => 'Veranstalter',
    'Organizer Tags'
        => 'Veranstalter',

    'Please select at minimum one tag for the %type%.'
        => 'Bitte legen Sie mindestens eine Markierung für %type% fest!',
    'Participant'
        => 'Teilnehmer',
    'Participant Tags'
        => 'Teilnehmer',
    'Participants'
        => 'Teilnehmer',
    'Participants maximum'
        => 'Teilnehmer, max. Anzahl',
    'Participants total'
        => 'Teilnehmer, angemeldet',
    'Pictures'
        => 'Bilder',
    'Please define a permanent link in config.event.json. Without this link Event can not create permanent links or respond to user requests.'
        => 'Bitte definieren Sie einen permanenten Link in der config.event.json. Ohne diesen Link kann Event keine Verweise auf Veranstaltungen erzeugen oder auf Anfragen von Veranstaltungsteilnehmern reagieren.',
    'Publish from'
        => 'Veröffentlichen ab',
        'Publish to'
            => 'Veröffentlichen bis',

    'Registrations'
        => 'Anmeldungen',

    'Select event group'
        => 'Veranstaltungsgruppe auswählen',
    'Short description'
        => 'Kurzbeschreibung',
    'Show detailed information'
        => 'Detailierte Informationen anzeigen',
    'Skipped kitEvent ID %event_id%: No valid value in %field%'
        => 'kitEvent ID <b>%event_id%</b> übersprungen: Ungültiger Wert in Feld %field%',
    'Start import from kitEvent'
        => 'Import aus kitEvent starten',
    'Subscribe to event'
        => 'Zu der Veranstaltung anmelden',

    'Text - 256 characters'
        => 'Text - max. 256 Zeichen',
    'Text - HTML'
        => 'Text - HTML formatiert',
    'Text - plain'
        => 'Text - unformatiert',
    'Thank you for your subscription. We have send you an email, please use the submitted confirmation link to confirm your email address and to activate your subscription!'
        => 'Vielen Dank für Ihre Anmeldung. Wir haben Ihnen eine E-Mail geschickt, bitte benutzen Sie den enthaltenen Bestätigungslink um Ihre E-Mail Adresse zu bestätigen und die Anmeldung zu aktivieren.',
    'Thank you for your subscription, we have send you a receipt at your email address.'
        => 'Vielen Dank für Ihre Anmeldung, wir haben Ihnen eine Bestätigung an Ihre E-Mail Adresse gesendet.',
    'The contact record was successfull updated.'
        => 'Der Adressdatensatz wurde aktualisiert.',
    'The email address %email% is associated with a company contact record. At the moment you can only subscribe to a event with your personal email address!'
        => 'Die E-Mail Adresse %email% ist einer Firma oder Institution zugeordnet. Zur Zeit können Sie sich jedoch nur mit einer persönlichen E-Mail Adresse zu einer Veranstaltung anmelden.',
    'The event list is empty, please create a event!'
        => 'Es existieren keine aktiven Veranstaltungen, legen Sie eine neue Versanstaltung an.',
    'The field list is empty, please define a extra field!'
        => 'Es wurden noch keine Zusatzfelder definiert, bitte erstellen Sie ein neues Zusatzfeld!',
    'The group list is empty, please define a group!'
        => 'Es existieren keine Gruppen, bitte legen Sie eine Gruppe an!',
    'The identifier %identifier% already exists!'
        => 'Der Bezeichner %identifier% existiert bereits!',
    'The image <b>%image%</b> has been added to the event.'
        => 'Der Veranstaltung wurde das Bild <b>%image%</b> hinzugefügt.',
    'The image with the ID %image_id% was successfull deleted.'
        => 'Das Bild mit der ID %image_id% wurde erfolgreich gelöscht.',
    'The record with the ID %id% does not exists!'
        => 'Der Datensatz mit der ID %id% existiert nicht!',
    'The record with the ID %id% was successfull deleted.'
        => 'Der Datensatz mit der ID %id% wurde gelöscht.',
    'The record with the ID %id% was successfull inserted.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich eingefügt.',
    'The record with the ID %id% was successfull updated.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich aktualisiert.',
    'The status of your address record is actually %status%, so we can not accept your subscription. Please contact the <a href="mailto:%email%">webmaster</a>.'
        => 'Der Status Ihres Adressdatensatz ist zur Zeit auf %status% gesetzt, wir können Ihre Anmeldung daher nicht entgegennehmen. Bitte nehmen Sie Kontakt mit dem <a href="mailto:%email%">Webmaster</a> auf, um die Situation zu klären.',
    'The submitted GUID %guid% does not exists.'
        => 'Die übermittelte GUID %guid% existiert nicht!',
    'The view <b>%view%</b> does not exists!'
        => 'Die Ansicht (view) <b>%view%</b> existiert nicht!',
    'There exists no kitEvent installation at the parent CMS!'
        => 'Es wurde keine kitEvent Installation in dem übergeordeneten Content Management System gefunden!',
    'Type'
        => 'Typ',

    'unlimited'
        => 'unbegrenzt',
    'Using qrcode[] is not enabled in config.event.json!'
        => 'qrcode[] ist nicht in der config.event.json freigegeben!',

    'You have already subscribed to this Event at %datetime%, you can not subscribe again.'
        => 'Sie haben sich am %datetime% bereits zu dieser Veranstaltung angemeldet und können sich deshalb nicht erneut anmelden.',

);
