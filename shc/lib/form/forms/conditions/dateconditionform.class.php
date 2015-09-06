<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\Conditions\DateCondition;

/**
 * Benutzer zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DateConditionForm extends DefaultHtmlForm {

    /**
     * @param DateCondition $condition
     */
    public function __construct(DateCondition $condition = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition instanceof DateCondition ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Start Datum
        $startDate = new TextField('startDate', ($condition instanceof DateCondition ? $condition->getData()['start'] : DateTime::now()->format('m-d')), array('minlength' => 5, 'maxlength' => 5));
        $startDate->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.startDate'));
        $startDate->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.startDate.description'));
        $startDate->requiredField(true);
        $this->addFormElement($startDate);

        //End Datum
        $endDate = new TextField('endDate', ($condition instanceof DateCondition ? $condition->getData()['end'] : DateTime::nextDay()->format('m-d')), array('minlength' => 5, 'maxlength' => 5));
        $endDate->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.endDate'));
        $endDate->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.endDate.description'));
        $endDate->requiredField(true);
        $this->addFormElement($endDate);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition  instanceof DateCondition ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}