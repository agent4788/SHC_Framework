<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\RoomChooser;
use SHC\Room\Room;
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
        $name = new TextField('name', ($sensor instanceof RainSensor ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Raum
        $room = new RoomChooser('room', ($sensor instanceof RainSensor && $sensor->getRoom() instanceof Room ? $sensor->getRoom()->getId() : null));
        $room->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $room->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $room->requiredField(true);
        $this->addFormElement($room);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof RainSensor ? $sensor->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Wetr Sichtbar
        $valueVisibility = new OnOffOption('valueVisibility', ($sensor instanceof RainSensor ? $sensor->isValueVisible() : true));
        $valueVisibility->setOnOffLabel();
        $valueVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.valueVisibility'));
        $valueVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.valueVisibility.description'));
        $valueVisibility->requiredField(true);
        $this->addFormElement($valueVisibility);

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