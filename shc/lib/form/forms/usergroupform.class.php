<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextArea;
use RWF\Form\FormElements\TextField;
use RWF\Form\TabbedHtmlForm;
use RWF\User\UserEditor;
use RWF\User\UserGroup;

/**
 * Benutzergruppen Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserGroupForm extends TabbedHtmlForm {

    /**
     * @param UserGroup $group
     */
    public function __construct(UserGroup $group = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Allgemeine Daten Tab hinzufuegen
        $this->addTab('data', RWF::getLanguage()->get('acp.userManagement.form.group.tab1.title'), RWF::getLanguage()->get('acp.userManagement.form.group.tab1.description'));

        //Gruppenname
        $name = new TextField('name', ($group instanceof UserGroup ? $group->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.userManagement.form.group.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.userManagement.form.group.name.description'));
        $name->requiredField(true);
        $this->addFormElementToTab('data', $name);

        //Beschreibung
        $description = new TextArea('description', ($group instanceof UserGroup ? $group->getDescription() : ''), array('maxlength' => 250));
        $description->setTitle(RWF::getLanguage()->get('acp.userManagement.form.group.desc'));
        $description->setDescription(RWF::getLanguage()->get('acp.userManagement.form.group.desc.description'));
        $this->addFormElementToTab('data', $description);

        //Benutzer Rechte Tab hinzufuegen
        $this->addTab('userPremissions', RWF::getLanguage()->get('acp.userManagement.form.group.tab2.title'), RWF::getLanguage()->get('acp.userManagement.form.group.tab2.description'));

        //Administrator Rechte Tab hinzufuegen
        $this->addTab('adminPremissions', RWF::getLanguage()->get('acp.userManagement.form.group.tab3.title'), RWF::getLanguage()->get('acp.userManagement.form.group.tab3.description'));

        //Rechte Ja/Nein Auswahl erstellen
        foreach(UserEditor::getInstance()->getUserGroupById(1)->listPermissions() as $premissionName => $premissionValue) {

            if(preg_match('#^shc\.#', $premissionName)) {

                $yesNoOption = new OnOffOption(str_replace('.', '_', $premissionName), ($group instanceof UserGroup ? $group->checkPermission($premissionName) : false));
                $yesNoOption->setTitle(RWF::getLanguage()->get('acp.userManagement.premissions.'. $premissionName));
                $yesNoOption->setDescription(RWF::getLanguage()->get('acp.userManagement.premissions.'. $premissionName .'.description'));
                $yesNoOption->requiredField(true);

                if(preg_match('#^shc\.ucp.#', $premissionName)) {

                    $this->addFormElementToTab('userPremissions', $yesNoOption);
                } else {

                    $this->addFormElementToTab('adminPremissions', $yesNoOption);
                }
            }
        }
        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}