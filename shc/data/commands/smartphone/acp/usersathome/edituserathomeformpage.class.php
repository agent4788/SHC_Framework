<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\UserAtHomeForm;
use SHC\Form\Forms\UserGroupForm;
use SHC\UserAtHome\UserAtHome;
use SHC\UserAtHome\UserAtHomeEditor;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditUserAtHomeFormPage extends PageCommand {

    protected $template = 'edituserathomeform.html';

    protected $requiredPremission = 'shc.acp.usersathomeManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'usersathomemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listusersathome');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Benutzer Objekt laden
        $userAtHomeId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $userAtHome = UserAtHomeEditor::getInstance()->getUserTaHomeById($userAtHomeId);

        //pruefen ob der Benutzer existiert
        if(!$userAtHome instanceof UserAtHome) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.usersathomeManagement.form.error.id')));
            return;
        }

        //Formular erstellen
        $userAtHomeForm = new UserAtHomeForm($userAtHome);
        $userAtHomeForm->setView(UserGroupForm::SMARTPHONE_VIEW);
        $userAtHomeForm->setAction('index.php?app=shc&m&page=edituserathomeform&id='. $userAtHome->getId());
        $userAtHomeForm->addId('shc-view-form-editUserAtHome');

        if($userAtHomeForm->isSubmitted() && $userAtHomeForm->validate() === true) {

            //Speichern
            $name = $userAtHomeForm->getElementByName('name')->getValue();
            $ip = $userAtHomeForm->getElementByName('ip')->getValue();
            $visibility = $userAtHomeForm->getElementByName('visibility')->getValue();
            $enabled = $userAtHomeForm->getElementByName('enabled')->getValue();

            $message = new Message();
            try {

                UserAtHomeEditor::getInstance()->editUserAtHome($userAtHomeId, $name, $ip, $enabled, $visibility);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.success.editUser'));
            } catch(\Exception $e) {

                if($e->getCode() == 1507) {

                    //Raumname schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error.1507'));
                }  elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error'));
                }
            }
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&m&page=listusersathome');
            $this->response->setBody('');
            $this->template = '';
        } else {

            $tpl->assign('userAtHomeForm', $userAtHomeForm);
        }
    }
}