<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\SwitchServerForm;
use SHC\SwitchServer\SwitchServer;
use SHC\SwitchServer\SwitchServerEditor;

/**
 * bearbeitet einen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditSwitchServerFormPage extends PageCommand {

    protected $template = 'switchserverform.html';

    protected $requiredPremission = 'shc.acp.switchserverManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchservermanagement', 'acpindex');

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

        //Schaltserver Objekt laden
        $switchServerId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchServer = SwitchServerEditor::getInstance()->getSwitchServerById($switchServerId);

        //pruefen ob der Benutzer existiert
        if(!$switchServer instanceof SwitchServer) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchserverManagement.form.error.id')));
            return;
        }
        
        //Formular erstellen
        $switchServerForm = new SwitchServerForm($switchServer);
        $switchServerForm->setAction('index.php?app=shc&page=editswitchserverform&id='. $switchServer->getId());
        $switchServerForm->addId('shc-view-form-addSwitchServer');

        if($switchServerForm->isSubmitted() && $switchServerForm->validate() === true) {

            //Speichern
            $name = $switchServerForm->getElementByName('name')->getValue();
            $ip = $switchServerForm->getElementByName('ip')->getValue();
            $port = $switchServerForm->getElementByName('port')->getValue();
            $timeout = $switchServerForm->getElementByName('timeout')->getValue();
            $model = $switchServerForm->getElementByName('model')->getValue();
            $radioSockets = $switchServerForm->getElementByName('radioSockets')->getValue();
            $readGPIO = $switchServerForm->getElementByName('readGPIO')->getValue();
            $writeGPIO = $switchServerForm->getElementByName('writeGPIO')->getValue();
            $enabled = $switchServerForm->getElementByName('enabled')->getValue();

            $message = new Message();
            try {

                SwitchServerEditor::getInstance()->editSwitchServer($switchServerId, $name, $ip, $port, $timeout, $model, $radioSockets, $writeGPIO, $readGPIO, $enabled);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.success.addSwitchServer'));
            } catch(\Exception $e) {

                if($e->getCode() == 1501) {

                    //Name schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.error.1501'));
                }  elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.error'));
                }
            }
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&page=listswitchservers');
            $this->response->setBody('');
            $this->template = '';
        } else {

            $tpl->assign('switchServerForm', $switchServerForm);
        }
    }

}