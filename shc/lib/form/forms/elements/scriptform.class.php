<?php

namespace SHC\Form\Forms\Elements;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\ButtonTextChooser;
use SHC\Form\FormElements\GroupPremissonChooser;
use SHC\Form\FormElements\IconChooser;
use SHC\Form\FormElements\RoomChooser;
use SHC\Switchable\Switchables\Script;

/**
 * Funksteckdose Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ScriptForm extends DefaultHtmlForm {

    /**
     * @param Script $script
     */
    public function __construct(Script $script = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Funksteckdose
        $name = new TextField('name', ($script instanceof Script ? $script->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($script instanceof Script ? $script->getIcon() : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Button Text
        $buttonText = new ButtonTextChooser('buttonText', ($script instanceof Script ? $script->getButtonText() : ''));
        $buttonText->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.buttonText'));
        $buttonText->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.buttonText.description'));
        $buttonText->requiredField(true);
        $this->addFormElement($buttonText);

        //Raeume
        $rooms = new RoomChooser('rooms', ($script instanceof Script && count($script->getRooms()) > 0 ? $script->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //An Kommando
        $onCommand = new TextField('onCommand', ($script instanceof Script ? $script->getOnCommand() : ''), array('maxlength' => 255));
        $onCommand->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.onCommand'));
        $onCommand->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.onCommand.description'));
        $this->addFormElement($onCommand);

        //Aus Kommando
        $offCommand = new TextField('offCommand', ($script instanceof Script ? $script->getOffCommand() : ''), array('maxlength' => 255));
        $offCommand->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.offCommand'));
        $offCommand->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.offCommand.description'));
        $this->addFormElement($offCommand);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($script instanceof Script ? $script->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($script instanceof Script ? $script->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($script instanceof Script ? $script->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}