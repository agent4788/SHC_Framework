<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Util\Message;
use SHC\Form\Forms\UserAtHomeForm;
use SHC\UserAtHome\UserAtHomeEditor;

/**
 * erstellt einen neuen Benutzer zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddUserAtHomeFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.usersathomeManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('usersathomemanagement', 'form', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Formular erstellen
        $userAtHomeForm = new UserAtHomeForm();
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
            $tpl->assign('message', $message);
        } else {

            $tpl->assign('userAtHomeForm', $userAtHomeForm);
        }

        //Template anzeigen
        $this->data = $tpl->fetchString('adduserathomeform.html');
    }

}