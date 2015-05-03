<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\Conditions\UserNotAtHomeCondition;
use SHC\Form\FormElements\UsersAtHomeChooser;

/**
 * Benutzer nicht zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserNotAtHomeConditionForm extends DefaultHtmlForm {

    /**
     * @param UserNotAtHomeCondition $condition
     */
    public function __construct(UserNotAtHomeCondition $condition = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition instanceof UserNotAtHomeCondition ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Benutzer auswahlen
        $users = new UsersAtHomeChooser('users', ($condition instanceof UserNotAtHomeCondition ? explode(',', $condition->getData()['users']) : array()));
        $users->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.userNotAtHome'));
        $users->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.userNotAtHome.description'));
        $users->requiredField(true);
        $this->addFormElement($users);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition  instanceof UserNotAtHomeCondition ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}