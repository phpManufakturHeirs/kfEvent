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

class Subscribe extends Basic
{
    protected $Message = null;
    protected $Event = null;
    protected $ContactControl = null;
    protected static $parameter = null;

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

        $this->Message = new Message($app);
        $this->ContactControl = new ContactControl($app);

    }

    protected function getFormFields($subscribe=array())
    {
        // get the communication types and values
        $email = $this->ContactControl->getDefaultCommunicationRecord();
        $phone = $this->ContactControl->getDefaultCommunicationRecord();
        $cell = $this->ContactControl->getDefaultCommunicationRecord();

        $form = $this->app['form.factory']->createBuilder('form')
        // contact - hidden fields
        ->add('contact_type', 'hidden', array(
            'data' => 'PERSON'
        ))
        ->add('contact_id', 'hidden', array(
            'data' => isset($subscribe['contact_id']) ? $subscribe['contact_id'] : -1
        ))
        ->add('persion_id', 'hidden', array(
            'data' => isset($subscribe['person_id']) ? $subscribe['person_id'] : -1
        ))
        // person - visible form fields
        ->add('person_gender', 'choice', array(
            'choices' => array('MALE' => 'male', 'FEMALE' => 'female'),
            'expanded' => true,
            'label' => 'Gender',
            'data' => isset($subscribe['person_gender']) ? $subscribe['person_gender'] : 'MALE'
        ))
        ->add('person_title', 'choice', array(
            'choices' => $this->ContactControl->getTitleArrayForTwig(),
            'empty_value' => '- please select -',
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'label' => 'Person title',
            'data' => isset($subscribe['person_title']) ? $subscribe['person_title'] : 'NO_TITLE'
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
        ));

        return $form;

    }

    /**
     * Subscribe for the given event
     *
     * @param Application $app
     * @throws \Exception
     * @return string dialog or result
     */
    public function exec(Application $app, $event_id)
    {
        $this->initParameters($app);

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
