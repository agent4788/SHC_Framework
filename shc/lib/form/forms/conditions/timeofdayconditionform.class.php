<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\Conditions\TimeOfDayCondition;

/**
 * Tageszeit
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TimeOfDayConditionForm extends DefaultHtmlForm {

    /**
     * @param TimeOfDayConditionForm $condition
     */
    public function __construct(TimeOfDayCondition $condition = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition instanceof TimeOfDayCondition ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Start Zeit
        if($condition instanceof TimeOfDayCondition) {

            $startTime = preg_split('#:#', $condition->getData()['start']);
        } else {

            $startTime[0] = DateTime::now()->format('H');
            $startTime[1] = DateTime::now()->format('i');
        }
        $startHour = new IntegerInputField('startHour', $startTime[0], array('min' => 0, 'max' => 23));
        $startHour->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.startHour'));
        $startHour->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.startHour.description'));
        $startHour->requiredField(true);
        $this->addFormElement($startHour);
        $startMinute = new IntegerInputField('startMinute', $startTime[1], array('min' => 0, 'max' => 59));
        $startMinute->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.startMinute'));
        $startMinute->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.startMinute.description'));
        $startMinute->requiredField(true);
        $this->addFormElement($startMinute);

        //End Zeit
        if($condition instanceof TimeOfDayCondition) {

            $endTime = preg_split('#:#', $condition->getData()['end']);
        } else {

            $end = DateTime::now()->add(new \DateInterval('PT2H'));
            $endTime[0] = $end->format('H');
            $endTime[1] = $end->format('i');
        }
        $endHour = new IntegerInputField('endHour', $endTime[0], array('min' => 0, 'max' => 23));
        $endHour->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.endHour'));
        $endHour->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.endHour.description'));
        $endHour->requiredField(true);
        $this->addFormElement($endHour);
        $endMinute = new IntegerInputField('endMinute', $endTime[1], array('min' => 0, 'max' => 59));
        $endMinute->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.endMinute'));
        $endMinute->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.endMinute.description'));
        $endMinute->requiredField(true);
        $this->addFormElement($endMinute);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition  instanceof TimeOfDayCondition ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}