<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\GroupPremissonChooser;
use SHC\Form\FormElements\IconChooser;
use SHC\Form\FormElements\RoomChooser;
use SHC\Form\FormElements\RPiGpioChooser;
use SHC\Form\FormElements\SwitchPointsChooser;
use SHC\Form\FormElements\SwitchServerChooser;
use SHC\Room\Room;
use SHC\Switchable\Readables\RpiGpioInput;

/**
 * RPi GPIO Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RpiGpioInputForm extends DefaultHtmlForm {

    /**
     * @param RpiGpioInput $rpiGpioInput
     */
    public function __construct(RpiGpioInput $rpiGpioInput = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des GPIO
        $name = new TextField('name', ($rpiGpioInput instanceof RpiGpioInput ? $rpiGpioInput->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Raum
        $room = new RoomChooser('room', ($rpiGpioInput instanceof RpiGpioInput && $rpiGpioInput->getRoom() instanceof Room ? $rpiGpioInput->getRoom()->getId() : null));
        $room->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.room'));
        $room->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.room.description'));
        $room->requiredField(true);
        $this->addFormElement($room);

        //Schaltserver Auswahl
        $switchServer = new SwitchServerChooser('switchServer', ($rpiGpioInput instanceof RpiGpioInput ? $rpiGpioInput->getSwitchServer() : 0));
        $switchServer->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.switchServer'));
        $switchServer->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.switchServer.description'));
        $switchServer->requiredField(true);
        $this->addFormElement($switchServer);

        //GPIO
        $gpio = new RPiGpioChooser('gpio', ($rpiGpioInput instanceof RpiGpioInput ? $rpiGpioInput->getPinNumber() : -1));
        $gpio->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.gpioPin'));
        $gpio->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.gpioPin.description'));
        $gpio->requiredField(true);
        $this->addFormElement($gpio);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($rpiGpioInput instanceof RpiGpioInput ? $rpiGpioInput->isEnabled() : false));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($rpiGpioInput instanceof RpiGpioInput ? $rpiGpioInput->isVisible() : false));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($rpiGpioInput instanceof RpiGpioInput ? $rpiGpioInput->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}