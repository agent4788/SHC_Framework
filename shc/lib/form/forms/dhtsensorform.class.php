<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\RoomChooser;
use SHC\Room\Room;
use SHC\Sensor\Sensors\DHT;

/**
 * DHT Sensor Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DHTSensorForm extends DefaultHtmlForm {

    /**
     * @param DHT $sensor
     */
    public function __construct(DHT $sensor = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des Sensors
        $name = new TextField('name', ($sensor instanceof DHT ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Raum
        $room = new RoomChooser('room', ($sensor instanceof DHT && $sensor->getRoom() instanceof Room ? $sensor->getRoom()->getId() : null));
        $room->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $room->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $room->requiredField(true);
        $this->addFormElement($room);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof DHT ? $sensor->isVisible() : false));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Temperatur Sichtbar
        $temperatureVisibility = new OnOffOption('temperatureVisibility', ($sensor instanceof DHT ? $sensor->isTemperatureVisible() : false));
        $temperatureVisibility->setOnOffLabel();
        $temperatureVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureVisibility'));
        $temperatureVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureVisibility.description'));
        $temperatureVisibility->requiredField(true);
        $this->addFormElement($temperatureVisibility);

        //Luftdfeuchte sichtbar
        $humidityVisibility = new OnOffOption('humidityVisibility', ($sensor instanceof DHT ? $sensor->isHumidityVisible() : false));
        $humidityVisibility->setOnOffLabel();
        $humidityVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.humidityVisibility'));
        $humidityVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.humidityVisibility.description'));
        $humidityVisibility->requiredField(true);
        $this->addFormElement($humidityVisibility);

        //Daten Aufzeichnung
        $dataRecording = new OnOffOption('dataRecording', ($sensor instanceof DHT ? $sensor->isDataRecordingEnabled() : false));
        $dataRecording->setOnOffLabel();
        $dataRecording->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording'));
        $dataRecording->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording.description'));
        $dataRecording->requiredField(true);
        $this->addFormElement($dataRecording);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}