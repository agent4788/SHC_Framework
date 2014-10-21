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
use SHC\Switchable\Switchables\Activity;

/**
 * Aktivität Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ActivityForm extends DefaultHtmlForm {

    /**
     * @param Activity $activity
     */
    public function __construct(Activity $activity = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($activity instanceof Activity ? $activity->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($activity instanceof Activity ? $activity->getIcon() : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raum
        $room = new RoomChooser('room', ($activity instanceof Activity && $activity->getRoom() instanceof Room ? $activity->getRoom()->getId() : null));
        $room->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.room'));
        $room->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.room.description'));
        $room->requiredField(true);
        $this->addFormElement($room);

        //Schaltpunkte Auswahl
        $switchPoints = new SwitchPointsChooser('switchPoints', ($activity instanceof Activity ? $activity->listSwitchPoints() : array()));
        $switchPoints->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.switchPoints'));
        $switchPoints->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.switchPoints.description'));
        $switchPoints->requiredField(true);
        $this->addFormElement($switchPoints);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($activity instanceof Activity ? $activity->isEnabled() : false));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($activity instanceof Activity ? $activity->isVisible() : false));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //erlaubte Benutzer
        $allowedUsers = new GroupPremissonChooser('allowedUsers', ($activity instanceof Activity ? $activity->listAllowedUserGroups() : array()));
        $allowedUsers->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.allowedUsers'));
        $allowedUsers->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.allowedUsers.description'));
        $allowedUsers->requiredField(true);
        $this->addFormElement($allowedUsers);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}