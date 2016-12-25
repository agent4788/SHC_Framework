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
use SHC\Switchable\Switchables\VirtualSocket;

/**
 * virtuelle Steckdose Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class VirtualSocketForm extends DefaultHtmlForm {

    /**
     * @param VirtualSocket $vSocket
     */
    public function __construct(VirtualSocket $vSocket = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Steckdose
        $name = new TextField('name', ($vSocket instanceof VirtualSocket ? $vSocket->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($vSocket instanceof VirtualSocket ? $vSocket->getIcon() : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Button Text
        $buttonText = new ButtonTextChooser('buttonText', ($vSocket instanceof VirtualSocket? $vSocket->getButtonText() : ''));
        $buttonText->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.buttonText'));
        $buttonText->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.buttonText.description'));
        $buttonText->requiredField(true);
        $this->addFormElement($buttonText);

        //Raeume
        $rooms = new RoomChooser('rooms', ($vSocket instanceof VirtualSocket && count($vSocket->getRooms()) > 0 ? $vSocket->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($vSocket instanceof VirtualSocket ? $vSocket->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($vSocket instanceof VirtualSocket ? $vSocket->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($vSocket instanceof AvmSocket? $vSocket->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}