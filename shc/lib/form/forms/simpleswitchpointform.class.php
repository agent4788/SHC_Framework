<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\RadioButtons;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\SwitchPointCommandChooser;
use SHC\Timer\SwitchPoint;

/**
 * Einfaches Schaltpunkt Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SimpleSwitchPointForm extends DefaultHtmlForm {

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

        //Tage
        $dayOfWeek = new RadioButtons('daysOfWeek');
        $dayOfWeek->setValues(array(
            1 => array(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.dayOfWeek.val1'), ($switchPoint instanceof SwitchPoint && implode(',', $switchPoint->getDay()) == '*' ? 1 : 0)),
            2 => array(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.dayOfWeek.val2'), ($switchPoint instanceof SwitchPoint && implode(',', $switchPoint->getDay()) == 'mon,tue,wed,thu,fri' ? 1 : 0)),
            3 => array(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.dayOfWeek.val3'), ($switchPoint instanceof SwitchPoint && implode(',', $switchPoint->getDay()) == 'sat,sun' ? 1 : 0))
        ));
        $dayOfWeek->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.dayOfWeek'));
        $dayOfWeek->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.dayOfWeek.decription'));
        $dayOfWeek->requiredField(true);
        $this->addFormElement($dayOfWeek);

        //Zeit
        $hourSpinner = new IntegerInputField('hour', ($switchPoint instanceof SwitchPoint ? $switchPoint->getHour()[0] : DateTime::now()->getHour()), array('min' => 0, 'max' => 23));
        $hourSpinner->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.hour'));
        $hourSpinner->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.hour.decription'));
        $hourSpinner->requiredField(true);
        $this->addFormElement($hourSpinner);

        $minuteSpinner = new IntegerInputField('minute', ($switchPoint instanceof SwitchPoint ? $switchPoint->getMinute()[0] : DateTime::now()->getMinute()), array('min' => 0, 'max' => 59));
        $minuteSpinner->setTitle(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.minute'));
        $minuteSpinner->setDescription(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.minute.decription'));
        $minuteSpinner->requiredField(true);
        $this->addFormElement($minuteSpinner);


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