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
use SHC\Form\FormElements\SwitchPointsChooser;
use SHC\Room\Room;
use SHC\Switchable\Switchables\Countdown;

/**
 * Countdown Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class CountdownForm extends DefaultHtmlForm {

    /**
     * @param Countdown $countdown
     */
    public function __construct(Countdown $countdown = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($countdown instanceof Countdown ? $countdown->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($countdown instanceof Countdown ? $countdown->getIcon() : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raum
        $room = new RoomChooser('room', ($countdown instanceof Countdown && $countdown->getRoom() instanceof Room ? $countdown->getRoom()->getId() : null));
        $room->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.room'));
        $room->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.room.description'));
        $room->requiredField(true);
        $this->addFormElement($room);

        //Intervall
        $interval = new TextField('interval', ($countdown instanceof Countdown ? 'To Do' : null));
        $interval->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.interval'));
        $interval->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.interval.description'));
        $interval->requiredField(true);
        $this->addFormElement($interval);

        //Schaltpunkte Auswahl
        $switchPoints = new SwitchPointsChooser('switchPoints', ($countdown instanceof Countdown ? $countdown->listSwitchPoints() : array()));
        $switchPoints->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.switchPoints'));
        $switchPoints->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.switchPoints.description'));
        $switchPoints->requiredField(true);
        $this->addFormElement($switchPoints);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($countdown instanceof Countdown ? $countdown->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($countdown instanceof Countdown ? $countdown->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($countdown instanceof Countdown ? $countdown->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}