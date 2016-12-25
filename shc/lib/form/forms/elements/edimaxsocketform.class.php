<?php

namespace SHC\Form\Forms\Elements;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\ButtonTextChooser;
use SHC\Form\FormElements\EdimaxTypeChooser;
use SHC\Form\FormElements\GroupPremissonChooser;
use SHC\Form\FormElements\IconChooser;
use SHC\Form\FormElements\IpAddressInputField;
use SHC\Form\FormElements\RoomChooser;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\EdimaxSocket;

/**
 * Edimax Steckdose Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EdimaxSocketForm extends DefaultHtmlForm {

    /**
     * @param AvmSocket $edimaxSocket
     */
    public function __construct(EdimaxSocket $edimaxSocket = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Steckdose
        $name = new TextField('name', ($edimaxSocket instanceof EdimaxSocket ? $edimaxSocket->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($edimaxSocket instanceof EdimaxSocket ? $edimaxSocket->getIcon() : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Button Text
        $buttonText = new ButtonTextChooser('buttonText', ($edimaxSocket instanceof EdimaxSocket ? $edimaxSocket->getButtonText() : ''));
        $buttonText->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.buttonText'));
        $buttonText->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.buttonText.description'));
        $buttonText->requiredField(true);
        $this->addFormElement($buttonText);

        //Raeume
        $rooms = new RoomChooser('rooms', ($edimaxSocket instanceof EdimaxSocket && count($edimaxSocket->getRooms()) > 0 ? $edimaxSocket->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //IP
        $ip = new IpAddressInputField('ip', ($edimaxSocket instanceof EdimaxSocket ? $edimaxSocket->getIpAddress() : null));
        $ip->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.ip'));
        $ip->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.ip.description'));
        $ip->requiredField(true);
        $this->addFormElement($ip);

        //Typ
        $type = new EdimaxTypeChooser('type', ($edimaxSocket instanceof EdimaxSocket ? $edimaxSocket->getType() : 1));
        $type->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.type'));
        $type->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.type.description'));
        $type->requiredField(true);
        $this->addFormElement($type);

        //Username
        $username = new TextField('username', ($edimaxSocket instanceof EdimaxSocket ? $edimaxSocket->getUsername() : 'admin'), array('minlength' => 3, 'maxlength' => 40));
        $username->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.username'));
        $username->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.username.description'));
        $username->requiredField(true);
        $username->disable(true);
        $this->addFormElement($username);

        //Password
        $password = new TextField('password', ($edimaxSocket instanceof EdimaxSocket ? $edimaxSocket->getPassword() : '1234'), array('minlength' => 3, 'maxlength' => 40));
        $password->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.password'));
        $password->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.password.description'));
        $password->requiredField(true);
        $this->addFormElement($password);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($edimaxSocket instanceof EdimaxSocket? $edimaxSocket->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($edimaxSocket instanceof EdimaxSocket? $edimaxSocket->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($edimaxSocket instanceof EdimaxSocket? $edimaxSocket->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}