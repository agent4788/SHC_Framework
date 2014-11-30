<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Util\Message;
use SHC\Form\Forms\SwitchServerForm;
use SHC\SwitchServer\SwitchServerEditor;

/**
 * erstellt einen neuen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddSwitchServerFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchserverManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchservermanagement', 'form', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Formular erstellen
        $switchServerForm = new SwitchServerForm();
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

                SwitchServerEditor::getInstance()->addSwitchServer($name, $ip, $port, $timeout, $model, $radioSockets, $writeGPIO, $readGPIO, $enabled);
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
            $tpl->assign('message', $message);
        } else {

            $tpl->assign('switchServerForm', $switchServerForm);
        }

        //Template anzeigen
        $this->data = $tpl->fetchString('addswitchserverform.html');
    }

}