<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Backend;

use Silex\Application;
use phpManufaktur\Basic\Control\Pattern\Alert;

class Backend extends Alert
{

    protected static $usage = null;
    protected static $usage_param = null;

    /**
     *
     * @param Application $app
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);

        $cms = $this->app['request']->get('usage');
        self::$usage = is_null($cms) ? 'framework' : $cms;
        self::$usage_param = (self::$usage != 'framework') ? '?usage='.self::$usage : '';
        // set the locale from the CMS locale
        if (self::$usage != 'framework') {
            $app['translator']->setLocale($this->app['session']->get('CMS_LOCALE', 'en'));
        }
    }

    /**
     * Get the toolbar for all backend dialogs
     *
     * @param string $active dialog
     * @return multitype:multitype:string boolean
     */
    public function getToolbar($active) {
        $toolbar_array = array(
            'event_list' => array(
                'name' => 'event_list',
                'text' => 'Event list',
                'hint' => 'List of all active events',
                'link' => FRAMEWORK_URL.'/admin/event/list'.self::$usage_param,
                'active' => ($active == 'event_list')
            ),
            'event_edit' => array(
                'name' => 'event_edit',
                'text' => 'Event',
                'hint' => 'Create a new event',
                'link' => FRAMEWORK_URL.'/admin/event/edit'.self::$usage_param,
                'active' => ($active == 'event_edit')
            ),
            'registration' => array(
                'name' => 'registration',
                'text' => 'Registrations',
                'hint' => 'List of all registrations for events',
                'link' => FRAMEWORK_URL.'/admin/event/registration'.self::$usage_param,
                'active' => ($active == 'registration')
            ),
            'propose' => array(
                'name' => 'propose',
                'text' => 'Proposes',
                'hint' => 'List of actual submitted proposes for events',
                'link' => FRAMEWORK_URL.'/admin/event/propose'.self::$usage_param,
                'active' => ($active == 'propose')
            ),
            'contact_list' => array(
                'name' => 'contact_list',
                'text' => 'Contact list',
                'hint' => 'List of all available contacts (Organizer, Locations, Participants)',
                'link' => FRAMEWORK_URL.'/admin/event/contact/list'.self::$usage_param,
                'active' => ($active == 'contact_list')
            ),
            'contact_edit' => array(
                'name' => 'contact_edit',
                'text' => 'Contact',
                'hint' => 'Create a new contact',
                'link' => FRAMEWORK_URL.'/admin/event/contact/select'.self::$usage_param,
                'active' => ($active == 'contact_edit')
            ),
            'group' => array(
                'name' => 'event_groups',
                'text' => 'Groups',
                'hint' => 'List of all available event groups',
                'link' => FRAMEWORK_URL.'/admin/event/group/list'.self::$usage_param,
                'active' => ($active == 'group')
            ),

            'about' => array(
                'name' => 'about',
                'text' => 'About',
                'hint' => 'Information about the Event extension',
                'link' => FRAMEWORK_URL.'/admin/event/about'.self::$usage_param,
                'active' => ($active == 'about')
                ),
        );
        return $toolbar_array;
    }

}
