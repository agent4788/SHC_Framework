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
use SHC\Switchable\Switchables\AvmSocket;

/**
 * AVM Steckdose Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AvmSocketForm extends DefaultHtmlForm {

    /**
     * @param AvmSocket $avmSocket
     */
    public function __construct(AvmSocket $avmSocket = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Steckdose
        $name = new TextField('name', ($avmSocket instanceof AvmSocket ? $avmSocket->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($avmSocket instanceof AvmSocket ? $avmSocket->getIcon() : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Button Text
        $buttonText = new ButtonTextChooser('buttonText', ($avmSocket instanceof AvmSocket? $avmSocket->getButtonText() : ''));
        $buttonText->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.buttonText'));
        $buttonText->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.buttonText.description'));
        $buttonText->requiredField(true);
        $this->addFormElement($buttonText);

        //Raeume
        $rooms = new RoomChooser('rooms', ($avmSocket instanceof AvmSocket&& count($avmSocket->getRooms()) > 0 ? $avmSocket->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //AIN Auswahl
        $aim = new TextField('ain', ($avmSocket instanceof AvmSocket ? $avmSocket->getAin() : ''), array('minlength' => 10, 'maxlength' => 25));
        $aim->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.ain'));
        $aim->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.ain.description'));
        $aim->requiredField(true);
        $this->addFormElement($aim);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($avmSocket instanceof AvmSocket? $avmSocket->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($avmSocket instanceof AvmSocket? $avmSocket->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($avmSocket instanceof AvmSocket? $avmSocket->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}