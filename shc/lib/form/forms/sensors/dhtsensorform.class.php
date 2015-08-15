<?php

namespace SHC\Form\Forms\Sensors;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\FloatInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\IconChooser;
use SHC\Form\FormElements\RoomChooser;
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

        //Icon
        $icon = new IconChooser('icon', ($sensor instanceof DHT ? ($sensor->getIcon() != '' ? $sensor->getIcon() : 'shc-icon-dht') : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raeume
        $rooms = new RoomChooser('rooms', ($sensor instanceof DHT && count($sensor->getRooms()) > 0 ? $sensor->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof DHT ? $sensor->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Temperatur Sichtbar
        $temperatureVisibility = new OnOffOption('temperatureVisibility', ($sensor instanceof DHT ? $sensor->isTemperatureVisible() : true));
        $temperatureVisibility->setOnOffLabel();
        $temperatureVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureVisibility'));
        $temperatureVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureVisibility.description'));
        $temperatureVisibility->requiredField(true);
        $this->addFormElement($temperatureVisibility);

        //Temperatur Offset
        $temperatureOffset = new FloatInputField('tempOffset', ($sensor instanceof DHT ? $sensor->getTemperatureOffset() : 0.0), array('min' => -10.0, 'max' => 10.0, 'step' => 0.1));
        $temperatureOffset->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureOffset'));
        $temperatureOffset->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.offset.description'));
        $temperatureOffset->requiredField(true);
        $this->addFormElement($temperatureOffset);

        //Luftdfeuchte sichtbar
        $humidityVisibility = new OnOffOption('humidityVisibility', ($sensor instanceof DHT ? $sensor->isHumidityVisible() : true));
        $humidityVisibility->setOnOffLabel();
        $humidityVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.humidityVisibility'));
        $humidityVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.humidityVisibility.description'));
        $humidityVisibility->requiredField(true);
        $this->addFormElement($humidityVisibility);

        //Luftdruck Offset
        $humidityOffset = new FloatInputField('humOffset', ($sensor instanceof DHT ? $sensor->getHumidityOffset() : 0.0), array('min' => -10.0, 'max' => 10.0, 'step' => 0.1));
        $humidityOffset->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.humidityOffset'));
        $humidityOffset->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.offset.description'));
        $humidityOffset->requiredField(true);
        $this->addFormElement($humidityOffset);

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