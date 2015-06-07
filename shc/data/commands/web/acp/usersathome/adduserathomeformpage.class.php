<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\UserAtHomeForm;
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
class AddUserAthomeFormPage extends PageCommand {

    protected $template = 'userathomeform.html';

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

        //Header Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());

        //Formular erstellen
        $userAtHomeForm = new UserAtHomeForm();
        $userAtHomeForm->setAction('index.php?app=shc&page=adduserathomeform');
        $userAtHomeForm->addId('shc-view-form-addUserAtHome');

        if($userAtHomeForm->isSubmitted() && $userAtHomeForm->validate() === true) {

            //Speichern
            $name = $userAtHomeForm->getElementByName('name')->getValue();
            $ip = $userAtHomeForm->getElementByName('ip')->getValue();
            $visibility = $userAtHomeForm->getElementByName('visibility')->getValue();
            $enabled = $userAtHomeForm->getElementByName('enabled')->getValue();

            $message = new Message();
            try {

                UserAtHomeEditor::getInstance()->addUserAtHome($name, $ip, $enabled, $visibility);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.success.addUser'));
            } catch(\Exception $e) {

                if($e->getCode() == 1507) {

                    //Name schon vergeben
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
            $this->response->addLocationHeader('index.php?app=shc&page=listusersathome');
            $this->response->setBody('');
            $this->template = '';
        } else {

            $tpl->assign('userAtHomeForm', $userAtHomeForm);
        }
    }
}