<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\SwitchServer\SwitchServer;
use SHC\SwitchServer\SwitchServerEditor;
use SHC\UserAtHome\UserAtHome;
use SHC\UserAtHome\UserAtHomeEditor;


/**
 * loescht einen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteSwitchServerAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchserverManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchservermanagement');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Schaltserver Objekt laden
        $switchServerId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchServer = SwitchServerEditor::getInstance()->getSwitchServerById($switchServerId);

        //pruefen ob der Benutzer existiert
        if(!$switchServer instanceof SwitchServer) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchserverManagement.form.error.id')));
            $this->data = $tpl->fetchString('editswitchserverform.html');
            return;
        }

        //Schaltserver loeschen
        $message = new Message();
        try {

            SwitchServerEditor::getInstance()->removeSwitchServer($switchServerId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.success.deleteSwitchServer'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.error.1102.del'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.error.del'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deleteswitchserver.html');
    }

}