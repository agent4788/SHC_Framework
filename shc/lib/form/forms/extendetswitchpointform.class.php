<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\SelectMultiple;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\ConditionsChooser;
use SHC\Form\FormElements\SwitchPointCommandChooser;
use SHC\Timer\SwitchPoint;

/**
 * Erweitertes Schaltpunkt Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ExtendetSwitchPointForm extends DefaultHtmlForm {

    /**
     * @param SwitchPoint $switchPoint
     */
    public function __construct(SwitchPoint $switchPoint = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($switchPoint instanceof SwitchPoint ? $switchPoint->getName() : ''), array('minlength' => 3, 'maxlength' => 35));
        $name->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.name.decription'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Befehl auswählen
        $command = new SwitchPointCommandChooser('command', ($switchPoint instanceof SwitchPoint ? $switchPoint->getCommand() : null));
        $command->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.command'));
        $command->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.command.decription'));
        $command->requiredField(true);
        $this->addFormElement($command);

        //Bedingungen
        $conditions = new ConditionsChooser('conditions', ($switchPoint instanceof SwitchPoint ? $switchPoint->listConditions() : array()));
        $conditions->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.condition'));
        $conditions->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.condition.decription'));
        $conditions->requiredField(true);
        $this->addFormElement($conditions);

        //Jahr
        $year = new SelectMultiple('year');
        $year->setOptions(array('size' => 8));
        $values = array();
        $values['*'] = array(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.year.every'), (($switchPoint instanceof SwitchPoint && in_array('*', $switchPoint->getYear())) || !$switchPoint instanceof SwitchPoint ? 1 : 0));
        foreach(range(DateTime::now()->getYear(), DateTime::now()->getYear() + 20) as $i) {

            $values[$i] = array($i, ($switchPoint instanceof SwitchPoint && in_array($i, $switchPoint->getYear()) ? 1 : 0));
        }
        $year->setValues($values);
        $year->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.year'));
        $year->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.year.decription'));
        $year->requiredField(true);
        $this->addFormElement($year);

        //Monat
        $month = new SelectMultiple('month');
        $month->setOptions(array('size' => 8));
        $values = array();
        $values['*'] = array(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.month.every'), (($switchPoint instanceof SwitchPoint && in_array('*', $switchPoint->getMonth())) || !$switchPoint instanceof SwitchPoint ? 1 : 0));
        foreach(range(1, 12) as $i) {

            $values[$i] = array(RWF::getLanguage()->get('global.date.month.'. $i), ($switchPoint instanceof SwitchPoint && in_array($i, $switchPoint->getMonth()) ? 1 : 0));
        }
        $month->setValues($values);
        $month->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.month'));
        $month->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.month.decription'));
        $month->requiredField(true);
        $this->addFormElement($month);

        //Tag
        $day = new SelectMultiple('day');
        $day->setOptions(array('size' => 8));
        $values = array();
        $values['*'] = array(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.day.every'), (($switchPoint instanceof SwitchPoint && in_array('*', $switchPoint->getDay())) || !$switchPoint instanceof SwitchPoint ? 1 : 0));
        $values['mon'] = array(RWF::getLanguage()->get('global.date.weekDay.mon'), ($switchPoint instanceof SwitchPoint && in_array('mon', $switchPoint->getDay())? 1 : 0));
        $values['tue'] = array(RWF::getLanguage()->get('global.date.weekDay.tue'), ($switchPoint instanceof SwitchPoint && in_array('tue', $switchPoint->getDay())? 1 : 0));
        $values['wed'] = array(RWF::getLanguage()->get('global.date.weekDay.wed'), ($switchPoint instanceof SwitchPoint && in_array('wed', $switchPoint->getDay())? 1 : 0));
        $values['thu'] = array(RWF::getLanguage()->get('global.date.weekDay.thu'), ($switchPoint instanceof SwitchPoint && in_array('thu', $switchPoint->getDay())? 1 : 0));
        $values['fri'] = array(RWF::getLanguage()->get('global.date.weekDay.fri'), ($switchPoint instanceof SwitchPoint && in_array('fri', $switchPoint->getDay())? 1 : 0));
        $values['sat'] = array(RWF::getLanguage()->get('global.date.weekDay.sat'), ($switchPoint instanceof SwitchPoint && in_array('sat', $switchPoint->getDay())? 1 : 0));
        $values['sun'] = array(RWF::getLanguage()->get('global.date.weekDay.sun'), ($switchPoint instanceof SwitchPoint && in_array('sun', $switchPoint->getDay())? 1 : 0));
        foreach(range(1, 31) as $i) {

            $values[$i] = array($i, ($switchPoint instanceof SwitchPoint && in_array($i, $switchPoint->getDay() )? 1 : 0));
        }
        $day->setValues($values);
        $day->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.day'));
        $day->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.day.decription'));
        $day->requiredField(true);
        $this->addFormElement($day);

        //Stunde
        $hour = new SelectMultiple('hour');
        $hour->setOptions(array('size' => 8));
        $values = array();
        $values['*'] = array(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.hour.every'), (($switchPoint instanceof SwitchPoint && in_array('*', $switchPoint->getHour())) || !$switchPoint instanceof SwitchPoint ? 1 : 0));
        foreach(range(0, 23) as $i) {

            $values[$i] = array($i, ($switchPoint instanceof SwitchPoint && in_array($i, $switchPoint->getHour()) && $switchPoint->getHour()[0] != '*'  ? 1 : 0));
        }
        $hour->setValues($values);
        $hour->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.hour'));
        $hour->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.hour.decription'));
        $hour->requiredField(true);
        $this->addFormElement($hour);

        //Minute
        $minute = new SelectMultiple('minute');
        $minute->setOptions(array('size' => 8));
        $values = array();
        $values['*'] = array(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.minute.every'), (($switchPoint instanceof SwitchPoint && in_array('*', $switchPoint->getMinute())) || !$switchPoint instanceof SwitchPoint ? 1 : 0));
        foreach(range(0, 59) as $i) {

            $values[$i] = array($i, ($switchPoint instanceof SwitchPoint && in_array($i, $switchPoint->getMinute()) && $switchPoint->getMinute()[0] != '*'  ? 1 : 0));
        }
        $minute->setValues($values);
        $minute->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.minute'));
        $minute->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.minute.decription'));
        $minute->requiredField(true);
        $this->addFormElement($minute);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($switchPoint instanceof SwitchPoint ? $switchPoint->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.active.decription'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}