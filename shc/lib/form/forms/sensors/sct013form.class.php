<?php

namespace SHC\Form\Forms\Sensors;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\IconChooser;
use SHC\Form\FormElements\RoomChooser;
use SHC\Sensor\Sensors\SCT013;

/**
 * SCT-013 Sensor Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SCT013Form extends DefaultHtmlForm {

    /**
     * @param SCT013 $sensor
     */
    public function __construct(SCT013 $sensor = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des Sensors
        $name = new TextField('name', ($sensor instanceof SCT013 ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($sensor instanceof SCT013 ? ($sensor->getIcon() != '' ? $sensor->getIcon() : 'shc-icon-sct013') : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raeume
        $rooms = new RoomChooser('rooms', ($sensor instanceof SCT013 && count($sensor->getRooms()) > 0 ? $sensor->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof SCT013 ? $sensor->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //aktuell entnommene Leistung sichtbar
        $pressureVisibility = new OnOffOption('powerVisibility', ($sensor instanceof SCT013 ? $sensor->isPowerVisible() : true));
        $pressureVisibility->setOnOffLabel();
        $pressureVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.powerVisibility'));
        $pressureVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.powerVisibility.description'));
        $pressureVisibility->requiredField(true);
        $this->addFormElement($pressureVisibility);

        //Daten Aufzeichnung
        $dataRecording = new OnOffOption('dataRecording', ($sensor instanceof SCT013 ? $sensor->isDataRecordingEnabled() : false));
        $dataRecording->setOnOffLabel();
        $dataRecording->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording'));
        $dataRecording->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording.description'));
        $dataRecording->requiredField(true);
        $this->addFormElement($dataRecording);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}