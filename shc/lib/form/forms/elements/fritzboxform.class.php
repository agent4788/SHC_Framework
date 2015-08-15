<?php

namespace SHC\Form\Forms\Elements;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\Select;
use SHC\Form\FormElements\GroupPremissonChooser;
use SHC\Form\FormElements\RoomChooser;
use SHC\Switchable\Switchables\FritzBox;

/**
 * Frtz!Box Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxForm extends DefaultHtmlForm {

    /**
     * @param FritzBox $fritzBox
     */
    public function __construct(FritzBox $fritzBox = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Funktionen
        $function = new Select('function');
        $function->setValues(array(
            FritzBox::FB_SWITCH_WLAN_2GHz => array(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.wlan1'), ($fritzBox instanceof FritzBox && $fritzBox->getFunction() == FritzBox::FB_SWITCH_WLAN_2GHz ? 1 : 0)),
            FritzBox::FB_SWITCH_WLAN_5GHz => array(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.wlan2'), ($fritzBox instanceof FritzBox && $fritzBox->getFunction() == FritzBox::FB_SWITCH_WLAN_5GHz ? 1 : 0)),
            FritzBox::FB_SWITCH_WLAN_Guest => array(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.wlan3'), ($fritzBox instanceof FritzBox && $fritzBox->getFunction() == FritzBox::FB_SWITCH_WLAN_Guest ? 1 : 0)),
            FritzBox::FB_RECONNECT_WAN => array(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.reconnect'), ($fritzBox instanceof FritzBox && $fritzBox->getFunction() == FritzBox::FB_RECONNECT_WAN ? 1 : 0)),
            FritzBox::FB_REBOOT => array(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.reboot'), ($fritzBox instanceof FritzBox && $fritzBox->getFunction() == FritzBox::FB_REBOOT ? 1 : 0))
        ));
        $function->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function'));
        $function->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.description'));
        $function->requiredField(true);
        $this->addFormElement($function);

        //Raeume
        $rooms = new RoomChooser('rooms', ($fritzBox instanceof FritzBox && count($fritzBox->getRooms()) > 0 ? $fritzBox->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($fritzBox instanceof FritzBox ? $fritzBox->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($fritzBox instanceof FritzBox ? $fritzBox->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($fritzBox instanceof FritzBox ? $fritzBox->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}