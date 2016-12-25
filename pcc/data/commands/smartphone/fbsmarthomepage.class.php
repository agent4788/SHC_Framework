<?php

namespace PCC\Command\Smartphone;

//Imports
use PCC\Core\PCC;
use RWF\AVM\FritzBoxFactory;
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;

/**
 * Startseite
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FbSmartHomePage extends PageCommand {

    /**
     * Template
     *
     * @var String
     */
    protected $template = 'fbsmarthome.html';

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'pcc.ucp.fbSmartHomeDevices';

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

        $tpl = PCC::getTemplate();

        //Headline Daten
        $tpl->assign('apps', PCC::listApps());
        $tpl->assign('style', PCC::getStyle());
        $tpl->assign('user', PCC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=pcc&m&page=index');
        $tpl->assign('device', PCC_DETECTED_DEVICE);
        $tpl->assign('title', PCC::getLanguage()->get('index.tabs.fbsmarthome'));

        //Daten zusammenstellen
        $fritzBox = FritzBoxFactory::getFritzBox();

        //Smarthome Geräte einlesen
        $fbSmartHome = $fritzBox->getSmartHome();
        $tpl->assign('smartHomeDevices', $fbSmartHome->listDevices());
    }
}