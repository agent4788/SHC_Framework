<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\User\User;
use RWF\User\UserEditor;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use RWF\XML\Exception\XmlException;
use SHC\Form\Forms\UserForm;


/**
 * Formular zum bearbeiten eines neuen Benutzers
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditUserFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.userManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('usermanagement', 'form');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Benutzer Objekt laden
        $userId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $user = UserEditor::getInstance()->getUserById($userId);

        //pruefen ob der Benutzer existiert
        if(!$user instanceof User) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.userManagement.form.error.id')));
            $this->data = $tpl->fetchString('adduserform.html');
            return;
        }

        //Formular erstellen
        $userForm = new UserForm($user);
        $userForm->addId('shc-view-form-editUser');
        $userForm->setDescription(RWF::getLanguage()->get('acp.userManagement.form.user.editDescription'));

        //Eingaben pruefen
        $valid = true;
        if($userForm->isSubmitted()) {

            //Benutzername
            if(!$userForm->validateByName('name')) {

                $valid = false;
            }
            if (!preg_match('#^[a-z0-9\#\_\!\-\.\,\;\+\*\?]{3,25}$#i', $userForm->getElementByName('name')->getValue())) {

                $userForm->markElementAsInvalid('name', RWF::getLanguage()->get('acp.userManagement.form.error.invalidName'));
                $valid = false;
            }
            if($user->getName() != $userForm->getElementByName('name')->getValue() && !UserEditor::getInstance()->isUserNameAvailable($userForm->getElementByName('name')->getValue())) {

                $userForm->markElementAsInvalid('name',RWF::getLanguage()->get('acp.userManagement.form.error.nameNotAvailable'));
                $valid = false;
            }

            //Passwoerter pruefen
            $pass1 = $userForm->getElementByName('password')->getValue();
            $pass2 = $userForm->getElementByName('passwordCompare')->getValue();
            if($pass1 != '' || $pass2 != '') {

                if (!$userForm->validateByName('password')) {

                    $valid = false;
                }
                if (!$userForm->validateByName('passwordCompare')) {

                    $valid = false;
                }
                if ($pass1 != '' && $pass1 != $pass2) {

                    $userForm->markElementAsInvalid('password', RWF::getLanguage()->get('acp.userManagement.form.error.passwordError'));;
                    $valid = false;
                }
            }

            //Benutzergruppen
            if(!$userForm->validateByName('mainGroup')) {

                $valid = false;
            }
            if(!$userForm->validateByName('userGroups')) {

                $valid = false;
            }

            //Sprache
            if(!$userForm->validateByName('lang')) {

                $valid = false;
            }
        }

        if(!$userForm->isSubmitted() || $valid !== true) {

            //Formular Anzeigen
            $tpl->assign('user', $user);
            $tpl->assign('userForm', $userForm);

        } else {

            //Eingaben i.O. -> speichern
            $name = $userForm->getElementByName('name')->getValue();
            if($userForm->getElementByName('password')->getValue() != '') {

                $password = $userForm->getElementByName('password')->getValue();
            } else {

                $password = null;
            }
            $mainGroup = $userForm->getElementByName('mainGroup')->getValue();
            $userGroups = $userForm->getElementByName('userGroups')->getValues();
            $lang = $userForm->getElementByName('lang')->getValue();

            $message = new Message();
            try {

                UserEditor::getInstance()->editUser($userId, $name, $password, $mainGroup, $userGroups, $lang);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.success.editUser'));
            } catch(\Exception $e) {

                if($e->getCode() == 1110) {

                    //Benutzername schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1110'));
                } elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error'));
                }
            }
            $tpl->assign('message', $message);
        }
        $this->data = $tpl->fetchString('edituserform.html');
    }

}