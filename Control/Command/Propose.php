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

class Propose extends Basic
{


    /**
     * Controller check the submitted group
     * @param Application $app
     */
    public function controllerSelectOrganizer(Application $app)
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



        return 'select organizer: '.$group_id;
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
                'form' => $form->createView()
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

        if (!isset($parameter['group'])) {
            // must first select a group
            return $this->controllerSelectGroup($app);
        }

        // select the organizer for the proposed event
        return $this->controllerSelectOrganizer($app);
    }

}
