<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Util\Message;
use SHC\Form\Forms\BoxForm;
use SHC\View\Room\ViewHelperEditor;

/**
 * erstellt eine neue Box
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddBoxFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'form', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Formular erstellen
        $boxForm = new BoxForm();
        $boxForm->addId('shc-view-form-addBox');

        if($boxForm->isSubmitted() && $boxForm->validate()) {

            //Speichern
            $name = $boxForm->getElementByName('name')->getValue();
            $roomId = $boxForm->getElementByName('room')->getValue();
            $orderId = ViewHelperEditor::getInstance()->getNextOrderId();

            $message = new Message();
            try {

                ViewHelperEditor::getInstance()->addBox($name, $roomId, $orderId);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addbox.success'));
            } catch(\Exception $e) {

                if($e->getCode() == 1507) {

                    //Raumname schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addbox.error.1507'));
                }  elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addbox.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addbox.error'));
                }
            }
            $tpl->assign('message', $message);
        } else {

            $tpl->assign('boxForm', $boxForm);
        }

        //Template anzeigen
        $this->data = $tpl->fetchString('addboxform.html');
    }

}