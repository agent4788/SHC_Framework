<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\RoomChooser;
use SHC\Room\Room;
use SHC\Sensor\Sensors\BMP;

/**
 * BMP Sensor Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class BMPSensorForm extends DefaultHtmlForm {

    /**
     * @param BMP $sensor
     */
    public function __construct(BMP $sensor = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des Sensors
        $name = new TextField('name', ($sensor instanceof BMP ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Raum
        $room = new RoomChooser('room', ($sensor instanceof BMP  && $sensor->getRoom() instanceof Room ? $sensor->getRoom()->getId() : null));
        $room->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $room->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $room->requiredField(true);
        $this->addFormElement($room);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof BMP ? $sensor->isVisible() : false));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Temperatur Sichtbar
        $temperatureVisibility = new OnOffOption('temperatureVisibility', ($sensor instanceof BMP ? $sensor->isTemperatureVisible() : false));
        $temperatureVisibility->setOnOffLabel();
        $temperatureVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureVisibility'));
        $temperatureVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureVisibility.description'));
        $temperatureVisibility->requiredField(true);
        $this->addFormElement($temperatureVisibility);

        //Luftdruck sichtbar
        $pressureVisibility = new OnOffOption('pressureVisibility', ($sensor instanceof BMP ? $sensor->isPressureVisible() : false));
        $pressureVisibility->setOnOffLabel();
        $pressureVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.pressureVisibility'));
        $pressureVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.pressureVisibility.description'));
        $pressureVisibility->requiredField(true);
        $this->addFormElement($pressureVisibility);

        //Standorthoehe sichtbar
        $altitudeVisibility = new OnOffOption('altitudeVisibility', ($sensor instanceof BMP ? $sensor->isAltitudeVisible() : false));
        $altitudeVisibility->setOnOffLabel();
        $altitudeVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.altitudeVisibility'));
        $altitudeVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.altitudeVisibility.description'));
        $altitudeVisibility->requiredField(true);
        $this->addFormElement($altitudeVisibility);

        //Daten Aufzeichnung
        $dataRecording = new OnOffOption('dataRecording', ($sensor instanceof BMP ? $sensor->isDataRecordingEnabled() : false));
        $dataRecording->setOnOffLabel();
        $dataRecording->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording'));
        $dataRecording->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording.description'));
        $dataRecording->requiredField(true);
        $this->addFormElement($dataRecording);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}