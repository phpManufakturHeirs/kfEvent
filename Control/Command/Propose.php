<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Command;

use Silex\Application;
use phpManufaktur\Basic\Control\kitCommand\Basic;
use phpManufaktur\Event\Data\Event\Group as EventGroup;
use phpManufaktur\Contact\Data\Contact\Overview;
use phpManufaktur\Event\Data\Event\OrganizerTag;
use phpManufaktur\Contact\Control\Contact;
use phpManufaktur\Event\Data\Event\LocationTag;
use phpManufaktur\Event\Data\Event\Event;

class Propose extends Basic
{

    public function controllerSubmitter(Application $app)
    {
        return 'submitter';
    }

    /**
     * Controller to check the event data, save the record and step to
     * the proposer record
     *
     * @param Application $app
     * @return string dialog
     */
    public function controllerEventCheck(Application $app)
    {
        $this->initParameters($app);

        // get the form fields
        $event = $this->app['request']->request->get('form', array());

        // check the event data
        if (strtotime($event['event_date_from']) < time()) {
            // event date is in the past
            $this->setMessage('The event date can not be in the past!');
            return $this->controllerEvent($app);
        }
        if (strtotime($event['event_date_from']) > strtotime($event['event_date_to'])) {
            $this->setMessage('The event start date is behind the event end date!');
            return $this->controllerEvent($app);
        }
        if (strtotime($event['event_publish_from']) > strtotime($event['event_date_from'])) {
            $this->setMessage('The publishing date is behind the event start date!');
            return $this->controllerEvent($app);
        }
        if (strtotime($event['event_publish_to']) < strtotime($event['event_date_from'])) {
            $this->setMessage('The publishing date ends before the event starts, this is not allowed!');
            return $this->controllerEvent($app);
        }
        if (strtotime($event['event_deadline']) > strtotime($event['event_date_from'])) {
            $this->setMessage('The deadline ends after the event start date!');
            return $this->controllerEvent($app);
        }
        if (strlen($event['description_short']) < 30) {
            $this->setMessage('Please type in a short description with %minimum% characters.', array('%minimum%' => 30));
            return $this->controllerEvent($app);
        }
        if (strlen($event['description_long']) < 50) {
            $this->setMessage('Please type in a long description with %minimum% characters.', array('%minimum%' => 50));
            return $this->controllerEvent($app);
        }

        // ok - save the event
        $data = array(
            'group_id' => $this->app['session']->get('group_id'),
            'event_type' => 'EVENT',
            'event_organizer' => $this->app['session']->get('organizer_id'),
            'event_location' => $this->app['session']->get('location_id'),
            'event_costs' => isset($event['event_costs']) ? $this->app['utils']->str2float($event['event_costs']) : 0,
            'event_participants_max' => isset($event['event_participants_max']) ? $this->app['utils']->str2int($event['event_participants_max']) : -1,
            'event_status' => 'LOCKED',
            'event_date_from' => date('Y-m-d H:i:s', strtotime($event['event_date_from'])),
            'event_date_to' => date('Y-m-d H:i:s', strtotime($event['event_date_to'])),
            'event_publish_from' => date('Y-m-d H:i:s', strtotime($event['event_publish_from'])),
            'event_publish_to' => date('Y-m-d H:i:s', strtotime($event['event_publish_to'])),
            'event_deadline' => date('Y-m-d H:i:s', strtotime($event['event_deadline'])),
            'description_title' => isset($event['description_title']) ? $event['description_title'] : '',
            'description_short' => isset($event['description_short']) ? $event['description_short'] : '',
            'description_long' => isset($event['description_long']) ? $event['description_long'] : '',
            'event_url' => isset($event['event_url']) ? $event['event_url'] : ''
        );

        $EventData = new Event($app);
        $event_id = -1;
        $EventData->insertEvent($data, $event_id);

        $this->app['session']->set('event_id', $event_id);

        return $this->controllerSubmitter($app);
    }

    /**
     * Controller to create a event
     *
     * @param Application $app
     * @return string dialog
     */
    public function controllerEvent(Application $app)
    {
        $this->initParameters($app);

        $ContactData = new Contact($app);
        // get the organizer data
        if (false === ($organizer = $ContactData->selectOverview($this->app['session']->get('organizer_id', -1)))) {
            throw new \Exception("The Organizer does not exists!");
        }
        // get the location data
        if (false === ($location = $ContactData->selectOverview($this->app['session']->get('location_id', -1)))) {
            throw new \Exception("The Location does not exists!");
        }

        // get the form fields
        $event = $this->app['request']->request->get('form', array());

        $fields = $this->app['form.factory']->createBuilder('form')
        // Event date
        ->add('event_date_from', 'text', array(
            'attr' => array('class' => 'event_date_from'),
            'data' => isset($event['event_date_from']) ? date($this->app['translator']->trans('DATETIME_FORMAT'), strtotime($event['event_date_from'])) : null,
        ))
        ->add('event_date_to', 'text', array(
            'attr' => array('class' => 'event_date_to'),
            'data' => isset($event['event_date_to']) ? date($this->app['translator']->trans('DATETIME_FORMAT'), strtotime($event['event_date_to'])) : null
        ))
        // Publish from - to
        ->add('event_publish_from', 'text', array(
            'attr' => array('class' => 'event_publish_from'),
            'data' => isset($event['event_publish_from']) ? date($this->app['translator']->trans('DATETIME_FORMAT'), strtotime($event['event_publish_from'])) : null,
            'label' => 'Publish from',
            'required' => false
        ))
        ->add('event_publish_to', 'text', array(
            'attr' => array('class' => 'event_publish_to'),
            'data' => isset($event['event_publish_to']) ? date($this->app['translator']->trans('DATETIME_FORMAT'), strtotime($event['event_publish_to'])) : null,
            'label' => 'Publish to',
            'required' => false
        ))
        // Deadline
        ->add('event_deadline', 'text', array(
            'attr' => array('class' => 'event_deadline'),
            'data' => isset($event['event_deadline']) ? date($this->app['translator']->trans('DATETIME_FORMAT'), strtotime($event['event_deadline'])) : null,
            'label' => 'Deadline',
            'required' => false
        ))
        // Participants
        ->add('event_participants_max', 'text', array(
            'label' => 'Participants, maximum',
            'data' => isset($event['event_participants_max']) ? $this->app['utils']->str2int($event['event_participants_max']) : -1,
            'label' => 'Participants maximum',
            'required' => false
        ))
        // Costs
        ->add('event_costs', 'text', array(
            'required' => false,
            'data' => isset($event['event_costs']) ? number_format($this->app['utils']->str2float($event['event_costs']), 2, $this->app['translator']->trans('DECIMAL_SEPARATOR'), $this->app['translator']->trans('THOUSAND_SEPARATOR')) : 0
        ))
        // Event URL
        ->add('event_url', 'url', array(
            'required' => false,
            'data' => isset($event['event_url']) ? $event['event_url'] : ''
        ))
        ->add('description_title', 'text', array(
            'data' => isset($event['description_title']) ? $event['description_title'] : '',
            'label' => 'Title'
        ))
        ->add('description_short', 'textarea', array(
            'data' => isset($event['description_short']) ? $event['description_short'] : '',
            'label' => 'Short description',
            'required' => false
        ))
        ->add('description_long', 'textarea', array(
            'data' => isset($event['description_long']) ? $event['description_long'] : '',
            'label' => 'Long description',
            'required' => false
        ))
        ;
        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template',
            "command/event.propose.event.create.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView(),
                'organizer' => $organizer,
                'location' => $location,
                'route' => array(
                    'event' => array(
                        'check' => '/event/propose/event/check'
                    )
                )
            ));
    }

    /**
     * Controller set the contact ID for the location and redirect to create
     * a new Event record
     *
     * @param Application $app
     * @param integer $contact_id
     * @return string
     */
    public function controllerLocationID(Application $app, $contact_id)
    {
        $app['session']->set('location_id', $contact_id);
        return $this->controllerEvent($app);
    }

    /**
     * Controller to select a location
     *
     * @param Application $app
     * @throws \Exception
     * @return string
     */
    public function controllerSelectLocation(Application $app)
    {
        $this->initParameters($app);

        // get the form fields
        $form_request = $this->app['request']->request->get('form', array());

        // check the search term
        if (!isset($form_request['new_location']) && (!isset($form_request['search']) || empty($form_request['search']))) {
            $this->setMessage('Please search for for a location or select the checkbox to create a new one.');
            return $this->controllerSearchOrganizer($app);
        }

        if (isset($form_request['new_location'])) {
            // create a new location record
            return $this->createContact('location', $this->app['session']->get('group_id'));
        }

        // get the TAGS assigned to the location
        $LocationTag = new LocationTag($app);
        if (false === ($tags = $LocationTag->selectTagNamesByGroupID($this->app['session']->get('group_id')))) {
            throw new \Exception("Missing the location TAG names for the group ID ".$this->app['session']->get('group_id'));
        }

        $Overview = new Overview($app);
        if (false === ($locations = $Overview->searchContact($form_request['search'], $tags, 'ACTIVE', '='))) {
            // no search result
            $this->setMessage('There exists no locations who fits to the search term %search%', array('%search%' => $form_request['search']));
            return $this->controllerSearchLocation($app);
        }

        // show the list with the matching organizers
        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template',
            "command/event.propose.location.select.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'locations' => $locations,
                'search_term' => $form_request['search'],
                'route' => array(
                    'location' => array(
                        'search' => '/event/propose/location/search',
                        'create' => '/event/propose/location/create/group/'.$this->app['session']->get('group_id'),
                        'id' => '/event/propose/location/id/{contact_id}'
                    )
                )
            ));

    }

    /**
     * Controller display a dialog to search for a location or create a new one
     *
     * @param Application $app
     */
    public function controllerSearchLocation(Application $app)
    {
        $this->initParameters($app);

        $fields = $this->app['form.factory']->createBuilder('form')
        ->add('search', 'text', array(
            'label' => 'Search Location',
            'required' => false
        ))
        ->add('new_location', 'checkbox', array(
            'required' => false
        ));
        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template',
            "command/event.propose.location.search.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView(),
                'route' => array(
                    'location' => array(
                        'select' => '/event/propose/location/select'
                    )
                )
            ));
    }

    /**
     * Controller to get the organizer ID and step to the location search
     *
     * @param Application $app
     * @param integer $contact_id
     * @return string
     */
    public function controllerOrganizerID(Application $app, $contact_id)
    {
        $app['session']->set('organizer_id', $contact_id);
        return $this->controllerSearchLocation($app);
    }

    /**
     * Check a new contact, insert it, set session and redirect to the next step
     *
     * @param Application $app
     * @throws \Exception
     * @return string
     */
    public function controllerContactCheck(Application $app)
    {
        $this->initParameters($app);

        $request = $this->app['request']->request->get('form');

        // some checks before creating the contact
        if ($request['contact_type'] == 'PERSON') {
            // check for a PERSON contact
            if (empty($request['person_last_name'])) {
                $this->setMessage('You have selected <i>natural person</i> as contact type, so please give us the last name of the person.');
                return $this->createContact($request['create_type'], $request['group_id']);
            }
        }
        else {
            // check for COMPANY contact
            if (empty($request['company_name'])) {
                $this->setMessage('You have selected <i>Company, Institution or Association</i> as contact type, so please give us the name');
                return $this->createContact($request['create_type'], $request['group_id']);
            }
        }

        if (empty($request['email']) && empty($request['phone']) && empty($request['url'])) {
            // at least we need one communication way
            $this->setMessage('At least we need one communication channel, so please tell us a email address, phone or a URL');
            return $this->createContact($request['create_type'], $request['group_id']);
        }

        if ($request['create_type'] == 'organizer') {
            // get the TAGS assigned to the organizer
            $OrganizerTag = new OrganizerTag($app);
            if (false === ($group_tags = $OrganizerTag->selectTagNamesByGroupID($request['group_id']))) {
                throw new \Exception("Missing the organizer TAG names for the group ID ".$request['group_id']);
            }
        }
        else {
            // get the TAGS assigned to the organizer
            $LocationTag = new LocationTag($app);
            if (false === ($group_tags = $LocationTag->selectTagNamesByGroupID($request['group_id']))) {
                throw new \Exception("Missing the location TAG names for the group ID ".$request['group_id']);
            }
        }

        $tags = array();
        foreach ($group_tags as $tag) {
            $tags[] = array(
                'contact_id' => -1,
                'tag_name' => $tag
            );
        }

        if (!empty($request['email'])) {
            $login = strtolower($request['email']);
        }
        elseif ($request['contact_type'] == 'PERSON') {
            $login = sprintf('%s_%s', strtolower($request['person_last_name']), date('zHi'));
        }
        else {
            $login = sprintf('%s_%s', strtolower($request['company_name']), date('zHi'));
        }

        $data = array(
            'contact' => array(
                'contact_id' => -1,
                'contact_type' => $request['contact_type'],
                'contact_name' => null,
                'contact_login' => $login,
                'contact_status' => 'LOCKED'
            ),
            'tag' => $tags,
            'company' => array(
                array(
                    'company_id' => -1,
                    'contact_id' => -1,
                    'company_name' => $request['company_name'],
                    'company_department' => $request['company_department']
                )
            ),
            'person' => array(
                array(
                    'person_id' => -1,
                    'contact_id' => -1,
                    'person_gender' => $request['person_gender'],
                    'person_first_name' => $request['person_first_name'],
                    'person_last_name' => $request['person_last_name']
                )
            ),
            'communication' => array(
                array(
                    'communication_id' => -1,
                    'contact_id' => -1,
                    'communication_type' => 'EMAIL',
                    'communication_usage' => 'PRIMARY',
                    'communication_value' => $request['email']
                ),
                array(
                    'communication_id' => -1,
                    'contact_id' => -1,
                    'communication_type' => 'PHONE',
                    'communication_usage' => 'PRIMARY',
                    'communication_value' => $request['phone']
                ),
                array(
                    'communication_id' => -1,
                    'contact_id' => -1,
                    'communication_type' => 'URL',
                    'communication_usage' => 'BUSINESS',
                    'communication_value' => $request['url']
                ),

            ),
            'address' => array(
                array(
                    'address_id' => -1,
                    'contact_id' => -1,
                    'address_type' => 'BUSINESS',
                    'address_street' => $request['address_street'],
                    'address_zip' => $request['address_zip'],
                    'address_city' => $request['address_city'],
                    'address_state' => $request['address_state'],
                    'address_country_code' => $request['address_country']
                )
            ),
            'note' => array(
                array(
                    'note_id' => -1,
                    'contact_id' => -1,
                    'note_title' => 'Remarks',
                    'note_type' => 'TEXT',
                    'note_content' => $request['note']
                )
            )
        );

        $ContactControl = new Contact($app);
        $contact_id = -1;
        if (false === ($ContactControl->insert($data, $contact_id))) {
            // don't return as message to to visitor, better throw an error
            throw new \Exception(strip_tags($ContactControl->getMessage()));
        }

        if ($request['create_type'] == 'organizer') {
            // set session for 'organizer_id'
            $this->app['session']->set('organizer_id', $contact_id);
            return $this->controllerSearchLocation($app);
        }
        else {
            // set session for 'location_id'
            $this->app['session']->set('location_id', $contact_id);
            return $this->controllerEvent($app);
        }
    }

    /**
     * Dialog to create a new contact of type 'organizer' or 'location' for the
     * specified event group ID
     *
     * @param string $type maybe 'organizer' or 'location'
     * @param integer $group_id event group ID
     * @return string contact dialog
     */
    protected function createContact($type, $group_id)
    {
        $request = $this->app['request']->request->get('form');

        $ContactControl = new Contact($this->app);

        $fields = $this->app['form.factory']->createBuilder('form')
        ->add('create_type', 'hidden', array(
            'data' => $type
        ))
        ->add('group_id', 'hidden', array(
            'data' => $group_id
        ))
        ->add('contact_type', 'choice', array(
            'expanded' => true,
            'multiple' => false,
            'required' => true,
            'choices' => array(
                'PERSON' => 'natural person',
                'COMPANY' => 'company, institution or association'
            ),
            'data' => isset($request['contact_type']) ? $request['contact_type'] : 'COMPANY'
         ))
        // person - visible form fields
        ->add('person_gender', 'choice', array(
            'choices' => array('MALE' => 'male', 'FEMALE' => 'female'),
            'expanded' => true,
            'label' => 'Gender',
            'data' => isset($request['person_gender']) ? $request['person_gender'] : 'MALE',
            'required' => false
        ))
        ->add('person_first_name', 'text', array(
            'required' => false,
            'label' => 'First name',
            'data' => isset($request['person_first_name']) ? $request['person_first_name'] : ''
        ))
        ->add('person_last_name', 'text', array(
            'required' => false,
            'label' => 'Last name',
            'data' => isset($request['person_last_name']) ? $request['person_last_name'] : ''
        ))
        ->add('company_name', 'text', array(
            'required' => false,
            'label' => 'Company name',
            'data' => isset($request['company_name']) ? $request['company_name'] : ''
        ))
        ->add('company_department', 'text', array(
            'required' => false,
            'label' => 'Company department',
            'data' => isset($request['company_department']) ? $request['company_department'] : ''
        ))
        ->add('email', 'email', array(
            'required' => false,
            'data' => isset($request['email']) ? $request['email'] : ''
        ))
        ->add('phone', 'text', array(
            'required' => false,
            'data' => isset($request['phone']) ? $request['phone'] : ''
        ))
        ->add('url', 'url', array(
            'required' => false,
            'label' => 'Homepage',
            'data' => isset($request['url']) ? $request['url'] : ''
        ))
        ->add('address_street', 'text', array(
            'required' => false,
            'label' => 'Street',
            'data' => isset($request['address_street']) ? $request['address_street'] : ''
        ))
        ->add('address_zip', 'text', array(
            'required' => false,
            'label' => 'Zip',
            'data' => isset($request['address_zip']) ? $request['address_zip'] : ''
        ))
        ->add('address_city', 'text', array(
            'required' => true,
            'label' => 'City',
            'data' => isset($request['address_city']) ? $request['address_city'] : ''
        ))
        ->add('address_state', 'text', array(
            'required' => false,
            'label' => 'State',
            'data' => isset($request['address_state']) ? $request['address_state'] : ''
        ))
        ->add('address_country', 'choice', array(
            'choices' => $ContactControl->getCountryArrayForTwig(),
            'empty_value' => '- please select -',
            'expanded' => false,
            'multiple' => false,
            'required' => true,
            'label' => 'Country',
            'data' => isset($request['address_country']) ? $request['address_country'] : null
        ))
        ->add('note', 'textarea', array(
            'required' => false,
            'data' => isset($request['note']) ? $request['note'] : ''
        ))
        ;

        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template',
            "command/event.propose.contact.create.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView(),
                'route' => array(
                    'contact' => array(
                        'check' => '/event/propose/contact/check'
                    )
                )
            ));
    }

    /**
     * Controller to create a new contact of type 'organizer'
     *
     * @param Application $app
     * @param integer $group_id event group ID
     * @return string dialog to create a contact
     */
    public function controllerCreateOrganizer(Application $app, $group_id)
    {
        $this->initParameters($app);
        return $this->createContact('organizer', $group_id);
    }

    /**
     * Controller to select a organizer from the list, created by search results
     *
     * @param Application $app
     * @throws \Exception
     * @return string dialog to select organizer
     */
    public function controllerSelectOrganizer(Application $app)
    {
        $this->initParameters($app);

        // get the form fields
        $form_request = $this->app['request']->request->get('form', array());

        // check the search term
        if (!isset($form_request['new_organizer']) && (!isset($form_request['search']) || empty($form_request['search']))) {
            $this->setMessage('Please search for for a organizer or select the checkbox to create a new one.');
            return $this->controllerSearchOrganizer($app);
        }

        // check the event group ID
        if (!isset($form_request['event_group'])) {
            throw new \Exception("Missing the group ID!");
        }

        if (isset($form_request['new_organizer'])) {
            // create a new organizer record
            return $this->createContact('organizer', $form_request['event_group']);
        }

        // get the TAGS assigned to the organizer
        $OrganizerTag = new OrganizerTag($app);
        if (false === ($tags = $OrganizerTag->selectTagNamesByGroupID($form_request['event_group']))) {
            throw new \Exception("Missing the organizer TAG names for the group ID ".$form_request['event_group']);
        }

        $Overview = new Overview($app);
        if (false === ($organizers = $Overview->searchContact($form_request['search'], $tags, 'ACTIVE', '='))) {
            // no search result
            $this->setMessage('There exists no organizer who fits to the search term %search%', array('%search%' => $form_request['search']));
            return $this->controllerSearchOrganizer($app);
        }

        // set session for the event group ID
        $this->app['session']->set('group_id', $form_request['event_group']);

        // show the list with the matching organizers
        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template',
            "command/event.propose.organizer.select.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'organizers' => $organizers,
                'search_term' => $form_request['search'],
                'route' => array(
                    'organizer' => array(
                        'search' => '/event/propose/organizer/search',
                        'create' => '/event/propose/organizer/create/group/'.$form_request['event_group'],
                        'id' => '/event/propose/organizer/id/{contact_id}'
                    )
                )
            ));
    }

    /**
     * Controller check the submitted group
     *
     * @param Application $app
     */
    public function controllerSearchOrganizer(Application $app)
    {
        $this->initParameters($app);

        // get the parameters
        $parameter = $this->getCommandParameters();

        // check if a event group isset
        $form_request = $this->app['request']->request->get('form', array());
        if (isset($form_request['event_group'])) {
            $group_id = $form_request['event_group'];
        }
        elseif (isset($parameter['group'])) {
            if (is_numeric($parameter['group'])) {
                $group_id = intval($parameter['group']);
            }
            else {
                $EventGroup = new EventGroup($app);
                if (false === ($group_id = $EventGroup->getGroupID($parameter['group']))) {
                    $Message = new Message($app);
                    return $Message->render('The event group with the name %group% does not exists!',
                        array('%group%' => $parameter['group']), 'group[]', array(), true);
                }
            }
        }

        $fields = $this->app['form.factory']->createBuilder('form')
        ->add('event_group', 'hidden', array(
            'data' => $group_id
        ))
        ->add('search', 'text', array(
            'label' => 'Search Organizer',
            'required' => false
        ))
        ->add('new_organizer', 'checkbox', array(
            'required' => false
        ));
        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template',
            "command/event.propose.organizer.search.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView(),
                'route' => array(
                    'organizer' => array(
                        'select' => '/event/propose/organizer/select'
                    )
                )
            ));
    }


    /**
     * Controller to select a event group
     *
     * @param Application $app
     */
    public function controllerSelectGroup(Application $app)
    {
        $this->initParameters($app);

        $EventGroup = new EventGroup($app);

        $fields = $this->app['form.factory']->createBuilder('form')
        // contact - hidden fields
        ->add('event_group', 'choice', array(
            'choices' => $EventGroup->getArrayForTwig(),
            'empty_value' => '- please select -',
            'expanded' => false,
            'label' => 'Select event group',
        ));
        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template',
            "command/event.propose.group.twig",
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView(),
                'route' => array(
                    'organizer' => array(
                        'search' => '/event/propose/organizer/search'
                    )
                )
            ));
    }

    /**
     * General execution for a propose. This Controller will be directly
     * executed from class Action.
     *
     * @param Application $app
     * @return string Dialog
     */
    public function exec(Application $app)
    {
        // init BASIC
        $this->initParameters($app);

        // get the parameters
        $parameter = $this->getCommandParameters();

        // remove all SESSION variables former used for the propose
        $app['session']->remove('group_id');
        $app['session']->remove('location_id');
        $app['session']->remove('organizer_id');
        $app['session']->remove('event_id');

        if (!isset($parameter['group'])) {
            // must first select a group
            return $this->controllerSelectGroup($app);
        }

        // select the organizer for the proposed event
        return $this->controllerSearchOrganizer($app);
    }

}
