<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\User\UserEditor;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\UserForm;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddUserFormPage extends PageCommand {

    protected $template = 'adduserform.html';

    protected $premission = 'shc.acp.userManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'usermanagement', 'acpindex');

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
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listusers');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Formular
        $userForm = new UserForm();
        $userForm->setView(UserForm::SMARTPHONE_VIEW);
        $userForm->setAction('index.php?app=shc&m&page=adduserform');
        $userForm->addId('shc-view-form-addUser');

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
            if(!UserEditor::getInstance()->isUserNameAvailable($userForm->getElementByName('name')->getValue())) {

                $userForm->markElementAsInvalid('name',RWF::getLanguage()->get('acp.userManagement.form.error.nameNotAvailable'));
                $valid = false;
            }

            //Passwoerter pruefen
            if (!$userForm->validateByName('password')) {

                $valid = false;
            }
            if (!$userForm->validateByName('passwordCompare')) {

                $valid = false;
            }
            $pass1 = $userForm->getElementByName('password')->getValue();
            $pass2 = $userForm->getElementByName('passwordCompare')->getValue();
            if ($pass1 != '' && $pass1 != $pass2) {

                $userForm->markElementAsInvalid('password', RWF::getLanguage()->get('acp.userManagement.form.error.passwordError'));;
                $valid = false;
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

        $tpl = RWF::getTemplate();
        if(!$userForm->isSubmitted() || $valid !== true) {

            //Formular Anzeigen
            $tpl->assign('userForm', $userForm);

        } else {

            //Eingaben i.O. -> speichern
            $name = $userForm->getElementByName('name')->getValue();
            $password = $userForm->getElementByName('password')->getValue();
            $mainGroup = $userForm->getElementByName('mainGroup')->getValue();
            $userGroups = $userForm->getElementByName('userGroups')->getValues();
            $lang = $userForm->getElementByName('lang')->getValue();
            $webStyle = $userForm->getElementByName('webStyle')->getValue();

            $message = new Message();
            try {

                UserEditor::getInstance()->addUser($name, $password, $mainGroup, $userGroups, $lang, $webStyle);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.success.addUser'));
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
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&m&page=listusers');
            $this->response->setBody('');
            $this->template = '';
        }
    }

}