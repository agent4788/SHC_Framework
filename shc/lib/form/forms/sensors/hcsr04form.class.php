<?php

namespace SHC\Form\Forms\Sensors;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\FloatInputField;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\IconChooser;
use SHC\Form\FormElements\RoomChooser;
use SHC\Sensor\Sensors\HcSr04;

/**
 * HC-SR04 Sensor Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class HcSr04Form extends DefaultHtmlForm {

    /**
     * @param HcSr04 $sensor
     */
    public function __construct(HcSr04 $sensor = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name des Sensors
        $name = new TextField('name', ($sensor instanceof HcSr04 ? $sensor->getName() : ''), array('minlength' => 3, 'maxlength' => 40));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Icon
        $icon = new IconChooser('icon', ($sensor instanceof HcSr04 ? ($sensor->getIcon() != '' ? $sensor->getIcon() : 'shc-icon-hcsr04') : ''));
        $icon->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon'));
        $icon->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.icon.description'));
        $icon->requiredField(true);
        $this->addFormElement($icon);

        //Raeume
        $rooms = new RoomChooser('rooms', ($sensor instanceof HcSr04 && count($sensor->getRooms()) > 0 ? $sensor->getRooms(): array()));
        $rooms->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room'));
        $rooms->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.room.description'));
        $rooms->requiredField(true);
        $this->addFormElement($rooms);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($sensor instanceof HcSr04 ? $sensor->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Entfernung Sichtbar
        $distanceVisibility = new OnOffOption('distanceVisibility', ($sensor instanceof HcSr04 ? $sensor->isDistanceVisible() : true));
        $distanceVisibility->setOnOffLabel();
        $distanceVisibility->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.distanceVisibility'));
        $distanceVisibility->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.distanceVisibility.description'));
        $distanceVisibility->requiredField(true);
        $this->addFormElement($distanceVisibility);

        //Entfernung Offset
        $distanceOffset = new IntegerInputField('distanceOffset', ($sensor instanceof HcSr04 ? $sensor->getDistanceOffset() : 0.0), array('min' => -10000, 'max' => 10000));
        $distanceOffset->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.distanceOffset'));
        $distanceOffset->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.offset.description'));
        $distanceOffset->requiredField(true);
        $this->addFormElement($distanceOffset);

        //Daten Aufzeichnung
        $dataRecording = new OnOffOption('dataRecording', ($sensor instanceof HcSr04 ? $sensor->isDataRecordingEnabled() : false));
        $dataRecording->setOnOffLabel();
        $dataRecording->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording'));
        $dataRecording->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.sensorForm.dataRecording.description'));
        $dataRecording->requiredField(true);
        $this->addFormElement($dataRecording);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}