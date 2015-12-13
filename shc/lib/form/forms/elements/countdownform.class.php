<?php

namespace SHC\Form\Forms\Elements;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Core\Exception\AssertException;
use SHC\Core\SHC;
use SHC\Form\FormElements\ButtonTextChooser;
use SHC\Form\FormElements\GroupPremissonChooser;
use SHC\Form\FormElements\IconChooser;
use SHC\Form\FormElements\RoomChooser;
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
        $name = new TextField('name', ($countdown instanceof Countdown ? $countdown->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
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

        //Button Text
        $buttonText = new ButtonTextChooser('buttonText', ($countdown instanceof Countdown ? $countdown->getButtonText() : ''));
        $buttonText->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.buttonText'));
        $buttonText->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.buttonText.description'));
        $buttonText->requiredField(true);
        $this->addFormElement($buttonText);

        //Raeume
        $rooms = new RoomChooser('rooms', ($countdown instanceof Countdown && count($countdown->getRooms()) > 0 ? $countdown->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Intervall
        switch(SHC::getSetting('shc.shedulerDaemon.performanceProfile')) {

            case 1:

                //fast
                $min = 5;
                break;
            case 2:

                //default
                $min = 30;
                break;
            case 3:

                //slow
                $min = 60;
                break;
            default:

                throw new AssertException("Die Einstellung 'shc.shedulerDaemon.performanceProfile' ist Fehlerhaft");
        }
        $interval = new IntegerInputField('interval', ($countdown instanceof Countdown ? $countdown->getInterval() : 30), array('min' => $min, 'max' => 14400, 'step' => 5));
        $interval->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.interval'));
        $interval->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.interval.description'));
        $interval->requiredField(true);
        $this->addFormElement($interval);

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