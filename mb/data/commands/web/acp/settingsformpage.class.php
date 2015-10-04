<?php

namespace MB\Command\Web;

//Imports
use MB\Core\MB;
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Util\Message;
use MB\Form\Forms\SettingsForm;

/**
 * Einstellungen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SettingsFormPage extends PageCommand {

    protected $requiredPremission = 'mb.acp.settings';

    protected $template = 'settings.html';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'settings', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template holen
        $tpl = RWF::getTemplate();

        //Headline
        $tpl->assign('apps', MB::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', MB::getStyle());
        $tpl->assign('user', MB::getVisitor());

        //Formular erstellen
        $settingsForm = new SettingsForm();
        $settingsForm->setAction('index.php?app=mb&page=settingsform');
        $settingsForm->addId('mb-view-form-settings');

        if($settingsForm->isSubmitted() && $settingsForm->validate()) {

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

        //Formular Anzeigen
        $tpl->assign('settingsForm', $settingsForm);
    }
}