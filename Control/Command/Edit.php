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

use phpManufaktur\Basic\Control\kitCommand\Basic;
use Silex\Application;
use phpManufaktur\Basic\Control\Account\Account;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

class Edit extends Basic
{
    public function controllerEditEvent(Application $app)
    {
        return '<a href="'.FRAMEWORK_URL.'/logout?pid='.$this->getParameterID().'&content='.urlencode('<p>Bääääh!</p>').'">logout</a> edit event';
    }

    public function controllerLoginCheck(Application $app)
    {
        return 'login check';
    }

    public function controllerLogin(Application $app)
    {
        $this->initParameters($app);

        $fields = $app['form.factory']->createBuilder('form')
        ->add('event_id', 'hidden', array(
            'data' => $app['request']->request->get('event_id', -1)
        ))
        ->add('redirect', 'hidden', array(
            'data' => $app['request']->request->get('redirect')
        ))
        ->add('login', 'text')
        ->add('password', 'password')
        ;
        $form = $fields->getForm();

        return $app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Event/Template',
            'command/event.edit.login.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView(),
            ));
    }

    public function controllerCheck(Application $app, $event_id, $redirect)
    {
        $this->initParameters($app);

        $Account = new Account($app);


        if ($Account->isAuthenticated()) {
            if ($this->app['security']->isGranted('ROLE_ADMIN')) {
                $subRequest = Request::create('/admin/event/frontend/edit', 'POST',
                    array('event_id' => $event_id, 'redirect' => $redirect));
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            }
            else {
                $Message = new Message($app);
                return $Message->render('You are authenticated but not allowed to edit this event. Please contact the admin if you are of the mind that you should be able for.',
                    array(), 'Edit event', array(), true);
            }

        }
        else {
            $subRequest = Request::create('/event/frontend/login', 'POST',
                    array('event_id' => $event_id, 'redirect' => $redirect));
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

    }
}
