<?php

/**
 * kitFramework::Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 *
 * This file was created by the kitFramework i18nEditor
 */

if ('á' != "\xc3\xa1") {
    // the language files must be saved as UTF-8 (without BOM)
    throw new \Exception('The language file ' . __FILE__ . ' is damaged, it must be saved UTF-8 encoded!');
}

return array(
  ' but not at %dates%.'
    => ', jedoch nicht am %dates%.',
  '- delete field -'
    => '- Feld löschen -',
  '- new extra field -'
    => '- neues Zusatzfeld -',
  '- new group -'
    => '- neue Gruppe -',
  'About'
    => '?',
  'Account type'
    => 'Benutzerkonto, Typ',
  'Activate the desired account role'
    => 'Das beantragte Benutzerrecht aktivieren',
  'Add a image'
    => 'Ein Bild hinzufügen',
  'Add a new group'
    => 'Eine neue Gruppe hinzufügen',
  'Add a subscription'
    => 'Eine Anmeldung hinzufügen',
  'Admin Status'
    => 'Administrator Status',
  'At day x of month'
    => 'Jeden x. Tag des Monat',
  'At day x of month must be greater than zero and less than 28.'
    => 'Der Wert für Ausführen am x. Tag des Monats muss größer als Null und kleiner als 28 sein.',
  'At least we need one communication channel, so please tell us a email address, phone or a URL'
    => 'Wir benötigen mindestens einen Kommunikationsweg, bitte nennen Sie uns eine E-Mail Adresse, Telefonummer oder die URL der Homepage.',
  'At month'
    => 'Im Monat',
  'At the first'
    => 'Am ersten',
  'At the first and third'
    => 'Am ersten und dritten',
  'At the fourth'
    => 'Am vierten',
  'At the last'
    => 'Am letzten',
  'At the moment there are no proposed events'
    => 'Momentan liegen keine Veranstaltungsvorschläge vor.',
  'At the moment there are no subscriptions for your events'
    => 'Momentan liegen keine Anmeldungen zu Ihren Veranstaltungen vor.',
  'At the second'
    => 'Am zweiten',
  'At the second and fourth'
    => 'Am zweiten und vierten',
  'At the second and last'
    => 'Am zweiten und letzten',
  'At the third'
    => 'Am dritten',
  'CHOICE_ADMIN_ACCOUNT'
    => 'Ich möchte als Administrator alle Veranstaltungen bearbeiten können',
  'CHOICE_LOCATION_ACCOUNT'
    => 'Ich vertrete einen Veranstaltungsort und möchte Veranstaltungen bearbeiten können, die dort stattfinden',
  'CHOICE_ORGANIZER_ACCOUNT'
    => 'Ich vertrete einen Veranstalter, Verein oder eine Organisation und möchte deren Veranstaltungen bearbeiten können',
  'CHOICE_SUBMITTER_ACCOUNT'
    => 'Ich möchte Veranstaltungen bearbeiten können, die ich übermittelt habe',
  'Change Event configuration'
    => 'Event Konfiguration ändern',
  'Change account rights'
    => 'Änderung der Benutzerrechte',
  'Checking the GUID identifier'
    => 'Überprüfung der GUID Kennung',
  'Click to subscribe'
    => 'Anklicken um sich zu dieser Veranstaltung anzumelden',
  'Comments handling'
    => 'Kommentar Behandlung',
  'Costs'
    => 'Teilnahmegebühr',
  'Create a new Location record'
    => 'Einen neuen Veranstaltungsort anlegen',
  'Create a new Organizer record'
    => 'Eine neue Veranstalter Adresse anlegen',
  'Create a new category'
    => 'Eine neue Kategorie anlegen',
  'Create a new event'
    => 'Eine neue Veranstaltung erstellen',
  'Create a new extra field'
    => 'Ein neues Zusatzfeld anlegen',
  'Create a new recurring event with the ID %event_id%'
    => 'Neue sich wiederholende Veranstaltung mit der ID %event_id% angelegt.',
  'Create a new title'
    => 'Eine neue Schlagzeile anlegen',
  'Created the tag %tag% in Contact.'
    => 'Das Schlagwort %tag% wurde in Contact angelegt.',
  'Daily recurring'
    => 'Tägliche Wiederholung',
  'Date'
    => 'Datum',
  'Date and Time'
    => 'Datum und Uhrzeit',
  'Deadline'
    => 'Anmeldeschluß',
  'Delete this image'
    => 'Dieses Bild löschen',
  'Description long'
    => 'Beschreibung',
  'Description short'
    => 'Zusammenfassung',
  'Description title'
    => 'Titel',
  'Detected a kitEvent installation (Release: %release%) with %count% active or locked events.'
    => 'Es wurde eine kitEvent Installation (Release: %release%) mit %count% Veranstaltungen gefunden, die importiert werden können.',
  'Do not know how to handle the recurring type <b>%type%</b>.'
    => 'Weiß nicht, wie der Wiederholungstyp <strong>%type%</strong> zu handhaben ist.',
  'Do not publish the event'
    => 'Veranstaltung nicht veröffentlichen',
  'Don\'t know how to handle the month type %type%'
    => 'Unbekannter Monatstyp %type%',
  'Don\'t know how to handle the recurring type %type%.'
    => 'Unbekannter Wiederholungstyp %type%.',
  'Don\'t know how to handle the year type %type%'
    => 'Weiß nicht, wie ich den Jahrestyp %type% behandeln soll!',
  'Edit event'
    => 'Veranstaltung bearbeiten',
  'Event'
    => 'Veranstaltung',
  'Event Administration - About'
    => 'Event Verwaltung - Über',
  'Event Administration - Categories list'
    => 'Event Verwaltung - Kategorien Liste',
  'Event Administration - Contact list'
    => 'Event Administration - Kontaktliste',
  'Event Administration - Copy Event'
    => 'Event Administration - Veranstaltung kopieren',
  'Event Administration - Create or edit event'
    => 'Event Administration - Veranstaltung erstellen oder bearbeiten',
  'Event Administration - Edit Contact'
    => 'Event Verwaltung - Kontakt bearbeiten',
  'Event ID'
    => 'ID',
  'Event Title'
    => 'Titel der Veranstaltung',
  'Event costs'
    => 'Eintrittspreis',
  'Event date from'
    => 'Beginn der Veranstaltung',
  'Event date to'
    => 'Ende der Veranstaltung',
  'Event deadline'
    => 'Anmeldeschluß',
  'Event group'
    => 'Gruppe',
  'Event id'
    => 'Veranstaltung ID',
  'Event list'
    => 'Veranstaltungen, Übersicht',
  'Event location'
    => 'Veranstaltungsort',
  'Event management suite for freelancers and organizers'
    => 'Veranstaltungs Verwaltung und Organisation',
  'Event participants confirmed'
    => 'Tln. best.',
  'Event participants max'
    => 'max. Tln.',
  'Event publish from'
    => 'Veröffentlichen ab',
  'Event publish to'
    => 'Veröffentlichen bis',
  'Event status'
    => 'Status',
  'Event successfull updated'
    => 'Die Veranstaltung wurde aktualisiert',
  'Event url'
    => 'Veranstaltungs URL',
  'Exclude dates'
    => 'Daten ausschließen',
  'Extra field'
    => 'Zusatzfeld',
  'Float'
    => 'Dezimalzahl',
  'Google Map'
    => 'Google Map',
  'Group'
    => 'Gruppe',
  'Group description'
    => 'Beschreibung',
  'Group extra fields'
    => 'Zusatzfelder',
  'Group id'
    => 'ID',
  'Group name'
    => 'Gruppen Bezeichner',
  'Group name (translated)'
    => 'Gruppen Bezeichner (übersetzt)',
  'Group status'
    => 'Status',
  'Groups'
    => 'Gruppen',
  'Hello %name%'
    => 'Hallo %name%',
  'I accept the <a href="%url%" target="_blank">general terms and conditions</a>'
    => 'Ich akzeptiere die <a href="%url%" target="_blank">AGB</a>',
  'I really don\'t know the Organizer'
    => 'Der Veranstalter ist mir leider nicht bekannt',
  'If you are prompted to login, please use your username and password'
    => 'Wenn Sie aufgefordert werden sich anzumelden, verwenden Sie bitte Ihren Benutzernamen und Ihr Passwort',
  'Ignore existing comments'
    => 'Existierende Kommentare werden nicht übernommen',
  'Import events from kitEvent'
    => 'Veranstaltungen aus kitEvent importieren',
  'Information about the Event extension'
    => 'Informationen über die Event Extension',
  'Integer'
    => 'Ganzzahl',
  'Invalid key => value pair in the set[] parameter!'
    => 'Ungültiges Schlüssel => Wert Paar für den set[] Parameter!',
  'Invalid login'
    => 'Ungültiger Login, Benutzername oder Passwort falsch',
  'Invalid submission, please try again'
    => 'Ungültige Übermittlung, bitte versuchen Sie es erneut!',
  'It is not allowed that the event start in the past!'
    => 'Der Veranstaltungsbeginn darf nicht in der Vergangenheit liegen!',
  'List of actual submitted proposes for events'
    => 'Übersicht über die aktuellen Vorschläge zu Veranstaltungen',
  'List of all active events'
    => 'Übersicht über alle aktiven Veranstaltungen',
  'List of all available contacts (Organizer, Locations, Participants)'
    => 'Übersicht über alle verfügbaren Kontakte (Veranstalter, Orte, Teilnehmer)',
  'List of all available event groups'
    => 'Übersicht über alle verfügbaren Veranstaltungsgruppen',
  'List of all subscriptions for events'
    => 'Übersicht über alle Anmeldungen zu Veranstaltungen',
  'List single dates in format <b>%format%</b> separated by comma to exclude them from recurring'
    => 'Schließen Sie einzelne Daten im Format <b>%format%</b> durch ein Komma getrennt von der Sequenz aus.',
  'Location'
    => 'Veranstaltungsort',
  'Location ID'
    => 'Veranstaltungsort ID',
  'Location Tags'
    => 'Veranstaltungsorte',
  'Long description'
    => 'Langbeschreibung',
  'Message from the kitFramework Event application'
    => 'Mitteilung von kitFramework Event',
  'Missing a valid Event ID!'
    => 'Vermisse eine gültige Veranstaltungs ID!',
  'Monthly recurring'
    => 'Monatliche Wiederholung',
  'New password'
    => 'Neues Passwort',
  'Next event dates'
    => 'Die nächsten Veranstaltungstermine',
  'No recurring event'
    => 'Keinen Serientermin festlegen',
  'No recurring event type selected'
    => 'Es wurde kein Serientermin Typ ausgewählt.',
  'No results for this filter!'
    => 'Dieser Filter lieferte kein Ergebnis!',
  'Organizer'
    => 'Veranstalter',
  'Organizer ID'
    => 'Veranstalter ID',
  'Organizer Tags'
    => 'Veranstalter',
  'Participant'
    => 'Teilnehmer',
  'Participant Tags'
    => 'Teilnehmer',
  'Participants canceled'
    => 'Teilnehmer, storniert',
  'Participants confirmed'
    => 'Teilnehmer, bestätigt',
  'Participants maximum'
    => 'Teilnehmer, max. Anzahl',
  'Participants pending'
    => 'Teilnehmer, unbestätigt',
  'Participants total'
    => 'Teilnehmer, angemeldet',
  'Pass comments from parent'
    => 'Kommentare werden aktiv von der ursprünglichen Veranstaltung vererbt',
  'Permalink successfull changed'
    => 'Der Permanent Link wurde erfolgreich geändert',
  'Pictures'
    => 'Bilder',
  'Please check the event data and use one of the following action links'
    => 'Bitte prüfen Sie die Angaben zu der Veranstaltung und verwenden Sie anschließend einen der folgenden Aktions-Links',
  'Please define a permanent link in config.event.json. Without this link Event can not create permanent links or respond to user requests.'
    => 'Bitte definieren Sie einen permanenten Link in der config.event.json. Ohne diesen Link kann Event keine Verweise auf Veranstaltungen erzeugen oder auf Anfragen von Veranstaltungsteilnehmern reagieren.',
  'Please determine the handling for the comments.'
    => 'Bitte legen Sie die Handhabung für die Kommentare fest.',
  'Please feel free to order a account.'
    => 'Fordern Sie ein Benutzerkonto an',
  'Please search for for a location or select the checkbox to create a new one.'
    => 'Bitte suchen Sie nach einem Veranstaltungsort oder haken Sie die Checkbox an um einen neuen Veranstaltungsort anzulegen.',
  'Please search for for a organizer or select the checkbox to create a new one.'
    => 'Bitte suchen Sie nach einem Veranstalter oder haken Sie die Checkbox an um einen neuen Veranstalter anzulegen.',
  'Please search for the contact you want to subscribe to an event or add a new contact, if you are shure that the person does not exists in Contacts.'
    => 'Bitte suchen Sie nach dem Kontakt, den Sie zu einer Veranstaltung anmelden möchten. Fügen Sie einen neuen Kontakt hinzu, falls dieser noch nicht existiert.',
  'Please search for the event you want to copy data from.'
    => 'Bitte suchen Sie nach der Veranstaltung, die Sie kopieren möchten.',
  'Please select action'
    => 'Bitte wählen Sie eine Aktion',
  'Please select at least one weekday!'
    => 'Bitte wählen Sie mindestens einen Wochentag aus!',
  'Please select at minimum one tag for the %type%.'
    => 'Bitte legen Sie mindestens eine Markierung für %type% fest!',
  'Please select the event you want to copy into a new one'
    => 'Wählen Sie die Veranstaltung aus, deren Daten für eine neue Veranstaltung übernommen werden sollen.',
  'Please type in a long description with %minimum% characters at minimum.'
    => 'Bitte geben Sie eine Langbeschreibung mit einer Länge von mindestens %minimum% Zeichen ein.',
  'Please type in a short description with %minimum% characters at minimum.'
    => 'Bitte geben Sie eine Kurzbeschreibung mit einer Länge von mindestens %minimum% Zeichen ein.',
  'Please type in a title with %minimum% characters at minimum.'
    => 'Bitte geben Sie einen Titel mit einer Länge von mindestens %minimum% Zeichen ein.',
  'Please use the parameter set[] to set a configuration value.'
    => 'Bitte verwenden Sie den Paramter set[] um einen Konfigurationswert zu setzen!',
  'Proposed event: %event%'
    => 'Vorgeschlagene Veranstaltung: %event%',
  'Proposes'
    => 'Vorschläge',
  'Publish from'
    => 'Veröffentlichen ab',
  'Publish the event'
    => 'Veranstaltung veröffentlichen',
  'Publish to'
    => 'Veröffentlichen bis',
  'ROLE_EVENT_ADMIN'
    => 'Benutzerrecht: Veranstaltungen als Administrator bearbeiten',
  'ROLE_EVENT_LOCATION'
    => 'Benutzerrecht: Veranstaltungen bearbeiten, die diesem Veranstaltungsort zugewiesen sind',
  'ROLE_EVENT_ORGANIZER'
    => 'Benutzerrecht: Veranstaltungen bearbeiten, die von diesem Veranstalter zugewiesen sind',
  'ROLE_EVENT_SUBMITTER'
    => 'Benutzerrecht: Veranstaltungen bearbeiten, die von diesem Benutzer vorgeschlagen wurden',
  'Received request'
    => 'Anfrage erhalten',
  'Recurring date end'
    => 'Letzte Wiederholung',
  'Recurring event'
    => 'Serientermin',
  'Redirect to the parent event ID!'
    => 'Umgeleitet auf die ursprüngliche Veranstaltungs ID!',
  'Reject the desired account role'
    => 'Das gewünschte Benutzerrecht zurückweisen',
  'Reject this event'
    => 'Veranstaltung ablehnen',
  'Remark'
    => 'Bemerkung',
  'Repeat at workdays'
    => 'An Werktagen wiederholen',
  'Repeat each x-days'
    => 'Alle x-Tage wiederholen',
  'Repeat each x-month'
    => 'Wiederhole jeden x. Monat',
  'Repeat each x-weeks'
    => 'Alle x-Wochen wiederholen',
  'Repeat each x-year'
    => 'Wiederhole jedes x. Jahr',
  'Repeat x-month must be greater than zero and less then 13.'
    => 'Der Wert für Wiederhole jeden x. Monat muß größer als Null und kleiner als 13 sein.',
  'Search Location'
    => 'Veranstaltungsort suchen',
  'Search Organizer'
    => 'Veranstalter suchen',
  'Search event'
    => 'Veranstaltung suchen',
  'Select account type'
    => 'Kontotyp wählen',
  'Select event'
    => 'Veranstaltung auswählen',
  'Select event group'
    => 'Veranstaltungsgruppe auswählen',
  'Select type'
    => 'Typ auswählen',
  'Short description'
    => 'Kurzbeschreibung',
  'Show detailed information'
    => 'Detailierte Informationen anzeigen',
  'Skipped kitEvent ID %event_id%: No valid value in %field%'
    => 'kitEvent ID <b>%event_id%</b> übersprungen: Ungültiger Wert in Feld %field%',
  'Start a new search'
    => 'Eine neue Suche starten',
  'Start import from kitEvent'
    => 'Import aus kitEvent starten',
  'Start search'
    => 'Suche starten',
  'Submit subscription'
    => 'Anmeldung übermitteln',
  'Submitter ID'
    => 'Übermittler ID',
  'Submitter Status'
    => 'Übermittler Status',
  'Subscribe to event'
    => 'Zu der Veranstaltung anmelden',
  'Subscriptions'
    => 'Anmeldungen',
  'Successfull inserted a recurring event'
    => 'Es wurden erfolgreich sich wiederholende Veranstaltungen angelegt.',
  'Text - 256 characters'
    => 'Text - max. 256 Zeichen',
  'Text - HTML'
    => 'Text - HTML formatiert',
  'Text - plain'
    => 'Text - unformatiert',
  'Thank you for proposing the following event'
    => 'Vielen Dank für Ihren Veranstaltungsvorschlag',
  'Thank you for your subscription, we have send you a receipt at your email address.'
    => 'Vielen Dank für Ihre Anmeldung, wir haben Ihnen eine Bestätigung an Ihre E-Mail Adresse gesendet.',
  'Thank you for your subscription. We have send you an email, please use the submitted confirmation link to confirm your email address and to activate your subscription!'
    => 'Vielen Dank für Ihre Anmeldung. Wir haben Ihnen eine E-Mail geschickt, bitte benutzen Sie den enthaltenen Bestätigungslink um Ihre E-Mail Adresse zu bestätigen und die Anmeldung zu aktivieren.',
  'Thank you, one of the admins will approve your request and contact you.'
    => 'Vielen Dank, ein Administrator wird Ihre Anfrage prüfen und sich mit Ihnen in Verbindung setzen.',
  'The action link was successfull executed'
    => 'Der Aktionslink wurde erfolgreich ausgeführt',
  'The change of your account rights is approved by admin'
    => 'Die Änderung Ihrer Benutzerrechte wurde durch den Administrator genehmigt',
  'The change of your account rights is rejected by admin'
    => 'Die Änderung Ihrer Benutzerrechte wurde durch den Administrator abgelehnt',
  'The contact record was successfull updated.'
    => 'Der Adressdatensatz wurde aktualisiert.',
  'The daily sequence must be greater than zero!'
    => 'Die Sequenz der täglichen Wiederholung muss größer als Null sein!',
  'The deadline ends after the event start date!'
    => 'Der Anmeldeschluß liegt nach dem Beginn der Veranstaltung!',
  'The email address %email% is associated with a company contact record. At the moment you can only subscribe to a event with your personal email address!'
    => 'Die E-Mail Adresse %email% ist einer Firma oder Institution zugeordnet. Zur Zeit können Sie sich jedoch nur mit einer persönlichen E-Mail Adresse zu einer Veranstaltung anmelden.',
  'The event [%event_id%] will be repeated at %pattern_type% %pattern_day% of each %pattern_sequence%. month%exclude%'
    => 'Die Veranstaltung [%event_id%] wird am %pattern_type% %pattern_day% jedes %pattern_sequence%. Monat wiederholt%exclude%',
  'The event [%event_id%] will be repeated at each workday%exclude%'
    => 'Die Veranstaltung [%event_id%] wird an jedem Werktag wiederholt%exclude%',
  'The event [%event_id%] will be repeated at the %month_day%. day of each %month_sequence%. month%exclude%'
    => 'Die Veranstaltung [%event_id%] wird am %month_day%. Tag jedes %month_sequence%. Monat wiederholt%exclude%',
  'The event [%event_id%] will be repeated each %day_sequence% day(s)%exclude%'
    => 'Die Veranstaltung [%event_id%] wird jeden %day_sequence%. Tag wiederholt%exclude%',
  'The event [%event_id%] will be repeated each %week_sequence% week(s) at %week_day%%exclude%'
    => 'Die Veranstaltung [%event_id%] wird jede %week_sequence%. Woche am %week_day% wiederholt%exclude%',
  'The event [%event_id%] will be repeated each %year_repeat%. year at %month_day%. %month_name%%exclude%'
    => 'Die Veranstaltung [%event_id%] wird jedes %year_repeat%. Jahr am %month_day%. %month_name% wiederholt%exclude%',
  'The event [%event_id%] will be repeated each %year_repeat%. year at %pattern_type% %pattern_day% of %pattern_month%%exclude%'
    => 'Die Veranstaltung [%event_id%] wird jedes %year_repeat%. Jahr am %pattern_type% %pattern_day% im %pattern_month% wiederholt%exclude%',
  'The event group with the name %group% does not exists!'
    => 'Die Veranstaltungs-Gruppe %group% existiert nicht!',
  'The event list is empty, please create a event!'
    => 'Es existieren keine aktiven Veranstaltungen, legen Sie eine neue Versanstaltung an.',
  'The event start date is behind the event end date!'
    => 'Das Anfangsdatum der Veranstaltung liegt nach dem Enddatum der Veranstaltung!',
  'The event with the title %title% was published.'
    => 'Die Veranstaltung mit der Bezeichnung %title% wurde veröffentlicht.',
  'The event with the title %title% was rejected.'
    => 'Die Veranstaltung mit der Bezeichnung %title% wurde zurückgewiesen.',
  'The group list is empty, please define a group!'
    => 'Es existieren keine Gruppen, bitte legen Sie eine Gruppe an!',
  'The identifier %identifier% already exists!'
    => 'Der Bezeichner %identifier% existiert bereits!',
  'The image <b>%image%</b> has been added to the event.'
    => 'Der Veranstaltung wurde das Bild <b>%image%</b> hinzugefügt.',
  'The image with the ID %image_id% was successfull deleted.'
    => 'Das Bild mit der ID %image_id% wurde erfolgreich gelöscht.',
  'The next recurring events'
    => 'Die nächsten Veranstaltungstermine',
  'The publishing date ends before the event starts, this is not allowed!'
    => 'Der Veröffentlichungszeitraum endet vor dem Beginn der Veranstaltung, dies ist nicht gewünscht!',
  'The recurring events where successfull deleted.'
    => 'Die wiederholten Veranstaltungen wurden erfolgreich gelöscht.',
  'The repeat each x-year sequence must be greater than zero and less than 10!'
    => 'Der Wert für die jährliche Wiederholung muss größer als Null und kleiner als 10 sein!',
  'The status of your address record is actually %status%, so we can not accept your subscription. Please contact the <a href="mailto:%email%">webmaster</a>.'
    => 'Der Status Ihres Adressdatensatz ist zur Zeit auf %status% gesetzt, wir können Ihre Anmeldung daher nicht entgegennehmen. Bitte nehmen Sie Kontakt mit dem <a href="mailto:%email%">Webmaster</a> auf, um die Situation zu klären.',
  'The submitted GUID %guid% does not exists.'
    => 'Die übermittelte GUID %guid% existiert nicht!',
  'The subscription was successfull updated'
    => 'Die Anmeldung wurde erfolgreich aktualisiert.',
  'The user %contact_name% with the ID %contact_id% and the email address %email% has proposed the following event'
    => 'Der Kontakt %contact_name% mit der der ID %contact_id% und der E-Mail Adresse %email% hat die folgende Veranstaltung vorgeschlagen',
  'The user %user% does not exists!'
    => 'Der Benutzer %user% existiert nicht!',
  'The user %user% has already proposed %count% events'
    => 'Der Kontakt %user% hat bereits %count% Veranstaltungen vorgeschlagen',
  'The user %user% has never proposed a event'
    => 'Der Kontakt %user% hat noch nie eine Veranstaltung vorgeschlagen',
  'The user %user% want to get the right'
    => 'Der Benutzer %user% möchte die Berechtigung erhalten',
  'The weekly sequence must be greater than zero!'
    => 'Die Sequenz der wöchentlichen Wiederholung muss größer als Null sein!',
  'There exists no kitEvent installation at the parent CMS!'
    => 'Es wurde keine kitEvent Installation in dem übergeordeneten Content Management System gefunden!',
  'There exists no locations who fits to the search term %search%'
    => 'Es wurde kein Veranstaltungsort gefunden, der zu dem Suchbegriff <i>%search%</i> passt.',
  'There exists no organizer who fits to the search term %search%'
    => 'Es wurde kein Veranstalter gefunden, der zu dem Suchbegriff <i>%search%</i> passt.',
  'This activation link was already used and is no longer valid!'
    => 'Dieser Aktivierungslink wurde bereits verwendet und ist nicht mehr gültig!',
  'This event was copied from the event with the ID %id%. Be aware that you should change the dates before publishing to avoid duplicate events!'
    => 'Diese Veranstaltung ist eine Kopie der Veranstaltung mit der ID %id%. Bitte beachten Sie, daß Sie die Datumsangaben anpassen bevor Sie diese Veranstaltung veröffentlichen - Sie erzeugen sonst doppelte Einträge!',
  'This extra field is used in the event group %group%. First remove the extra field from the event group.'
    => 'Dieses Zusatzfeld wird in der Veranstaltungs Gruppe %group% verwendet. Sie müssen das Zusatzfeld zunächst aus der Gruppe entfernen.',
  'This user has a account but was never in contact in context with events'
    => 'Dieser Benutzer verfügt über ein Benutzerkonto, war jedoch im Zusammenhang mit Veranstaltungen noch nie im Kontakt',
  'This user has a contact record but was never in contact in context with events'
    => 'Dieser Benutzer verfügt über einen Kontaktdatensatz, war jedoch im Zusammenhang mit Veranstaltungen noch nie im Kontakt',
  'Type'
    => 'Typ',
  'Using qrcode[] is not enabled in config.event.json!'
    => 'qrcode[] ist nicht in der config.event.json freigegeben!',
  'We have send you a new password, please check your email account'
    => 'Wir haben Ihnen ein neues Passwort an Ihre E-Mail Adresse gesendet, bitte prüfen Sie Ihren Posteingang',
  'We send you a new password to your email address.'
    => 'Wir senden Ihnen ein neues Passwort an Ihre E-Mail Adresse.',
  'Weekly recurring'
    => 'Wöchentliche Wiederholung',
  'Yearly recurring'
    => 'Jährliche Wiederholung',
  'You have already subscribed to this Event at %datetime%, you can not subscribe again.'
    => 'Sie haben sich am %datetime% bereits zu dieser Veranstaltung angemeldet und können sich deshalb nicht erneut anmelden.',
  'You have now the additional right to: "%role%'
    => 'Sie verfügen jetzt über das zusätzliche Recht: "%role%',
  'You have selected <i>Company, Institution or Association</i> as contact type, so please give us the name'
    => 'Sie haben <i>Firma, Institution oder Verein</i> als Kontakt Typ angegeben, bitte nennen Sie uns den Namen der Einrichtung.',
  'You have selected <i>natural person</i> as contact type, so please give us the last name of the person.'
    => 'Sie haben <i>natürliche Person</i> als Kontakt Typ gewählt, bitte nennen Sie uns den Nachnamen der Person.',
  'You need a account if you want to edit events. In general we will give accounts to all event organizers, locations and persons which submit events frequently.'
    => 'Sie benötigen ein Benutzerkonto um Veranstaltungen ändern und ergänzen zu können. Im Allgemeinen erhalten alle Veranstalter, Veranstaltungsorte sowie Personen, die regelmäßig Veranstaltungen eintragen, einen Zugang von uns.',
  'Your contact record is locked, so we can not perform any action. Please contact the administrator'
    => 'Ihr Kontakt Datensatz ist gesperrt, wir können keine Aktion durchführen. Bitte wenden Sie sich an den Administrator.',
  'Your subscription for the event %event% is already confirmed.'
    => 'Ihre Anmeldung für die Veranstaltung %event% wurde bereits bestätigt.',
  'by copying from a existing event'
    => 'durch Kopieren einer existierenden Veranstaltung',
  'by selecting a event group'
    => 'durch Auswahl einer Veranstaltungsgruppe',
  'closed'
    => 'geschlossen',
  'company, institution or association'
    => 'Firma, Institution oder Verein',
  'description_title'
    => 'Schlagzeile',
  'email'
    => 'E-Mail',
  'email usage'
    => 'Verwendung',
  'event'
    => 'Veranstaltung',
  'event_date_to'
    => 'Datum, bis',
  'free of charge'
    => 'kostenlos',
  'location'
    => 'Veranstaltungsort',
  'natural person'
    => 'Natürliche Person',
  'personal email address'
    => 'persönliche E-Mail Adresse',
  'regular email address of a company, institution or association'
    => 'offizielle E-Mail Adresse einer Firma, Einrichtung oder eines Verein',
  'second'
    => 'zweiten',
  'second_fourth'
    => 'zweiten und vierten',
  'second_last'
    => 'zweiten und letzten',
  'third'
    => 'dritten',
  'unlimited'
    => 'unbegrenzt',
  'zip'
    => 'PLZ',
  
);
