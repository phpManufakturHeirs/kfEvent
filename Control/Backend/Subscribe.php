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

use phpManufaktur\Event\Control\Backend\Backend;
use Silex\Application;
use phpManufaktur\Event\Data\Event\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use phpManufaktur\Event\Data\Event\EventSearch;
use phpManufaktur\Event\Data\Event\Event as EventData;

class Subscribe extends Backend {

    protected $EventSearch = null;
    protected $EventData = null;
    protected $SubscriptionData = null;

    protected function initialize(Application $app)
    {
        parent::initialize($app);
        $this->EventSearch = new EventSearch($app);
        $this->EventData = new EventData($app);
        $this->SubscriptionData = new Subscription($app);
    }

    /**
     * Get the search field for the Contact
     *
     */
    protected function getSearchContactFormFields()
    {
        return $this->app['form.factory']->createBuilder('form')
            ->add('search_contact', 'text', array(
            ));
    }

    /**
     * Form: Select the contact
     *
     * @param array $contacts
     */
    protected function getSelectContactFormFields($contacts=array())
    {
        $select_contacts = array();
        foreach ($contacts as $contact) {
            $select_contacts[$contact['contact_id']] = sprintf('%s [%s] %s %s %s',
                $contact['contact_name'],
                $contact['communication_email'],
                $contact['address_zip'],
                $contact['address_city'],
                $contact['address_street']
            );
        }
        return $this->app['form.factory']->createBuilder('form')
            ->add('contact_id', 'choice', array(
                'choices' => $select_contacts,
                'empty_value' => '- please select -',
                'expanded' => false,
                'required' => true,
                'label' => 'Contact search'
        ));
    }

    /**
     * Get the search field for the Event
     *
     * @param array $data
     */
    protected function getSearchEventFormFields($data=array())
    {
        return $this->app['form.factory']->createBuilder('form', $data)
        ->add('contact_id', 'hidden', array(
            'data' => isset($data['contact_id']) ? $data['contact_id'] : -1
        ))
        ->add('search_event', 'text');
    }

    /**
     * Get the final subscription form fields
     *
     * @param array $data
     */
    protected function getSubscriptionFormFields($data=array())
    {
        $event_array = array();
        if (isset($data['events'])) {
            foreach ($data['events'] as $event) {
                $event_array[$event['event_id']] = sprintf('[%05d] %s - %s',
                    $event['event_id'],
                    date($this->app['translator']->trans('DATE_FORMAT'), strtotime($event['event_date_from'])),
                    $event['description_title']
                );
            }
        }

        if (isset($data['contact_id'])) {
            $contact = $this->app['contact']->selectOverview($data['contact_id']);
        }

        return $this->app['form.factory']->createBuilder('form', $data)
        ->add('contact_id', 'hidden', array(
            'data' => isset($data['contact_id']) ? $data['contact_id'] : -1
        ))
        ->add('subscriber', 'text', array(
            'data' => isset($contact['contact_name']) ? $contact['contact_name'] : '',
            'disabled' => true,
            'required' => false
        ))
        ->add('event_id', 'choice', array(
            'choices' => isset($event_array) ? $event_array : null,
            'empty_value' => '- please select -',
            'label' => 'Select event'
        ))
        ->add('remark', 'textarea', array(
            'data' => isset($data['remark']) ? $data['remark'] : '',
            'required' => false
        ))
        ;
    }

    /**
     * Add the subscription to the database
     *
     * @param Application $app
     * @return string
     */
    public function ControllerFinishSubscription(Application $app)
    {
        $this->initialize($app);

        $data = $this->app['request']->request->get('form');

        if (!isset($data['contact_id']) || !isset($data['event_id'])) {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!', array(), self::ALERT_TYPE_DANGER);
            $subRequest = Request::create('/admin/event/subscription/add/start', 'GET', array('usage' => self::$usage));
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        // handle the subscription
        if (false === ($event = $this->EventData->selectEvent($data['event_id']))) {
            throw new \Exception('The Event with the ID '.$data['event_id'].' does not exists!');
        }

        $message_id = -1;
        if (isset($data['remark']) && !empty($data['remark'])) {
            // insert a new Message
            $message_id = $this->app['contact']->addMessage(
                $data['contact_id'],
                $event['description_title'],
                $data['remark'],
                'Event',
                'Subscription',
                $data['event_id']
            );
        }

        $data = array(
            'event_id' => $data['event_id'],
            'contact_id' => $data['contact_id'],
            'message_id' => $message_id,
            'subscription_participants' => 1,
            'subscription_date' => date('Y-m-d H:i:s'),
            'subscription_guid' => $this->app['utils']->createGUID(),
            'subscription_status' => 'CONFIRMED'
        );
        $this->SubscriptionData->insert($data);

        $this->setAlert('The subscription was successfull inserted.');
        $subRequest = Request::create('/admin/event/subscription', 'GET', array('usage' => self::$usage));
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Check the submitted event search term and show a selection for a event
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ControllerSearchEvent(Application $app)
    {
        $this->initialize($app);

        $fields = $this->getSearchEventFormFields();
        $form = $fields->getForm();

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                // check the term in event search
                if (false === ($events = $this->EventSearch->search($data['search_event']))) {
                    // no hits for the search term
                    $this->setAlert('No hits for the search term <i>%search%</i>!',
                        array('%search%' => $data['search_event']));
                    $fields = $this->getSearchEventFormFields($data);
                    $form = $fields->getForm();
                    return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                        '@phpManufaktur/Event/Template', 'admin/event.add.subscription.twig'),
                        array(
                            'usage' => self::$usage,
                            'toolbar' => $this->getToolbar('subscription'),
                            'alert' => $this->getAlert(),
                            'form' => $form->createView()
                        ));
                }
                else {
                    $data['events'] = $events;
                    $fields = $this->getSubscriptionFormFields($data);
                    $form = $fields->getForm();
                    return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                        '@phpManufaktur/Event/Template', 'admin/form.add.subscription.twig'),
                        array(
                            'usage' => self::$usage,
                            'toolbar' => $this->getToolbar('subscription'),
                            'alert' => $this->getAlert(),
                            'form' => $form->createView()
                        ));
                }
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setAlert('The form is not valid, please check your input and try again!', array(), self::ALERT_TYPE_DANGER);
                $subRequest = Request::create('/admin/event/subscription/add/start', 'GET', array('usage' => self::$usage));
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            }
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!', array(), self::ALERT_TYPE_DANGER);
            $subRequest = Request::create('/admin/event/subscription/add/start', 'GET', array('usage' => self::$usage));
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }
    }

    /**
     * Controller to add a selected contact and show the search field for an event
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ControllerAddContact(Application $app)
    {
        $this->initialize($app);

        $fields = $this->getSearchEventFormFields();
        $form = $fields->getForm();

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $fields = $this->getSearchEventFormFields($data);
                $form = $fields->getForm();
                return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                    '@phpManufaktur/Event/Template', 'admin/event.add.subscription.twig'),
                    array(
                        'usage' => self::$usage,
                        'toolbar' => $this->getToolbar('subscription'),
                        'alert' => $this->getAlert(),
                        'form' => $form->createView()
                    ));
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setAlert('The form is not valid, please check your input and try again!', array(), self::ALERT_TYPE_DANGER);
                $subRequest = Request::create('/admin/event/subscription/add/start', 'GET', array('usage' => self::$usage));
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            }
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!', array(), self::ALERT_TYPE_DANGER);
            $subRequest = Request::create('/admin/event/subscription/add/start', 'GET', array('usage' => self::$usage));
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

    }

    /**
     * Start Controller to subscribe a contact to an event
     *
     * @param Application $app
     */
    public function ControllerAddSubscriptionStart(Application $app)
    {
        $this->initialize($app);

        $fields = $this->getSearchContactFormFields();
        $form = $fields->getForm();

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                if (false === ($contacts = $app['contact']->searchContact($data['search_contact']))) {
                    $this->setAlert('No hits for the search term <i>%search%</i>!', array('%search%' => $data['search_contact']));
                }
                else {
                    $fields = $this->getSelectContactFormFields($contacts);
                    $form = $fields->getForm();
                    return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                        '@phpManufaktur/Event/Template', 'admin/contact.add.subscription.twig'),
                        array(
                            'usage' => self::$usage,
                            'toolbar' => $this->getToolbar('subscription'),
                            'alert' => $this->getAlert(),
                            'form' => $form->createView()
                        ));
                }
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setAlert('The form is not valid, please check your input and try again!', array(), self::ALERT_TYPE_DANGER);
            }
        }
        else {
            // set the intro text
            $this->setAlert('Please search for the contact you want to subscribe to an event or add a new contact, if you are shure that the person does not exists in Contacts.');
        }

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template', 'admin/start.add.subscription.twig'),
            array(
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('subscription'),
                'alert' => $this->getAlert(),
                'form' => $form->createView()
            ));
    }

    /**
     * Show the about dialog for Event
     *
     * @return string rendered dialog
     */
    public function ControllerList(Application $app)
    {
        $this->initialize($app);

        $SubscriptionData = new Subscription($app);
        $subscriptions = $SubscriptionData->selectList();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template', 'admin/list.subscription.twig'),
            array(
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('subscription'),
                'subscriptions' => $subscriptions,
                'alert' => $this->getAlert()
            ));
    }

}
