<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\AbstractCondition;
use SHC\Condition\Conditions\FirstLoopCondition;
use SHC\Condition\Conditions\SwitchableStateHighCondition;
use SHC\Condition\Conditions\SwitchableStateLowCondition;
use SHC\Form\FormElements\SwitchableChooser;

/**
 * Status schaltbares Element
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchableStateConditionForm extends DefaultHtmlForm {

    /**
     * @param AbstractCondition $condition
     */
    public function __construct(AbstractCondition $condition = null) {

        //Pruefen ob zulaessiges Objekt uebergeben
        if($condition !== null && !$condition instanceof SwitchableStateHighCondition && !$condition instanceof SwitchableStateLowCondition) {

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

        //Schaltbare Elemente
        $switchables = new SwitchableChooser('switchables', ($condition !== null ? explode(',', $condition->getData()['switchables']) : array()));
        $switchables->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.switchables'));
        $switchables->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.switchables.description'));
        $switchables->requiredField(true);
        $this->addFormElement($switchables);

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