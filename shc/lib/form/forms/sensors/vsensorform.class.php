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
use SHC\Form\FormElements\SensorChooser;
use SHC\Sensor\Sensor;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\vSensor;
use SHC\Sensor\vSensors\Energy;
use SHC\Sensor\vSensors\FluidAmount;
use SHC\Sensor\vSensors\Humidity;
use SHC\Sensor\vSensors\LightIntensity;
use SHC\Sensor\vSensors\Moisture;
use SHC\Sensor\vSensors\Power;
use SHC\Sensor\vSensors\Temperature;

/**
 * VIrtual Sensor Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class vSensorForm extends DefaultHtmlForm {

    /**
     * @param vSensor $sensor
     */
    public function __construct(vSensor $sensor = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        $filter = SensorChooser::ALL;
        if($sensor instanceof Energy) {

            $filter = SensorChooser::ENERGY;
        } elseif($sensor instanceof FluidAmount) {

            $filter = SensorChooser::METER;
        } elseif($sensor instanceof Humidity) {

            $filter = SensorChooser::HUMDITY;
        } elseif($sensor instanceof LightIntensity) {

            $filter = SensorChooser::LINGTH_INTENSIVITY;
        } elseif($sensor instanceof Moisture) {

            $filter = SensorChooser::MOISTURE;
        } elseif($sensor instanceof Power) {

            $filter = SensorChooser::POWER;
        } elseif($sensor instanceof Temperature) {

            $filter = SensorChooser::TEMPERATURE;
        }

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des Sensors
        $name = new TextField('name', ($sensor instanceof Sensor ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($sensor instanceof Sensor ? ($sensor->getIcon() != '' ? $sensor->getIcon() : 'shc-icon-ds18x20') : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raeume
        $rooms = new RoomChooser('rooms', ($sensor instanceof Sensor && count($sensor->getRooms()) > 0 ? $sensor->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof Sensor ? $sensor->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Sensoren
        $sensors = new SensorChooser('sensors', ($sensor instanceof vSensor ? $sensor->listSensorIDs() : array()), $filter);
        $sensors->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.sensors'));
        $sensors->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.sensors.description'));
        $sensors->requiredField(true);
        $this->addFormElement($sensors);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}