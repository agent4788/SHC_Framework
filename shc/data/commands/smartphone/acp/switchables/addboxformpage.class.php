<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Form\Form;
use RWF\Request\Commands\PageCommand;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\BoxForm;
use SHC\View\Room\ViewHelperEditor;

/**
 * Listet die schaltbaren Elemente
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddBoxFormPage extends PageCommand {

    protected $template = 'addboxform.html';

    protected $requiredPremission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchablemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=acp');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Formular erstellen
        $boxForm = new BoxForm();
        $boxForm->setAction('index.php?app=shc&m&page=addboxform');
        $boxForm->setView(Form::SMARTPHONE_VIEW);
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

                if($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addbox.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addbox.error'));
                }
            }
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
            $this->response->setBody('');
            $this->template = '';
        } else {

            $tpl->assign('boxForm', $boxForm);
        }
    }

}