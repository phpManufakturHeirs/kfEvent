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
use phpManufaktur\Event\Data\Event\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RecurringEvent extends Backend {

    protected $EventData = null;

    protected static $event_id = null;
    protected static $recurring_type = null;
    protected static $day_type = null;
    protected static $day_sequence = null;
    protected static $week_sequence = null;
    protected static $week_days = null;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Event\Control\Backend\Backend::initialize()
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);

        $this->EventData = new Event($app);

    }

    /**
     * Get the form fields to select the recurring type a start
     *
     */
    protected function getSelectTypeFormFields()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('event_id', 'hidden', array(
            'data' => self::$event_id
        ))
        ->add('recurring_type', 'choice', array(
            'choices' => array(
                'NONE' => 'No recurring event',
                'DAY' => 'Daily recurring',
                'WEEK' => 'Weekly recurring',
                'MONTH' => 'Monthly recurring',
                'YEAR' => 'Yearly recurring'
            ),
            'expanded' => true,
            'required' => true,
            'label' => 'Select type',
            'data' => 'NONE'
        ));
    }

    /**
     * Get the form fields to select the recurring Day Type
     */
    protected function getSelectDayTypeFormFields()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('event_id', 'hidden', array(
            'data' => self::$event_id
        ))
        ->add('recurring_type', 'hidden', array(
            'data' => self::$recurring_type
        ))
        ->add('day_type', 'choice', array(
            'choices' => array(
                'DAILY' => 'Repeat each x-days',
                'WORKDAYS' => 'Repeat at workdays'
            ),
            'expanded' => true,
            'required' => true,
            'label' => 'Select type',
            'data' => 'DAILY'
        ));
    }

    /**
     * Get the form fields to input the day sequence
     */
    protected function getSelectDaySequenceFormFields()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('event_id', 'hidden', array(
            'data' => self::$event_id
        ))
        ->add('recurring_type', 'hidden', array(
            'data' => self::$recurring_type
        ))
        ->add('day_type', 'hidden', array(
            'data' => self::$day_type
        ))
        ->add('day_sequence', 'text', array(
            'label' => 'Repeat each x-days',
            'required' => true
        ));
    }

    /**
     * Get the form fields to input the week sequence
     */
    protected function getSelectWeekSequenceFormFields()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('event_id', 'hidden', array(
            'data' => self::$event_id
        ))
        ->add('recurring_type', 'hidden', array(
            'data' => self::$recurring_type
        ))
        ->add('week_sequence', 'text', array(
            'label' => 'Repeat each x-weeks',
            'required' => true
        ))
        ->add('week_days', 'choice', array(
            'choices' => array(
                'MONDAY' => 'Monday',
                'TUESDAY' => 'Tuesday',
                'WEDNESDAY' => 'Wednesday',
                'THURSDAY' => 'Thursday',
                'FRIDAY' => 'Friday',
                'SATURDAY' => 'Saturday',
                'SUNDAY' => 'Sunday'
            ),
            'expanded' => true,
            'multiple' => true,
            'required' => true,
            'label' => 'Weekdays',
            'data' => null
        ))
        ;
    }

    /**
     * Select the Day Type
     *
     * @return string
     */
    protected function selectDayType()
    {
        $fields = $this->getSelectDayTypeFormFields();
        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template', 'admin/recurring/day.type.twig'),
            array(
                'usage' => self::$usage,
                'alert' => $this->getAlert(),
                'toolbar' => $this->getToolbar('event_edit'),
                'form' => $form->createView()
            ));
    }

    /**
     * Select the Day Sequence
     */
    protected function selectDaySequence()
    {
        $fields = $this->getSelectDaySequenceFormFields();
        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template', 'admin/recurring/day.sequence.twig'),
            array(
                'usage' => self::$usage,
                'alert' => $this->getAlert(),
                'toolbar' => $this->getToolbar('event_edit'),
                'form' => $form->createView()
            ));
    }

    /**
     * Controller to check the selected Day Type
     *
     * @param Application $app
     * @return string
     */
    public function ControllerCheckDayType(Application $app)
    {
        $this->initialize($app);

        $fields = $this->getSelectDayTypeFormFields();
        $form = $fields->getForm();
        $form->bind($app['request']);

        if ($form->isValid()) {
            $data = $form->getData();
            self::$event_id = $data['event_id'];
            self::$recurring_type = $data['recurring_type'];
            self::$day_type = $data['day_type'];

            if (self::$day_type == 'WORKDAYS') {
                // save the recurring event
                return $this->selectRecurringDateEnd();
            }
            else {
                // get the daily sequence
                return $this->selectDaySequence();
            }
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->selectDayType($app);
        }

    }

    /**
     * Controller to check the Day Sequence
     *
     * @param Application $app
     * @return string
     */
    public function ControllerCheckDaySequence(Application $app)
    {
        $this->initialize($app);

        $fields = $this->getSelectDaySequenceFormFields();
        $form = $fields->getForm();
        $form->bind($app['request']);

        if ($form->isValid()) {
            $data = $form->getData();
            self::$event_id = $data['event_id'];
            self::$recurring_type = $data['recurring_type'];
            self::$day_type = $data['day_type'];
            self::$day_sequence = (int) $data['day_sequence'];
            if (self::$day_sequence < 1) {
                $this->setAlert('The daily sequence must be greater than zero!', array(), self::ALERT_TYPE_WARNING);
                return $this->selectDaySequence();
            }
            // finish the selection
            return $this->selectRecurringDateEnd();
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->selectDaySequence();
        }
    }

    /**
     * Select the week sequence
     */
    protected function selectWeekSequence()
    {
        $fields = $this->getSelectWeekSequenceFormFields();
        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template', 'admin/recurring/week.sequence.twig'),
            array(
                'usage' => self::$usage,
                'alert' => $this->getAlert(),
                'toolbar' => $this->getToolbar('event_edit'),
                'form' => $form->createView()
            ));
    }

    /**
     * Controller to check the Weekly Sequence
     *
     * @param Application $app
     * @return string
     */
    public function ControllerCheckWeekSequence(Application $app)
    {
        $this->initialize($app);

        $fields = $this->getSelectWeekSequenceFormFields();
        $form = $fields->getForm();
        $form->bind($app['request']);

        if ($form->isValid()) {
            $data = $form->getData();
            self::$event_id = $data['event_id'];
            self::$recurring_type = $data['recurring_type'];
            self::$week_sequence = (int) $data['week_sequence'];
            if (self::$week_sequence < 1) {
                $this->setAlert('The weekly sequence must be greater than zero!', array(), self::ALERT_TYPE_WARNING);
                return $this->selectWeekSequence();
            }
            // now select the week days
            return $this->selectWeekDays();
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->selectWeekSequence();
        }
    }

    /**
     * Select the Week Days
     *
     * @return string
     */
    protected function selectWeekDays()
    {
        return __METHOD__;
    }

    protected function selectMonthType()
    {
        return __METHOD__;
    }

    protected function selectYearType()
    {
        return __METHOD__;
    }

    /**
     * Get the end date of the recurring event ...
     *
     */
    protected function selectRecurringDateEnd()
    {
        return __METHOD__;
    }

    /**
     * Controller to check the recurring event type
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response|string
     */
    public function ControllerCheckType(Application $app)
    {

        $this->initialize($app);

        $fields = $this->getSelectTypeFormFields();
        $form = $fields->getForm();
        $form->bind($app['request']);

        if ($form->isValid()) {
            // the form is valid
            $data = $form->getData();
            self::$event_id = $data['event_id'];
            self::$recurring_type = $data['recurring_type'];
            if (self::$recurring_type == 'NONE') {
                // nothing to do, return to event edit dialog
                $this->setAlert('No recurring event type selected', array(), self::ALERT_TYPE_SUCCESS);
                $subRequest = Request::create('/admin/event/edit/id/'.self::$event_id, 'GET', array('usage' => self::$usage));
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            }
            switch (self::$recurring_type) {
                // select the next step
                case 'DAY':
                    return $this->selectDayType();
                case 'WEEK':
                    return $this->selectWeekSequence();
                case 'MONTH':
                    return  $this->selectMonthType();
                case 'YEAR':
                    return $this->selectYearType();
                default:
                    $this->setAlert('Do not know how to handle the recurring type <b>%type%</b>.',
                        array('%type%' => self::$recurring_type), self::ALERT_TYPE_DANGER);
                    return $this->ControllerStart($app, self::$event_id);
            }
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->ControllerStart($app, self::$event_id);
        }
    }

    /**
     * Controller to start handling recurring events
     *
     * @param Application $app
     * @param integer $event_id
     */
    public function ControllerStart(Application $app, $event_id)
    {
        $this->initialize($app);

        self::$event_id = $event_id;

        if (self::$event_id < 1) {
            // invalid event ID
            $this->setAlert('Missing a valid Event ID!', array(), self::ALERT_TYPE_DANGER);
            $subRequest = Request::create('/admin/event/list', 'GET', array('usage' => self::$usage));
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        $fields = $this->getSelectTypeFormFields();
        $form = $fields->getForm();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template', 'admin/recurring/start.twig'),
            array(
                'usage' => self::$usage,
                'alert' => $this->getAlert(),
                'toolbar' => $this->getToolbar('event_edit'),
                'form' => $form->createView(),
                'route' => '/admin'
            ));
    }
}
