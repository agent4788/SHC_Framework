<?php

namespace PCC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\User\UserEditor;
use RWF\User\UserGroup;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use PCC\Form\Forms\UserGroupForm;

/**
 * Bearbeitet eine Benutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditGroupFormAjax extends AjaxCommand {

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

        //Gruppen Objekt laden
        $groupId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $group = UserEditor::getInstance()->getUserGroupById($groupId);

        //pruefen ob die Benutzergruppe existiert
        if(!$group instanceof UserGroup) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.userManagement.form.error.id.group')));
            $this->data = $tpl->fetchString('editgroupform.html');
            return;
        }

        //Formular erstellen
        $groupForm = new UserGroupForm($group);
        $groupForm->addId('pcc-view-form-editGroup');

        if(!$groupForm->isSubmitted() || ($groupForm->isSubmitted() && !$groupForm->validate() === true)) {

            $tpl->assign('group', $group);
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

                UserEditor::getInstance()->editUserGroup($groupId, $name, $description, $premissions);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.success.editGroup'));
            } catch(\Exception $e) {

                if($e->getCode() == 1112) {

                    //Gruppenname schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1112.group'));
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
        $this->data = $tpl->fetchString('editgroupform.html');
    }

}