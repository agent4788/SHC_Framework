<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\Conditions\HolidaysCondition;
use SHC\Form\FormElements\HolidayChooser;

/**
 * Niemand zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class HolidayConditionForm extends DefaultHtmlForm {

    /**
     * @param HolidaysCondition $condition
     */
    public function __construct(HolidaysCondition $condition = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition instanceof HolidaysCondition ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Feiertage
        $holidays = new HolidayChooser('holidays', ($condition instanceof HolidaysCondition ? $condition->getData()['holidays'] : 0));
        $holidays->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.holidays'));
        $holidays->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.holidays.description'));
        $holidays->requiredField(true);
        $this->addFormElement($holidays);

        //Invertiert
        $invert = new OnOffOption('invert', ($condition  instanceof HolidaysCondition ? $condition->getData()['invert'] : false));
        $invert->setYesNoLabel();
        $invert->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.holidaysInvert'));
        $invert->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.holidaysInvert.description'));
        $invert->requiredField(true);
        $this->addFormElement($invert);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition  instanceof HolidaysCondition ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}