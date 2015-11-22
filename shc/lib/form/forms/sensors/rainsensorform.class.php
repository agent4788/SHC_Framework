<?php

namespace SHC\Form\Forms\Sensors;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\IconChooser;
use SHC\Form\FormElements\RoomChooser;
use SHC\Sensor\Sensors\RainSensor;

/**
 * DS18x20 Sensor Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RainSensorForm extends DefaultHtmlForm {

    /**
     * @param RainSensor $sensor
     */
    public function __construct(RainSensor $sensor = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des Sensors
        $name = new TextField('name', ($sensor instanceof RainSensor ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($sensor instanceof RainSensor ? ($sensor->getIcon() != '' ? $sensor->getIcon() : 'shc-icon-rain') : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raeume
        $rooms = new RoomChooser('rooms', ($sensor instanceof RainSensor && count($sensor->getRooms()) > 0 ? $sensor->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof RainSensor ? $sensor->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Wetr Sichtbar
        $valueVisibility = new OnOffOption('valueVisibility', ($sensor instanceof RainSensor ? $sensor->isMoistureVisible() : true));
        $valueVisibility->setOnOffLabel();
        $valueVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.valueVisibility'));
        $valueVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.valueVisibility.description'));
        $valueVisibility->requiredField(true);
        $this->addFormElement($valueVisibility);

        //Werte Offset
        $valueOffset = new IntegerInputField('valOffset', ($sensor instanceof RainSensor ? $sensor->getMoistureOffset() : 0.0), array('min' => -50, 'max' => 50));
        $valueOffset->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.valueOffset'));
        $valueOffset->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.offset.description'));
        $valueOffset->requiredField(true);
        $this->addFormElement($valueOffset);

        //Daten Aufzeichnung
        $dataRecording = new OnOffOption('dataRecording', ($sensor instanceof RainSensor ? $sensor->isDataRecordingEnabled() : false));
        $dataRecording->setOnOffLabel();
        $dataRecording->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording'));
        $dataRecording->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording.description'));
        $dataRecording->requiredField(true);
        $this->addFormElement($dataRecording);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}