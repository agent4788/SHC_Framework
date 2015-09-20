<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\Select;
use RWF\Form\FormElements\TextField;
use RWF\Runtime\RaspberryPi;
use SHC\Arduino\Arduino;
use SHC\Form\FormElements\IpAddressInputField;
use SHC\SwitchServer\SwitchServer;

/**
 * Schaltserver Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchServerForm extends DefaultHtmlForm {

    /**
     * @param SwitchServer $userAtHome
     */
    public function __construct(SwitchServer $switchServer = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Raumname
        $name = new TextField('name', ($switchServer instanceof SwitchServer ? $switchServer->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //IP Adresse
        $ip = new IpAddressInputField('ip', ($switchServer instanceof SwitchServer ? $switchServer->getIpAddress() : null));
        $ip->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.ip'));
        $ip->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.ip.description'));
        $ip->requiredField(true);
        $this->addFormElement($ip);

        //Port
        $port = new IntegerInputField('port', ($switchServer instanceof SwitchServer ? $switchServer->getPort() : 9274), array('min' => 1, 'max' => 65535));
        $port->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.port'));
        $port->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.port.description'));
        $port->requiredField(true);
        $this->addFormElement($port);

        //Timeout
        $timeout = new IntegerInputField('timeout', ($switchServer instanceof SwitchServer ? $switchServer->getTimeout() : 1), array('min' => 1, 'max' => 60));
        $timeout->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.timeout'));
        $timeout->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.timeout.description'));
        $timeout->requiredField(true);
        $this->addFormElement($timeout);

        //Model
        $model = new Select('model');
        $model->setValues(array(
            RaspberryPi::MODEL_A => array('Model A', ($switchServer instanceof SwitchServer && $switchServer->getModel() == RaspberryPi::MODEL_A ? 1 : 0)),
            RaspberryPi::MODEL_A_PLUS => array('Model A+', ($switchServer instanceof SwitchServer && $switchServer->getModel() == RaspberryPi::MODEL_A_PLUS ? 1 : 0)),
            RaspberryPi::MODEL_B => array('Model B', (($switchServer instanceof SwitchServer && $switchServer->getModel() == RaspberryPi::MODEL_B) || !$switchServer instanceof SwitchServer ? 1 : 0)),
            RaspberryPi::MODEL_B_PLUS => array('Model B+', ($switchServer instanceof SwitchServer && $switchServer->getModel() == RaspberryPi::MODEL_B_PLUS ? 1 : 0)),
            RaspberryPi::MODEL_A_PLUS => array('Model A+', ($switchServer instanceof SwitchServer && $switchServer->getModel() == RaspberryPi::MODEL_A_PLUS ? 1 : 0)),
            RaspberryPi::MODEL_COMPUTE_MODULE => array('Compute Modul', ($switchServer instanceof SwitchServer && $switchServer->getModel() == RaspberryPi::MODEL_COMPUTE_MODULE ? 1 : 0)),
            RaspberryPi::MODEL_COMPUTE_MODULE => array('Compute Modul', ($switchServer instanceof SwitchServer && $switchServer->getModel() == RaspberryPi::MODEL_COMPUTE_MODULE ? 1 : 0)),
            RaspberryPi::MODEL_2_B => array('Model 2 B', ($switchServer instanceof SwitchServer && $switchServer->getModel() == RaspberryPi::MODEL_2_B ? 1 : 0)),
            Arduino::PRO_MINI => array('Arduino Pro Mini', ($switchServer instanceof SwitchServer && $switchServer->getModel() == Arduino::PRO_MINI ? 1 : 0)),
            Arduino::NANO => array('Arduino Nano', ($switchServer instanceof SwitchServer && $switchServer->getModel() == Arduino::NANO ? 1 : 0)),
            Arduino::UNO => array('Arduino Uno', ($switchServer instanceof SwitchServer && $switchServer->getModel() == Arduino::UNO ? 1 : 0)),
            Arduino::MEGA => array('Arduino Mega', ($switchServer instanceof SwitchServer && $switchServer->getModel() == Arduino::MEGA ? 1 : 0)),
            Arduino::DUE => array('Arduino Due', ($switchServer instanceof SwitchServer && $switchServer->getModel() == Arduino::DUE ? 1 : 0)),
            Arduino::ESP8266_01 => array('ESP8266-01', ($switchServer instanceof SwitchServer && $switchServer->getModel() == Arduino::ESP8266_01 ? 1 : 0)),
            Arduino::ESP8266_12 => array('ESP8266-12', ($switchServer instanceof SwitchServer && $switchServer->getModel() == Arduino::ESP8266_12 ? 1 : 0))
        ));
        $model->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.model'));
        $model->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.model.description'));
        $model->requiredField(true);
        $this->addFormElement($model);

        //Funksteckdosen
        $radioSockets = new OnOffOption('radioSockets', ($switchServer instanceof SwitchServer ? $switchServer->isRadioSocketsEnabled() : true));
        $radioSockets->setActiveInactiveLabel();
        $radioSockets->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.radioSockets'));
        $radioSockets->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.radioSockets.description'));
        $radioSockets->requiredField(true);
        $this->addFormElement($radioSockets);

        //GPIOs lesen
        $readGPIO = new OnOffOption('readGPIO', ($switchServer instanceof SwitchServer ? $switchServer->isReadGpiosEnabled() : false));
        $readGPIO->setActiveInactiveLabel();
        $readGPIO->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.readGPIO'));
        $readGPIO->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.readGPIO.description'));
        $readGPIO->requiredField(true);
        $this->addFormElement($readGPIO);

        //GPIOs schreiben
        $writeGPIO = new OnOffOption('writeGPIO', ($switchServer instanceof SwitchServer ? $switchServer->isWriteGpiosEnabled() : false));
        $writeGPIO->setActiveInactiveLabel();
        $writeGPIO->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.writeGPIO'));
        $writeGPIO->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.writeGPIO.description'));
        $writeGPIO->requiredField(true);
        $this->addFormElement($writeGPIO);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($switchServer instanceof SwitchServer ? $switchServer->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.switchserverManagement.form.switchServer.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}