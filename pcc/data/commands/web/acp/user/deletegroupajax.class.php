<?php

namespace PCC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\User\User;
use RWF\User\UserEditor;
use RWF\User\UserGroup;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;


/**
 * loescht eine Benutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteGroupAjax extends AjaxCommand {

    protected $premission = 'pcc.acp.userManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('usermanagement');

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

        //Benutzergruppe loeschen
        $message = new Message();
        try {

            UserEditor::getInstance()->removeUserGroup($groupId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.success.deleteGroup'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1102.group.del'));
            } elseif($e->getCode() == 1111) {

                //Systemgruppe
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1113.group.del'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.group.del'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deletegroup.html');
    }

}