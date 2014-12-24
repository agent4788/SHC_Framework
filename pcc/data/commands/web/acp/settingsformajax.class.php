<?php

namespace PCC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Util\Message;
use PCC\Form\Forms\SettingsForm;

/**
 * Einstellungen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SettingsFormAjax extends AjaxCommand {

    protected $premission = 'pcc.acp.settings';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('settings', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template holen
        $tpl = RWF::getTemplate();

        //Formular erstellen
        $settingsForm = new SettingsForm();
        $settingsForm->addId('pcc-view-form-settings');

        if($settingsForm->isSubmitted() && $settingsForm->validate()) {

            //Speichern
            $settings = RWF::getSettings();

            foreach($settingsForm->listFormElements() as $formElement) {

                /* @var $formElement \RWF\Form\FormElement */
                $settings->editSetting(str_replace('_', '.', $formElement->getName()), $formElement->getValue());
            }

            $message = new Message();
            try {

                $settings->saveAndReload();
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.settings.form.success'));
            } catch(\Exception $e) {

                if($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.settings.form.error.1102'));
                } else {

                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.settings.form.error'));
                }
            }

            $tpl->assign('message', $message);
        }

        //Formular Anzeigen
        $tpl->assign('settingsForm', $settingsForm);
        $this->data = $tpl->fetchString('settings.html');
    }
}