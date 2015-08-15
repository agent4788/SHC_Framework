<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\SettingsForm;

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

    protected $premission = 'shc.acp.settings';

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

        $tpl = RWF::getTemplate();

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=acp');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Formular erstellen
        $settingsForm = new SettingsForm();
        $settingsForm->setView(SettingsForm::SMARTPHONE_VIEW);
        $settingsForm->setAction('index.php?app=shc&m&page=settingsform');
        $settingsForm->addId('shc-view-form-settings');

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