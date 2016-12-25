<?php

namespace SHC\Form\Forms\Conditions;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Condition\Conditions\FileExistsCondition;

/**
 * Benutzer zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.2-0
 * @version    2.0.2-0
 */
class FileExistsConditionForm extends DefaultHtmlForm {

    /**
     * @param FileExistsCondition $condition
     */
    public function __construct(FileExistsCondition $condition = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($condition instanceof FileExistsCondition ? $condition->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //path
        $path = new TextField('path', ($condition instanceof FileExistsCondition ? $condition->getData()['path'] : ''), array('minlength' => 3, 'maxlength' => 100));
        $path->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.path'));
        $path->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.path.description'));
        $path->requiredField(true);
        $this->addFormElement($path);

        //wait
        $wait = new IntegerInputField('wait', ($condition instanceof FileExistsCondition ? $condition->getData()['wait'] : 0), array('min' => 0, 'max' => 30));
        $wait->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.wait'));
        $wait->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.wait.description'));
        $wait->requiredField(true);
        $this->addFormElement($wait);

        //delete
        $delete = new OnOffOption('delete', ($condition instanceof FileExistsCondition ? $condition->getData()['delete'] : false));
        $delete->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.delete'));
        $delete->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.delete.description'));
        $delete->requiredField(true);
        $this->addFormElement($delete);

        //invert
        $invert = new OnOffOption('invert', ($condition instanceof FileExistsCondition ? $condition->getData()['invert'] : false));
        $invert->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.invert'));
        $invert->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.invert.description'));
        $invert->requiredField(true);
        $this->addFormElement($invert);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($condition  instanceof FileExistsCondition ? $condition->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.conditionManagement.form.condition.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}