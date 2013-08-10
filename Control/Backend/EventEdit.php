<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/propangas24
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Event\Control\Backend;

use Silex\Application;
use phpManufaktur\Event\Control\Backend\Backend;
use phpManufaktur\Event\Data\Event\Event as EventData;
use phpManufaktur\Event\Data\Event\Group as EventGroup;
use phpManufaktur\Contact\Control\Contact as ContactControl;
use phpManufaktur\Event\Data\Event\OrganizerTag as EventOrganizerTag;
use phpManufaktur\Event\Data\Event\LocationTag as EventLocationTag;
use phpManufaktur\Event\Data\Event\Description as EventDescription;

class EventEdit extends Backend {

    protected static $event_id = -1;
    protected $EventData = null;
    protected $EventGroup = null;
    protected $ContactControl = null;
    protected $EventOrganizerTag = null;
    protected $EventLocationTag = null;
    protected $EventDescription = null;

    public function __construct(Application $app=null)
    {
        parent::__construct($app);
        if (!is_null($app)) {
            $this->initialize($app);
        }
    }

    protected function initialize(Application $app)
    {
        parent::initialize($app);
        $this->EventData = new EventData($this->app);
        $this->EventGroup = new EventGroup($this->app);
        $this->ContactControl = new ContactControl($this->app);
        $this->EventLocationTag = new EventLocationTag($this->app);
        $this->EventOrganizerTag = new EventOrganizerTag($this->app);
        $this->EventDescription = new EventDescription($this->app);
    }
    
    public function setEventID($event_id)
    {
        self::$event_id = $event_id;
    }

    /**
     * Create the form fields for the start dialog to create a new event
     *
     * @return $fields FormFactory
     */
    protected function getCreateStartFormFields()
    {
        $fields = $this->app['form.factory']->createBuilder('form')
        ->add('event_id', 'hidden', array(
            'data' => self::$event_id
        ))
        ->add('create_by', 'choice', array(
            'choices' => array('GROUP' => 'by selecting a event group', 'COPY' => 'by copying from a existing event'),
            'expanded' => true,
            'label' => 'Create a new event',
            'data' => 'GROUP'
        ));
        return $fields;
    }

    protected function getCreateByGroupFormFields()
    {
        $fields = $this->app['form.factory']->createBuilder('form')
        ->add('event_id', 'hidden', array(
            'data' => self::$event_id
        ))
        ->add('select_group', 'choice', array(
            'choices' => $this->EventGroup->getArrayForTwig(),
            'empty_value' => '- please select -',
            'expanded' => false,
            'label' => 'Select event group',
        ))
        ;
        return $fields;
    }

    /**
     * Create the dialog to edit an existing event
     *
     * @param array $event data record
     * @return $fields FormFactory
     */
    protected function getFormFields($event)
    {
        if (false === ($group = $this->EventGroup->select($event['group_id']))) {
            throw new \Exception('The event group with the ID '.$event['group_id']." does not exists!");
        }
        $organizer_tags = $this->EventOrganizerTag->selectTagNamesByGroupID($event['group_id']);
        $location_tags = $this->EventLocationTag->selectTagNamesByGroupID($event['group_id']);

        $fields = $this->app['form.factory']->createBuilder('form')
        ->add('event_id', 'hidden', array(
            'data' => self::$event_id
        ))
        ->add('event_status', 'choice', array(
            'choices' => array('ACTIVE' => 'active', 'LOCKED' => 'locked', 'DELETED' => 'deleted'),
            'empty_value' => false,
            'expanded' => false,
            'required' => false,
            'label' => 'Status',
            'data' => $event['event_status']
        ))
        ->add('group_id', 'hidden', array(
            'data' => $event['group_id']
        ))
        ->add('group_name', 'hidden', array(
            'data' => $this->EventGroup->getGroupName($event['group_id'])
        ))
        
        // Organizer
        ->add('event_organizer', 'choice', array(
            'choices' => $this->ContactControl->getContactsByTagsForTwig($organizer_tags),
            'empty_value' => '- please select -',
            'expanded' => false,
            'required' => true,
            'label' => 'Organizer',
            'data' => $event['event_organizer']
        ))
        
        // Location
        ->add('event_location', 'choice', array(
            'choices' => $this->ContactControl->getContactsByTagsForTwig($location_tags), // explode(',', $group['group_location_contact_tags'])),
            'empty_value' => '- please select -',
            'expanded' => false,
            'required' => true,
            'label' => 'Event location',
            'data' => $event['event_location']
        ))
        // Participants
        ->add('event_participants_max', 'text', array(
            'label' => 'Participants, maximum',
            'data' => $event['event_participants_max']
        ))
        ->add('event_participants_total', 'hidden', array(
            'data' => $event['event_participants_total']
        ))
        ;
        
        if (function_exists('intl_get_error_code')) {
            // use the date field only if the intl extension is installed
            // Event date
            $fields->add('event_date_from', 'date', array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => array('class' => 'event_date_from'),
                'data' => (!empty($event['event_date_from']) && ($event['event_date_from'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_date_from']) : null
            ));
            $fields->add('event_date_to', 'date', array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => array('class' => 'event_date_to'),
                'data' => (!empty($event['event_date_to']) && ($event['event_date_to'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_date_to']) : null
            ));
            // Publish from - to
            $fields->add('event_publish_from', 'date', array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => array('class' => 'event_publish_from'),
                'data' => (!empty($event['event_publish_from']) && ($event['event_publish_from'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_publish_from']) : null
            ));
            $fields->add('event_publish_to', 'date', array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => array('class' => 'event_publish_to'),
                'data' => (!empty($event['event_publish_to']) && ($event['event_publish_to'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_publish_to']) : null
            ));
            // Deadline
            $fields->add('event_deadline', 'date', array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => array('class' => 'event_deadline'),
                'data' => (!empty($event['event_deadline']) && ($event['event_deadline'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_deadline']) : null
            ));
        }
        else {
            // alternate date handling
            // Event date
            $fields->add('event_date_from', 'text', array(
                'attr' => array('class' => 'event_date_from'),
                'data' => (!empty($event['event_date_from']) && ($event['event_date_from'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_date_from']) : null
            ));
            $fields->add('event_date_to', 'text', array(
                'attr' => array('class' => 'event_date_to'),
                'data' => (!empty($event['event_date_to']) && ($event['event_date_to'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_date_to']) : null
            ));
            // Publish from - to
            $fields->add('event_publish_from', 'text', array(
                'attr' => array('class' => 'event_publish_from'),
                'data' => (!empty($event['event_publish_from']) && ($event['event_publish_from'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_publish_from']) : null
            ));
            $fields->add('event_publish_to', 'text', array(
                'attr' => array('class' => 'event_publish_to'),
                'data' => (!empty($event['event_publish_to']) && ($event['event_publish_to'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_publish_to']) : null
            ));
            // Deadline
            $fields->add('event_deadline', 'text', array(
                'attr' => array('class' => 'event_deadline'),
                'data' => (!empty($event['event_deadline']) && ($event['event_deadline'] != '0000-00-00 00:00:00')) ? new \DateTime($event['event_deadline']) : null
            ));
        }
        
        

        return $fields;
    }

    public function exec(Application $app, $event_id=null)
    {
        $this->initialize($app);
        if (!is_null($event_id)) {
            $this->setEventID($event_id);
        }
        // check if a event ID isset
        $form_request = $this->app['request']->request->get('form', array());
        if (isset($form_request['event_id'])) {
            self::$event_id = $form_request['event_id'];
        }

        $is_start = false;
        if (self::$event_id < 1) {
            $is_start = true;
            if (isset($form_request['create_by'])) {
                if ($form_request['create_by'] == 'COPY') {
                    // show the dialog to copy an existing event into a new one
                    throw new \Exception('The COPY dialog is not implemented yet!');
                }
                else {
                    // show the dialog to select a event group
                    $fields = $this->getCreateByGroupFormFields();
                    $form = $fields->getForm();
                    return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Event/Template', 'backend/event.create.by.group.twig'),
                        array(
                            'usage' => self::$usage,
                            'toolbar' => $this->getToolbar('event_edit'),
                            'message' => $this->getMessage(),
                            'form' => $form->createView()
                        ));
                }
            }
            elseif (isset($form_request['select_group'])) {
                // create a new event using the specified event group
                $data = array(
                    'group_id' => $form_request['select_group']
                );
                $this->EventData->insert($data, self::$event_id);
                // create a description record for the event
                $data = array(
                    'event_id' => self::$event_id,
                    'description_title' => '',
                    'description_short' => '',
                    'description_long' => ''
                );
                $this->EventDescription->insert($data);
                // get the event data
                $event = $this->EventData->selectEvent(self::$event_id);
            }
            else {
                // first step - show the start dialog to create a new event
                $fields = $this->getCreateStartFormFields();
                $form = $fields->getForm();
                return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Event/Template', 'backend/event.create.start.twig'),
                    array(
                        'usage' => self::$usage,
                        'toolbar' => $this->getToolbar('event_edit'),
                        'message' => $this->getMessage(),
                        'form' => $form->createView()
                    ));
            }
        }
        // select the event data
        elseif (false === ($event = $this->EventData->selectEvent(self::$event_id))) {
            $event = $this->EventData->getDefaultRecord();
            $this->setMessage('The record with the ID %id% does not exists!', array('%id%' => self::$event_id));
            self::$event_id = -1;
        }

        // create the form fields
        $fields = $this->getFormFields($event);
        // get the form
        $form = $fields->getForm();

        if (!$is_start && ('POST' == $this->app['request']->getMethod())) {
            // the form was submitted, bind the request
$req = $this->app['request']->request->get('form');
print_r($req);            
            $form->bind($this->app['request']);
echo "xxx";
            if ($form->isValid()) {
                $event = $form->getData();
                self::$event_id = $event['event_id'];
echo "id2: ".self::$event_id;
                if (self::$event_id < 1) {
                    // insert a new event

                }
                else {
                    // update an existing event

                }

                if (self::$event_id > 0) {
                    // get the actual event record
                    $event = $this->EventData->select(self::$event_id);
                    // get the form fields
                    $fields = $this->getFormFields($event);
                    // get the form
                    $form = $fields->getForm();
                }
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setMessage('The form is not valid, please check your input and try again!');
            }
        }

        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Event/Template', 'backend/event.edit.twig'),
            array(
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('event_edit'),
                'message' => $this->getMessage(),
                'form' => $form->createView()
            ));
    }

}