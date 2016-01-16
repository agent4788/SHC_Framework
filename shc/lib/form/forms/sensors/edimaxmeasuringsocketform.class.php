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
use SHC\Sensor\Sensors\EdimaxMeasuringSocket;

/**
 * AvmMeasuringSocket Sensor Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EdimaxMeasuringSocketForm extends DefaultHtmlForm {

    /**
     * @param EdimaxMeasuringSocket $sensor
     */
    public function __construct(EdimaxMeasuringSocket $sensor = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des Sensors
        $name = new TextField('name', ($sensor instanceof EdimaxMeasuringSocket ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($sensor instanceof EdimaxMeasuringSocket ? ($sensor->getIcon() != '' ? $sensor->getIcon() : 'shc-icon-edimaxPowerSensor') : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raeume
        $rooms = new RoomChooser('rooms', ($sensor instanceof EdimaxMeasuringSocket && count($sensor->getRooms()) > 0 ? $sensor->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof EdimaxMeasuringSocket ? $sensor->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //aktuell entnommene Leistung sichtbar
        $pressureVisibility = new OnOffOption('powerVisibility', ($sensor instanceof EdimaxMeasuringSocket ? $sensor->isPowerVisible() : true));
        $pressureVisibility->setOnOffLabel();
        $pressureVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.powerVisibility'));
        $pressureVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.powerVisibility.description'));
        $pressureVisibility->requiredField(true);
        $this->addFormElement($pressureVisibility);

        //entnomme Leistung sichtbar
        $altitudeVisibility = new OnOffOption('energyVisibility', ($sensor instanceof EdimaxMeasuringSocket ? $sensor->isEnergyVisible() : true));
        $altitudeVisibility->setOnOffLabel();
        $altitudeVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.energyVisibility'));
        $altitudeVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.energyVisibility.description'));
        $altitudeVisibility->requiredField(true);
        $this->addFormElement($altitudeVisibility);

        //Daten Aufzeichnung
        $dataRecording = new OnOffOption('dataRecording', ($sensor instanceof EdimaxMeasuringSocket ? $sensor->isDataRecordingEnabled() : false));
        $dataRecording->setOnOffLabel();
        $dataRecording->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording'));
        $dataRecording->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording.description'));
        $dataRecording->requiredField(true);
        $this->addFormElement($dataRecording);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}