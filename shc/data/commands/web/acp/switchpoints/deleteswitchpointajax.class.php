<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Room\Room;
use SHC\Room\RoomEditor;
use SHC\Timer\SwitchPoint;
use SHC\Timer\SwitchPointEditor;


/**
 * loescht einen Schaltpunkt
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteSwitchPointAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchpointsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchpointsmanagment');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Schaltpunkt Objekt laden
        $switchPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchPoint = SwitchPointEditor::getInstance()->getSwitchPointById($switchPointId);

        //pruefen ob der Schaltpunkt existiert
        if (!$switchPoint instanceof SwitchPoint) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchpointsManagment.form.error.id')));
            $this->data = $tpl->fetchString('editswitchpointform.html');
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            SwitchPointEditor::getInstance()->removeSwitchPoint($switchPointId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.success.delete'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.error.1102.del'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.error.del'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deleteswitchpoint.html');
    }

}