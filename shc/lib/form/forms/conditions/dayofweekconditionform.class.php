<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\Conditions\DayOfWeekCondition;
use SHC\Form\FormElements\DayOfWeekChooser;

/**
 * Tag der Woche
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DayOfWeekConditionForm extends DefaultHtmlForm {

    /**
     * @param DayOfWeekCondition $condition
     */
    public function __construct(DayOfWeekCondition $condition = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition instanceof DayOfWeekCondition ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Start Tag
        $startDay = new DayOfWeekChooser('startDay', ($condition instanceof DayOfWeekCondition ? $condition->getData()['start'] : strtolower(DateTime::now()->format('D'))), array('minlength' => 5, 'maxlength' => 5));
        $startDay->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.startDay'));
        $startDay->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.startDay.description'));
        $startDay->requiredField(true);
        $this->addFormElement($startDay);

        //End Tag
        $endDay = new DayOfWeekChooser('endDay', ($condition instanceof DayOfWeekCondition ? $condition->getData()['end'] : strtolower(DateTime::nextDay()->format('D'))), array('minlength' => 5, 'maxlength' => 5));
        $endDay->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.endDay'));
        $endDay->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.endDay.description'));
        $endDay->requiredField(true);
        $this->addFormElement($endDay);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition  instanceof DayOfWeekCondition ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}