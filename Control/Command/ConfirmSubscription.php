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
use phpManufaktur\Event\Data\Event\Subscription;
use phpManufaktur\Contact\Data\Contact\Contact;
use phpManufaktur\Contact\Data\Contact\Overview;
use phpManufaktur\Contact\Data\Contact\Protocol;
use phpManufaktur\Basic\Data\Security\Users;

class ConfirmSubscription extends Basic
{
    protected static $guid = null;
    protected $Message = null;
    protected static $config = null;
    protected $ContactData = null;
    protected $ContactOverview = null;
    protected $ContactProtocol = null;
    protected $Users = null;

    protected function initParameters(Application $app, $parameter_id=-1)
    {
        // init parent
        parent::initParameters($app);

        // check the permanent link
        self::$config = $app['utils']->readConfiguration(MANUFAKTUR_PATH.'/Event/config.event.json');
        if (!isset(self::$config['permalink']['cms']['url']) || empty(self::$config['permalink']['cms']['url'])) {
            throw new \Exception('Missing the permanent link definition in config.event.json!');
        }

        // set the permanent link as CMS page URI!
        $this->setCMSpageURL(self::$config['permalink']['cms']['url']);
        // redirect to itself
        $this->setRedirectRoute('/event/subscribe/guid/'.self::$guid);
        // activate redirection
        $this->setRedirectActive(true);

        $this->Message = new Message($app, $this->getParameterID());
        $this->ContactData = new Contact($app);
        $this->ContactOverview = new Overview($app);
        $this->ContactProtocol = new Protocol($app);
        $this->Users = new Users($app);
    }

    public function exec(Application $app, $guid)
    {
        // set the GUID
        self::$guid = $guid;

        // init parent
        $this->initParameters($app);

        $SubscriptionData = new Subscription($app);
        if (false === ($subscription = $SubscriptionData->selectGUID($guid))) {
            $message = 'The submitted GUID %guid% does not exists.';
            return $this->Message->render($message, array('%guid%' => self::$guid), 'Checking the GUID identifier', array(), true);
        }

        // check the contact?
        if (self::$config['contact']['confirm']['double_opt_in']) {
            if (false === ($contact = $this->ContactData->select($subscription['contact_id']))) {
                // the contact ID does not exists
                throw new \Exception("[ConfirmSubscription] The contact ID {$subscription['contact_id']} does not exists!");
            }
            if ($contact['contact_type'] != 'PERSON') {
                // at the moment we have support only for PERSONs
                throw new \Exception('[ConfirmSubscription] At the time there are only contacts of type PERSON supported!');
            }
            if ($contact['contact_status'] == 'PENDING') {
                try {
                    $data = array(
                        'contact_status' => 'ACTIVE'
                    );
                    $app['db']->beginTransaction();
                    // update the contact data
                    $this->ContactData->update($data, $subscription['contact_id']);
                    // update the overview
                    $this->ContactOverview->refresh($subscription['contact_id']);
                    // add info to protocol
                    $this->ContactProtocol->addInfo($subscription['contact_id'], 'The contact activated by confirmation link.');

                    // add contact to the framework users
                    if (!$this->Users->existsUser($contact['contact_login'])) {
                        $password = $app['utils']->createPassword();
echo "$password<br>";
                        $data = array(
                            'username' => $contact['contact_login'],
                            'email' => $contact['contact_login'],
                            'password' => $this->Users->encodePassword($password),
                            'displayname' => $contact['contact_name'],
                            'roles' => 'EVENT_USER'
                        );
                        $this->Users->insertUser($data);
                    }
                    else {
                        $user = $this->Users->selectUser($contact['contact_login']);
                        $roles = explode(',', $user['roles']);
                        if (!in_array('EVENT_USER', $roles)) {
                            $roles[] = 'EVENT_USER';
                            $data = array(
                                'roles' => implode(',', $roles)
                            );
                            $this->Users->updateUser($contact['contact_login'], $data);
                        }
                    }
                    // commit the transaction
                    $app['db']->commit();
                } catch (\Exception $e) {
                    $app['db']->rollback();
                    throw new \Exception($e);
                }
            }
            elseif ($contact['contact_status'] != 'ACTIVE') {
                // contact status is neither PENDING nor ACTIVE, perhaps a problem?
                $message = 'The status for the contact with the ID %contact_id% is ambiguous, the program can not activate the account. Please contact the <a href="%email%">webmaster</a>.';
                return $this->Message->render($message, array('%contact_id%' => $contact['contact_id'], '%email%' => SERVER_EMAIL_ADDRESS), 'Checking the GUID identifier', array(), true);
            }
            else {
                // nothing to do, contact is already active
            }
        }

echo "<pre>";
        print_r($subscription);
        echo "</pre>";

        return $app['twig']->render($app['utils']->templateFile(
            '@phpManufaktur/Event/Template',
            'command/subscribe.guid.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
            ));
    }
}
