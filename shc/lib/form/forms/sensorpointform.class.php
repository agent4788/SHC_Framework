<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\FloatInputField;
use RWF\Form\FormElements\TextField;
use SHC\Sensor\SensorPoint;

/**
 * Sensorpunkt Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorPointForm extends DefaultHtmlForm {

    /**
     * @param SensorPoint $sensorPoint
     */
    public function __construct(SensorPoint $sensorPoint = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name
        $name = new TextField('name', ($sensorPoint instanceof SensorPoint ? $sensorPoint->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.sensorpointsManagement.form.sensorPoint.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.sensorpointsManagement.form.sensorPoint.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Warnungsgrenze
        $warnLevel = new FloatInputField('warnLevel', ($sensorPoint instanceof SensorPoint ? $sensorPoint->getWarnLevel() : 0), array('min' => 0, 'max' => 120, 'step' => 0.1));
        $warnLevel->setTitle(RWF::getLanguage()->get('acp.sensorpointsManagement.form.sensorPoint.warnLevel'));
        $warnLevel->setDescription(RWF::getLanguage()->get('acp.sensorpointsManagement.form.sensorPoint.warnLevel.description'));
        $warnLevel->requiredField(true);
        $this->addFormElement($warnLevel);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}