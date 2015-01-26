<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\GroupPremissonChooser;
use SHC\Form\FormElements\RoomChooser;
use SHC\Room\Room;
use SHC\Switchable\Switchables\Shutdown;

/**
 * Funksteckdose Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ShutdownForm extends DefaultHtmlForm {

    /**
     * @param Shutdown $shutdown
     */
    public function __construct(Shutdown $shutdown = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Funksteckdose
        $name = new TextField('name', ($shutdown instanceof Shutdown ? $shutdown->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Raum
        $room = new RoomChooser('room', ($shutdown instanceof Shutdown && $shutdown->getRoom() instanceof Room ? $shutdown->getRoom()->getId() : null));
        $room->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.room'));
        $room->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.room.description'));
        $room->requiredField(true);
        $this->addFormElement($room);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($shutdown instanceof Shutdown ? $shutdown->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($shutdown instanceof Shutdown ? $shutdown->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($shutdown instanceof Shutdown ? $shutdown->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}