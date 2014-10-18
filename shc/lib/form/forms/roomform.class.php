<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\GroupPremissonChooser;
use SHC\Room\Room;

/**
 * Benutzergruppen Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RoomForm extends DefaultHtmlForm {

    /**
     * @param Room $room
     */
    public function __construct(Room $room = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Raumname
        $name = new TextField('name', ($room instanceof Room ? $room->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.roomManagement.form.room.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.roomManagement.form.room.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($room instanceof Room ? $room->isEnabled() : false));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.roomManagement.form.room.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.roomManagement.form.room.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($room instanceof Room ? $room->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.roomManagement.form.room.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.roomManagement.form.room.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}