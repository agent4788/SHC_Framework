<?php

namespace PCC\Command\Web;

//Imports
use RWF\AVM\FritzBoxFactory;
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;

/**
 * Zeigt den Systemstatus an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FBSmartHomeAjax extends AjaxCommand {

    protected $premission = 'pcc.ucp.fbSmartHomeDevices';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Pruefen ob aktiv
        if(!RWF::getSetting('pcc.fritzBox.showSmartHomeDevices')) {

            throw new \Exception('Die Funktion ist deaktiviert', 1014);
        }

        //Template anzeigen
        $tpl = RWF::getTemplate();

        //Daten zusammenstellen
        $fritzBox = FritzBoxFactory::getFritzBox();

        //Smarthome Geräte einlesen
        $fbSmartHome = $fritzBox->getSmartHome();
        $tpl->assign('smartHomeDevices', $fbSmartHome->listDevices());

        //HTML senden
        $this->data = $tpl->fetchString('fbsmarthome.html');
    }
}