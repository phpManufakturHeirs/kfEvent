<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

if ('á' != "\xc3\xa1") {
    // the language files must be saved as UTF-8 (without BOM)
    throw new \Exception('The language file ' . __FILE__ . ' is damaged, it must be saved UTF-8 encoded!');
}

/**
 * The language file for the ENGLISH language is NOT COMPLETE, you will miss many entries!
 *
 * Reason: The english messages, labels, titles a.s.o. are placed directly in the source code
 * and not available as separate file. Please use the de.php as reference for all translations!
 */

return array(

    'About'
        => '?',

    // event data columns
    'Event costs'
        => 'Costs',
    'Event date from'
        => 'Date from',
    'Event date to'
        => 'Date to',
    'Event deadline'
        => 'Deadline',
    'Event id'
        => 'ID',
    'Event participants confirmed'
        => 'Part. conf.',
    'Event participants max'
        => 'max. Part.',
    'Event participants total'
        => 'Total',
    'Event publish from'
        => 'Publish from',
    'Event publish to'
        => 'Publish to',
    'Event status'
        => 'Status',
    'Event timestamp'
        => 'Timestamp',

    'Kitevent'
        => 'kitEvent',
);
