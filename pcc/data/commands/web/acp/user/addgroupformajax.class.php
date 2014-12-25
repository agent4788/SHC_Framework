<?php

namespace PCC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\User\UserEditor;
use RWF\Util\Message;
use PCC\Form\Forms\UserGroupForm;

/**
 * Erstellt eine neue Benutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddGroupFormAjax extends AjaxCommand {

    protected $premission = 'pcc.acp.userManagement';

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

        //Formular erstellen
        $groupForm = new UserGroupForm();
        $groupForm->addId('pcc-view-form-addGroup');

        if(!$groupForm->isSubmitted() || ($groupForm->isSubmitted() && !$groupForm->validate() === true)) {

            $tpl->assign('groupForm', $groupForm);
        } else {

            //Speichern
            $name = $groupForm->getElementByName('name')->getValue();
            $description = $groupForm->getElementByName('description')->getValue();
            $premissions = array();
            foreach(UserEditor::getInstance()->getUserGroupById(1)->listPremissions() as $premissionName => $premissionValue) {

                $value = $groupForm->getElementByName(str_replace('.', '_', $premissionName))->getValue();
                $premissions[$premissionName] = ($value === null ? false : $value);
            }

            $message = new Message();
            try {

                UserEditor::getInstance()->addUserGroup($name, $description, $premissions);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.success.addGroup'));
            } catch(\Exception $e) {

                if($e->getCode() == 1112) {

                    //Benutzername schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1112'));
                }  elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1102.group'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.group'));
                }
            }
            $tpl->assign('message', $message);
        }

        //Template anzeigen
        $this->data = $tpl->fetchString('addgroupform.html');
    }

}