<?php

namespace SHC\Form\Forms\Elements;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\GroupPremissonChooser;
use SHC\Form\FormElements\IpAddressInputField;
use SHC\Form\FormElements\MacAddressInputField;
use SHC\Form\FormElements\RoomChooser;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * Wake On Lan Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class WolForm extends DefaultHtmlForm {

    /**
     * @param WakeOnLan $wakeOnLan
     */
    public function __construct(WakeOnLan $wakeOnLan = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des GPIO
        $name = new TextField('name', ($wakeOnLan instanceof WakeOnLan ? $wakeOnLan->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Raeume
        $rooms = new RoomChooser('rooms', ($wakeOnLan instanceof WakeOnLan && count($wakeOnLan->getRooms()) > 0 ? $wakeOnLan->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //MAC Adresse
        $mac = new MacAddressInputField('mac', ($wakeOnLan instanceof WakeOnLan ? $wakeOnLan->getMac() : null));
        $mac->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.mac'));
        $mac->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.mac.description'));
        $mac->requiredField(true);
        $this->addFormElement($mac);

        //IP Adresse
        $ip = new IpAddressInputField('ip', ($wakeOnLan instanceof WakeOnLan ? $wakeOnLan->getIpAddress() : null));
        $ip->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.ip'));
        $ip->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.ip.description'));
        $ip->requiredField(true);
        $this->addFormElement($ip);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($wakeOnLan instanceof WakeOnLan ? $wakeOnLan->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($wakeOnLan instanceof WakeOnLan ? $wakeOnLan->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($wakeOnLan instanceof WakeOnLan ? $wakeOnLan->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}