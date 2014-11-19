<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\User\User;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\PasswordField;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\LanguageChooser;
use SHC\Form\FormElements\UserGroupChooser;
use SHC\Form\FormElements\UserMainGroupChooser;
use SHC\Form\FormElements\WebStyleChooser;

/**
 * Benutzer Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserForm extends DefaultHtmlForm {

    /**
     * @param User $user Benutzer
     */
    public function __construct(User $user = null)
    {

        //Konstruktor von DefaultHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Benutzername
        $name = new TextField('name', ($user instanceof User ? $user->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.userManagement.form.user.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.userManagement.form.user.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Passwort
        $pass1 = new PasswordField('password', '', array('minlength' => 5, 'maxlength' => 20));
        $pass1->setTitle(RWF::getLanguage()->get('acp.userManagement.form.user.pass1'));
        $pass1->setDescription(RWF::getLanguage()->get('acp.userManagement.form.user.pass1.description'));
        if (!$user instanceof User) {

            $pass1->requiredField(true);
        }
        $this->addFormElement($pass1);
        $pass2 = new PasswordField('passwordCompare', '', array('minlength' => 5, 'maxlength' => 20));
        $pass2->setTitle(RWF::getLanguage()->get('acp.userManagement.form.user.pass2'));
        $pass2->setDescription(RWF::getLanguage()->get('acp.userManagement.form.user.pass2.description'));
        if (!$user instanceof User) {

            $pass2->requiredField(true);
        }
        $this->addFormElement($pass2);

        //Hauptgruppe
        $mainGroup = new UserMainGroupChooser('mainGroup', $user);
        $mainGroup->setTitle(RWF::getLanguage()->get('acp.userManagement.form.user.mainGroup'));
        $mainGroup->setDescription(RWF::getLanguage()->get('acp.userManagement.form.user.mainGroup.description'));
        $mainGroup->requiredField(true);
        $this->addFormElement($mainGroup);

        //Benutzergruppen
        $userGroups = new UserGroupChooser('userGroups', $user);
        $userGroups->setTitle(RWF::getLanguage()->get('acp.userManagement.form.user.userGroups'));
        $userGroups->setDescription(RWF::getLanguage()->get('acp.userManagement.form.user.userGroups.description'));
        $this->addFormElement($userGroups);

        //Sprache
        $lang = new LanguageChooser('lang');
        $lang->setTitle(RWF::getLanguage()->get('acp.userManagement.form.user.lang'));
        $lang->setDescription(RWF::getLanguage()->get('acp.userManagement.form.user.lang.description'));
        $lang->requiredField(true);
        $this->addFormElement($lang);

        //Web Style
        $webStyle = new WebStyleChooser('webStyle', ($user instanceof User ? $user->getWebStyle() : ''));
        $webStyle->setTitle(RWF::getLanguage()->get('acp.userManagement.form.user.webStyle'));
        $webStyle->setDescription(RWF::getLanguage()->get('acp.userManagement.form.user.webStyle.description'));
        $webStyle->requiredField(true);
        $this->addFormElement($webStyle);

        RWF::getLanguage()->enableAutoHtmlEndocde();

    }
}

