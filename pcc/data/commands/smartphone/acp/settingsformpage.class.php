<?php

namespace PCC\Command\Smartphone;

//Imports
use PCC\Core\PCC;
use PCC\Form\Forms\SettingsForm;
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Util\Message;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SettingsFormPage extends PageCommand {

    protected $template = 'settingsform.html';

    protected $requiredPremission = 'pcc.acp.settings';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'acpindex', 'settings');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = PCC::getTemplate();

        //Headline Daten
        $tpl->assign('apps', PCC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', PCC::getStyle());
        $tpl->assign('user', PCC::getVisitor());
        $tpl->assign('device', PCC_DETECTED_DEVICE);
        $tpl->assign('backLink', 'index.php?app=pcc&m&page=acp');

        //Formular erstellen
        $settingsForm = new SettingsForm();
        $settingsForm->setView(SettingsForm::SMARTPHONE_VIEW);
        $settingsForm->setAction('index.php?app=shc&m&page=settingsform');
        $settingsForm->addId('pcc-view-form-settings');

        if($settingsForm->isSubmitted() && $settingsForm->validate() === true) {

            //Speichern
            $settings = RWF::getSettings();

            foreach($settingsForm->listFormElements() as $formElement) {

                /* @var $formElement \RWF\Form\FormElement */
                $settings->editSetting(str_replace('_', '.', $formElement->getName()), $formElement->getValue());
            }

            $message = new Message();
            try {

                $settings->reloadSettings();
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

        //Formular anzeigen
        $tpl->assign('settingsForm', $settingsForm);
    }
}