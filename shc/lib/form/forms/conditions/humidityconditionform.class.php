<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\FloatInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\AbstractCondition;
use SHC\Condition\Conditions\HumidityGreaterThanCondition;
use SHC\Condition\Conditions\HumidityLowerThanCondition;
use SHC\Form\FormElements\SensorChooser;

/**
 * Luftfeuchte Bedingung
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class HumidityConditionForm extends DefaultHtmlForm {

    /**
     * @param AbstractCondition $condition
     */
    public function __construct(AbstractCondition $condition = null) {

        //Pruefen ob zulaessiges Objekt uebergeben
        if($condition !== null && !$condition instanceof HumidityLowerThanCondition && !$condition instanceof HumidityGreaterThanCondition) {

            throw new \Exception('ungültige Bedingung übergeben', 1581);
        }

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition !== null ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Sensoren
        $sensors = new SensorChooser('sensors', ($condition !== null ? explode(',', $condition->getData()['sensors']) : array()), SensorChooser::HUMDITY);
        $sensors->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.sensors'));
        $sensors->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.sensors.description'));
        $sensors->requiredField(true);
        $this->addFormElement($sensors);

        //Grenzwert
        $humidity = new FloatInputField('humidity', ($condition !== null ? (float) $condition->getData()['humidity'] : 50));
        $humidity->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.humidity'));
        $humidity->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.humidity.description'));
        $humidity->requiredField(true);
        $this->addFormElement($humidity);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition !== null ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}