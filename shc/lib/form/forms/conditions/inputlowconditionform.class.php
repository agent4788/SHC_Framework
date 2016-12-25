<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\Conditions\InputHighCondition;
use SHC\Condition\Conditions\InputLowCondition;
use SHC\Form\FormElements\InputChooser;

/**
 * Niemand zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InputLowConditionForm extends DefaultHtmlForm {

    /**
     * @param InputHighCondition $condition
     */
    public function __construct(InputLowCondition $condition = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition instanceof InputLowCondition ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Eingaenge
        $inputs = new InputChooser('inputs', ($condition instanceof InputLowCondition ? $condition->getData()['inputs'] : array()));
        $inputs->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.inputs'));
        $inputs->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.inputs.description'));
        $inputs->requiredField(true);
        $this->addFormElement($inputs);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition  instanceof InputLowCondition ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}