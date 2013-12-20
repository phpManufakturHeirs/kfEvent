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
use phpManufaktur\Event\Control\Backend\Backend;
use phpManufaktur\Event\Data\Event\EventSearch;
use phpManufaktur\Event\Data\Event\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class EventCopy extends Backend
{
    protected static $columns = null;
    protected static $route = null;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Event\Control\Backend\Backend::initialize()
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);
        try {
            // search for the config file in the template directory
            $cfg_file = $this->app['utils']->getTemplateFile('@phpManufaktur/Event/Template', 'backend/event.list.json', '', true);
            // get the columns to show in the list
            $cfg = $this->app['utils']->readJSON($cfg_file);
            self::$columns = isset($cfg['columns']) ? $cfg['columns'] : $this->EventData->getColumns();
        } catch (\Exception $e) {
            // the config file does not exists - use all available columns
            self::$columns = $this->EventData->getColumns();
        }
        self::$route =  array(
            'start' => '/admin/event/copy?usaage='.self::$usage,
            'select' => '/admin/event/copy/id/{event_id}?usage='.self::$usage,
            'check' => '/admin/event/copy/search/check?usage='.self::$usage
        );
    }

    /**
     * Get the search form fields
     *
     * @return form.factory
     */
    protected function getSearchFormFields()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('search', 'text' );
    }

    /**
     * Controller to copy a event from another
     *
     * @param Application $app
     * @return string
     */
    public function controllerCopyEvent(Application $app)
    {
        $this->initialize($app);

        $fields = $this->getSearchFormFields();
        $form = $fields->getForm();

        return $app['twig']->render($app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template', 'backend/bootstrap/event.copy.start.twig'),
            array(
                'usage' => self::$usage,
                'toolbar' => $this->getToolbar('event_edit'),
                'message' => array(
                    'type' => $this->getMessageType(),
                    'content' => $this->getMessage()
                ),
                'form' => $form->createView(),
                'route' => self::$route
            ));
    }

    /**
     * Check the search term, display result list or again the search dialog
     *
     * @param Application $app
     */
    public function controllerSearchCheck(Application $app)
    {
        $this->initialize($app);

        // get the form
        $fields = $this->getSearchFormFields();
        $form = $fields->getForm();
        // get the requested data
        $form->bind($this->app['request']);

        if ($form->isValid()) {
            // the form is valid
            $search = $form->getData();

            $EventSearch = new EventSearch($app);
            if (false === ($events = $EventSearch->search($search['search']))) {
                // no hits, return to the search dialog
                $this->setMessage('No hits for the search term <i>%search%</i>!', array('%search%' => $search['search']));
                return $this->controllerCopyEvent($app);
            }

            $this->setMessage('%count% hits for the search term </i>%search%</i>.',
                array('%count%' => count($events), '%search%' => $search['search']));
            $this->setMessageType('success');

            return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                '@phpManufaktur/Event/Template', 'backend/bootstrap/event.copy.list.twig'),
                array(
                    'usage' => self::$usage,
                    'toolbar' => $this->getToolbar('event_edit'),
                    'message' => array(
                        'content' => $this->getMessage(),
                        'type' => $this->getMessageType()
                    ),
                    'events' => $events,
                    'columns' => self::$columns,
                    'route' => self::$route,
                    'form' => $form->createView()
                ));
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setMessage('The form is not valid, please check your input and try again!');
            $this->setMessageType('warning');
            return $this->controllerCopyEvent($app);
        }
    }

    /**
     * Controller for the selected Event ID to copy the new event from
     *
     * @param Application $app
     * @param integer $event_id
     * @return string
     */
    public function controllerCopyID(Application $app, $event_id)
    {
        $this->initialize($app);

        $EventData = new Event($app);
        if (false === ($event = $EventData->selectEvent($event_id, false))) {
            // Ooops, ID does not exists!
            $this->setMessage('The record with the ID %id% does not exists!', array('%id%' => $event_id));
            $this->setMessageType('danger');
            return $this->controllerCopyEvent($app);
        }

        // unset not needed fields
        unset($event['contact']);
        unset($event['event_id']);
        unset($event['participants']);
        unset($event['rating']);

        // change the status to LOCKED
        $event['event_status'] = 'LOCKED';

        $new_event_id = -1;
        $EventData->insertEvent($event, $new_event_id, true);

        // the request method must be GET not POST!
        $subRequest = Request::create("/admin/event/edit/id/$new_event_id", 'GET', array(
            'usage' => self::$usage,
            'message' => $app['translator']->trans('This event was copied from the event with the ID %id%. Be aware that you should change the dates before publishing to avoid duplicate events!',
                array('%id%' => $event_id)),
            'message_type' => 'success'
        ));
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}
