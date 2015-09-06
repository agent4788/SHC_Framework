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
use SHC\Sensor\Sensors\AvmMeasuringSocket;

/**
 * AvmMeasuringSocket Sensor Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AvmMeasuringSocketForm extends DefaultHtmlForm {

    /**
     * @param AvmMeasuringSocket $sensor
     */
    public function __construct(AvmMeasuringSocket $sensor = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des Sensors
        $name = new TextField('name', ($sensor instanceof AvmMeasuringSocket ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($sensor instanceof AvmMeasuringSocket ? ($sensor->getIcon() != '' ? $sensor->getIcon() : 'shc-icon-avmPowerSensor') : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raeume
        $rooms = new RoomChooser('rooms', ($sensor instanceof AvmMeasuringSocket && count($sensor->getRooms()) > 0 ? $sensor->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof AvmMeasuringSocket ? $sensor->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Temperatur Sichtbar
        $temperatureVisibility = new OnOffOption('temperatureVisibility', ($sensor instanceof AvmMeasuringSocket ? $sensor->isTemperatureVisible() : true));
        $temperatureVisibility->setOnOffLabel();
        $temperatureVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureVisibility'));
        $temperatureVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureVisibility.description'));
        $temperatureVisibility->requiredField(true);
        $this->addFormElement($temperatureVisibility);

        //Temperatur Offset
        $temperatureOffset = new FloatInputField('tempOffset', ($sensor instanceof AvmMeasuringSocket ? $sensor->getTemperatureOffset() : 0.0), array('min' => -10.0, 'max' => 10.0, 'step' => 0.1));
        $temperatureOffset->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.temperatureOffset'));
        $temperatureOffset->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.offset.description'));
        $temperatureOffset->requiredField(true);
        $this->addFormElement($temperatureOffset);

        //aktuell entnommene Leistung sichtbar
        $pressureVisibility = new OnOffOption('powerVisibility', ($sensor instanceof AvmMeasuringSocket ? $sensor->isPowerVisible() : true));
        $pressureVisibility->setOnOffLabel();
        $pressureVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.powerVisibility'));
        $pressureVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.powerVisibility.description'));
        $pressureVisibility->requiredField(true);
        $this->addFormElement($pressureVisibility);

        //entnomme Leistung sichtbar
        $altitudeVisibility = new OnOffOption('energyVisibility', ($sensor instanceof AvmMeasuringSocket ? $sensor->isEnergyVisible() : true));
        $altitudeVisibility->setOnOffLabel();
        $altitudeVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.energyVisibility'));
        $altitudeVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.energyVisibility.description'));
        $altitudeVisibility->requiredField(true);
        $this->addFormElement($altitudeVisibility);

        //Daten Aufzeichnung
        $dataRecording = new OnOffOption('dataRecording', ($sensor instanceof AvmMeasuringSocket ? $sensor->isDataRecordingEnabled() : false));
        $dataRecording->setOnOffLabel();
        $dataRecording->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording'));
        $dataRecording->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording.description'));
        $dataRecording->requiredField(true);
        $this->addFormElement($dataRecording);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}