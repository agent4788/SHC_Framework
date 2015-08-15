<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\Conditions\UserAtHomeCondition;
use SHC\Form\FormElements\UsersAtHomeChooser;

/**
 * Benutzer zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserAtHomeConditionForm extends DefaultHtmlForm {

    /**
     * @param UserAtHomeCondition $condition
     */
    public function __construct(UserAtHomeCondition $condition = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition instanceof UserAtHomeCondition ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Benutzer auswahlen
        $users = new UsersAtHomeChooser('users', ($condition instanceof UserAtHomeCondition ? explode(',', $condition->getData()['users']) : array()));
        $users->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.userAtHome'));
        $users->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.userAtHome.description'));
        $users->requiredField(true);
        $this->addFormElement($users);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition  instanceof UserAtHomeCondition ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}