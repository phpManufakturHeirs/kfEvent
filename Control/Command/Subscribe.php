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
use phpManufaktur\Contact\Control\Contact as ContactControl;
use phpManufaktur\Event\Data\Event\Subscription;
use phpManufaktur\Event\Data\Event\Event;
use phpManufaktur\Contact\Data\Contact\Message as MessageData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Subscribe extends Basic
{
    protected $MessageData = null;
    protected $Event = null;
    protected static $event_id = -1;
    protected static $redirect = null;
    protected static $contact_id = -1;
    protected $ContactControl = null;
    protected static $parameter = null;
    protected $SubscriptionData = null;
    protected $EventData = null;
    protected static $config = null;

    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);

        $parameters = $this->getCommandParameters();
        // check the CMS GET parameters
        $GET = $this->getCMSgetParameters();
        if (isset($GET['command']) && ($GET['command'] == 'event')) {
            foreach ($GET as $key => $value) {
                if ($key == 'command') continue;
                $parameters[$key] = $value;
            }
            $this->setCommandParameters($parameters);
        }
        self::$parameter = $this->getCommandParameters();

        $this->MessageData = new MessageData($app);
        $this->ContactControl = new ContactControl($app);
        $this->SubscriptionData = new Subscription($app);
        $this->EventData = new Event($app);

        self::$config = $app['utils']->readConfiguration(MANUFAKTUR_PATH.'/Event/config.event.json');
    }

    protected function getFormFields($subscribe=array())
    {
        // get the communication types and values
        $email = $this->ContactControl->getDefaultCommunicationRecord();
        $phone = $this->ContactControl->getDefaultCommunicationRecord();
        $cell = $this->ContactControl->getDefaultCommunicationRecord();

        $form = $this->app['form.factory']->createBuilder('form')
        // contact - hidden fields
        ->add('event_id', 'hidden', array(
            'data' => self::$event_id
        ))
        ->add('redirect', 'hidden', array(
            'data' => self::$redirect
        ))
        ->add('contact_type', 'hidden', array(
            'data' => 'PERSON'
        ))
        ->add('contact_id', 'hidden', array(
            'data' => self::$contact_id
        ))
        ->add('person_id', 'hidden', array(
            'data' => isset($subscribe['person_id']) ? $subscribe['person_id'] : -1
        ))
        // person - visible form fields
        ->add('person_gender', 'choice', array(
            'choices' => array('MALE' => 'male', 'FEMALE' => 'female'),
            'expanded' => true,
            'label' => 'Gender',
            'data' => isset($subscribe['person_gender']) ? $subscribe['person_gender'] : 'MALE'
        ))
        ->add('person_first_name', 'text', array(
            'required' => false,
            'label' => 'First name',
            'data' => isset($subscribe['person_first_name']) ? $subscribe['person_first_name'] : ''
        ))
        ->add('person_last_name', 'text', array(
            'required' => true,
            'label' => 'Last name',
            'data' => isset($subscribe['person_last_name']) ? $subscribe['person_last_name'] : ''
        ))
        ->add('email_id', 'hidden', array(
            'data' => isset($subscribe['email_id']) ? $subscribe['email_id'] : -1
        ))
        ->add('email', 'email', array(
            'required' => true,
            'label' => 'E-Mail',
            'data' => isset($subscribe['email']) ? $subscribe['email'] : ''
        ))
        ->add('subscribe', 'choice', array(
            'choices' => array('yes' => 'Subscribe to event'),
            'expanded' => true,
            'multiple' => true,
            'required' => true,
            'label' => '&nbsp;', // suppress label
            'data' => isset($subscribe['subscribe']) ? array('yes') : null
        ))
        ->add('message', 'textarea', array(
            'required' => false,
            'data' => isset($subscribe['message']) ? $subscribe['message'] : ''
        ));

        return $form;

    }

    public function check(Application $app)
    {
        $this->initParameters($app);

        $subscribe_fields = $this->getFormFields();
        // get the form
        $form = $subscribe_fields->getForm();

        $form->bind($this->app['request']);
        if ($form->isValid()) {
            $subscribe = $form->getData();
            self::$event_id = $subscribe['event_id'];
            self::$contact_id = $subscribe['contact_id'];

            // contact ID isset or email address already exists as login
            if ((self::$contact_id > 0) || (false !== (self::$contact_id = $this->ContactControl->existsLogin($subscribe['email'])))) {
                // select the contact record
                $contact = $this->ContactControl->select(self::$contact_id);
                if ($contact['contact']['contact_type'] == 'COMPANY') {
                    // this is a COMPANY address !
                    $this->setMessage('The email address %email% is associated with a company contact record. At the moment you can only subscribe to a event with your personal email address!',
                        array('%email%' => $contact['contact']['contact_login']), true);
                    // unset email address
                    unset($subscribe['email']);
                    // set contact ID to -1 to enable a new submission
                    self::$contact_id = -1;
                    // return the form and the message
                    $subscribe_fields = $this->getFormFields($subscribe);
                    $form = $subscribe_fields->getForm();
                    return $this->app['twig']->render($this->app['utils']->templateFile(
                        '@phpManufaktur/Event/Template', 'command/subscribe.twig', $this->getPreferredTemplateStyle()),
                        array(
                            'basic' => $this->getBasicSettings(),
                            'message' => $this->getMessage(),
                            'form' => $form->createView(),
                        ));
                }
                if ($contact['contact']['contact_status'] != 'ACTIVE') {
                    // this contact is not ACTIVE, so we disallow the subscription - set message and write to logifle!
                    $this->setMessage('The status of your address record is actually %status%, so we can not accept your subscription. Please contact the <a href="mailto:%email%">webmaster</a>.',
                        array('%status%' => $contact['contact']['contact_status'], '%email%' => SERVER_EMAIL_ADDRESS), true);
                    // return to the calling dialog
                    $subRequest = Request::create($subscribe['redirect'], 'GET', array('pid' => $this->getParameterID()));
                    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
                }
                // check if the contact date are changed
                if (($contact['person'][0]['person_gender'] != $subscribe['person_gender']) ||
                    ($contact['person'][0]['person_first_name'] != $subscribe['person_first_name']) ||
                    ($contact['person'][0]['person_last_name'] != $subscribe['person_last_name'])) {
                    // update the contact record
                    $data = array(
                        'contact' => array(
                            'contact_id' => self::$contact_id
                        ),
                        'person' => array(
                            array(
                                'person_id' => $contact['person'][0]['person_id'],
                                'person_gender' => $subscribe['person_gender'],
                                'person_first_name' => $subscribe['person_first_name'],
                                'person_last_name' => $subscribe['person_last_name']
                            )
                        )
                    );
                    if (!$this->ContactControl->update($data, self::$contact_id)) {
                        // something went wrong, throw an error
                        throw new \Exception($this->ContactControl->getMessage());
                    }
                    $this->setMessage('The contact record was successfull updated.');
                }
                // this is no new record
                $new_contact = false;
            }
            else {
                // insert a new contact record
                $contact = array(
                    'contact' => array(
                        'contact_id' => self::$contact_id,
                        'contact_type' => 'PERSON',
                        'contact_status' => self::$config['contact']['confirm']['double_opt_in'] ? 'PENDING' : 'ACTIVE',
                        'contact_login' => strtolower($subscribe['email']),
                        'contact_name' => $subscribe['person_last_name'].(!empty($subscribe['person_first_name'])) ? ', '.$subscribe['person_first_name'] : ''
                    ),
                    'person' => array(
                        array(
                            'person_id' => -1,
                            'person_gender' => $subscribe['person_gender'],
                            'person_first_name' => $subscribe['person_first_name'],
                            'person_last_name' => $subscribe['person_last_name']
                        )
                    ),
                    'communication' => array(
                        array(
                            'communication_id' => -1,
                            'communication_type' => 'EMAIL',
                            'communication_usage' => 'PRIMARY',
                            'communication_value' => strtolower($subscribe['email'])
                        )
                    )
                );
                if (!$this->ContactControl->insert($contact, self::$contact_id)) {
                    // something went wrong, throw an error
                    throw new \Exception($this->ContactControl->getMessage());
                }
                $contact['contact']['contact_id'] = self::$contact_id;
                // this is a new contact
                $new_contact = true;
            }

            // check if the subscriber is already registered
            if (false !== ($subscription_id = $this->SubscriptionData->isAlreadySubscribedForEvent(self::$event_id, self::$contact_id))) {
                $subscription_data = $this->SubscriptionData->select($subscription_id);
                $this->setMessage('You have already subscribed to this Event at %datetime%, you can not subscribe again.',
                    array('%datetime%' => date($this->app['translator']->trans('DATETIME_FORMAT'))));
                // return to the calling dialog
                $subRequest = Request::create($subscribe['redirect'], 'GET', array('pid' => $this->getParameterID()));
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

                // return the form and the message
                $subscribe_fields = $this->getFormFields($subscribe);
                $form = $subscribe_fields->getForm();
                return $this->app['twig']->render($this->app['utils']->templateFile(
                    '@phpManufaktur/Event/Template', 'command/subscribe.twig', $this->getPreferredTemplateStyle()),
                    array(
                        'basic' => $this->getBasicSettings(),
                        'message' => $this->getMessage(),
                        'form' => $form->createView(),
                    ));
            }

            // check message
            $message_id = -1;
            if (!empty($subscribe['message'])) {
                // get the event data
                $event = $this->EventData->selectEvent(self::$event_id);
                // insert the message
                $data = array(
                    'contact_id' => self::$contact_id,
                    'application_name' => 'Event',
                    'application_marker_type' => 'Subscription',
                    'application_marker_id' => self::$event_id,
                    'message_title' => $event['description_title'],
                    'message_content' => strip_tags($subscribe['message']),
                    'message_date' => date('Y-m-d H:i:s'),
                );
                $this->MessageData->insert($data, $message_id);
            }

            // insert subscription
            $data = array(
                'event_id' => self::$event_id,
                'contact_id' => self::$contact_id,
                'message_id' => $message_id,
                'subscription_participants' => 1, // only one person at the moment!
                'subscription_date' => date('Y-m-d H:i:s'),
                'subscription_guid' => $this->app['utils']->createGUID(),
                'subscription_status' => self::$config['event']['subscription']['confirm']['double_opt_in'] ? 'PENDING' : 'ACTIVE'
            );

            $subscription_id = -1;
            $this->SubscriptionData->insert($data, $subscription_id);

            // confirm the subscription ?
            if (($new_contact && in_array('contact', self::$config['contact']['confirm']['mail_to'])) ||
                (in_array('contact', self::$config['event']['subscription']['confirm']['mail_to']))) {
                // send a mail to the contact
echo "send mail to contact<br>";
            }

            if ($new_contact && ($contact['contact']['contact_status'] == 'ACTIVE')) {
                $check_array = self::$config['contact']['confirm']['mail_to'];
                unset($check_array['contact']);
                if (!empty($check_array)) {
                    // send a information about the new contact to the members in $check_array
echo "send info about new contact to ".implode(',', $check_array)."<br>";
                }
            }

            if (!self::$config['event']['subscription']['confirm']['double_opt_in'] &&
                ($contact['contact']['contact_status'] == 'ACTIVE')) {
                $check_array = self::$config['event']['subscription']['confirm']['mail_to'];
                unset($check_array['contact']);
                if (!empty($check_array)) {
                    // send a information about the new event subscription to the members in $check_array
echo "send info about event subscription to ".implode(',', $check_array)."<br>";

                }
            }

            // return to the calling dialog
            $subRequest = Request::create($subscribe['redirect'], 'GET', array('pid' => $this->getParameterID()));
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }
        else {
            // invalid form submission
            $this->setMessage('The form is not valid, please check your input and try again!');
            return $this->app['twig']->render($this->app['utils']->templateFile(
                '@phpManufaktur/Event/Template', 'command/subscribe.twig', $this->getPreferredTemplateStyle()),
                array(
                    'basic' => $this->getBasicSettings(),
                    'message' => $this->getMessage(),
                    'form' => $form->createView(),
                ));
        }
    }

    /**
     * Subscribe for the given event
     *
     * @param Application $app
     * @throws \Exception
     * @return string dialog or result
     */
    public function exec(Application $app, $event_id, $redirect)
    {
        $this->initParameters($app);
        self::$event_id = $event_id;
        self::$redirect = base64_decode($redirect);

        $subscribe_fields = $this->getFormFields();
        // get the form
        $form = $subscribe_fields->getForm();

        return $this->app['twig']->render($this->app['utils']->templateFile(
            '@phpManufaktur/Event/Template', 'command/subscribe.twig', $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'message' => $this->getMessage(),
                'form' => $form->createView(),
            ));
    }
}