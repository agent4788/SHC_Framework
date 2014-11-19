<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\IpAddressInputField;
use SHC\UserAtHome\UserAtHome;

/**
 * Benutzer zu Hause Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserAtHomeForm extends DefaultHtmlForm {

    /**
     * @param UserAtHome $userAtHome
     */
    public function __construct(UserAtHome $userAtHome = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Raumname
        $name = new TextField('name', ($userAtHome instanceof UserAtHome ? $userAtHome->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.usersathomeManagement.form.userAtHome.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.usersathomeManagement.form.userAtHome.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //IP Adresse
        $ip = new IpAddressInputField('ip', ($userAtHome instanceof UserAtHome ? $userAtHome->getIpAddress() : null));
        $ip->setTitle(RWF::getLanguage()->get('acp.usersathomeManagement.form.userAtHome.ip'));
        $ip->setDescription(RWF::getLanguage()->get('acp.usersathomeManagement.form.userAtHome.ip.description'));
        $ip->requiredField(true);
        $this->addFormElement($ip);

        //Sichtbarkeit
        $visibility = new OnOffOption('visibility', ($userAtHome instanceof UserAtHome ? $userAtHome->isVisible() : true));
        $visibility->setOnOffLabel();
        $visibility->setTitle(RWF::getLanguage()->get('acp.usersathomeManagement.form.userAtHome.visibility'));
        $visibility->setDescription(RWF::getLanguage()->get('acp.usersathomeManagement.form.userAtHome.visibility.description'));
        $visibility->requiredField(true);
        $this->addFormElement($visibility);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($userAtHome instanceof UserAtHome ? $userAtHome->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.usersathomeManagement.form.userAtHome.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.usersathomeManagement.form.userAtHome.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}