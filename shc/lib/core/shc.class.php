<?php

namespace SHC\Core;

//Imports
use RWF\Core\RWF;
use RWF\Language\Language;
use RWF\Session\Session;
use RWF\Settings\Settings;
use RWF\User\UserEditor;
use RWF\XML\XmlFileManager;
use RWF\Style\StyleEditor;
use RWF\User\User;
use SHC\Database\NoSQL\Redis;

/**
 * Kernklasse (initialisiert das SHC)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.2.0-0
 */
class SHC extends RWF {

    /**
     * Version
     *
     * @var String
     */
    const VERSION = '2.2.5';

    /**
     * Sensor Transmitter
     *
     * @var String
     */
    const XML_SENSOR_TRANSMITTER = 'sensortransmitter';

    /**
     * Schaltserver Einstellungen
     *
     * @var String
     */
    const XML_SWITCHSERVER_SETTINGS = 'switchserversettings';
    
    /**
     * Style
     * 
     * @var \RWF\Style\Style 
     */
    protected static $style = null;
    
    public function __construct() {

        global $argv;

        //XML Initialisieren
        $this->initXml();

        //Berechtigungen initialisieren
        $this->initPermissions();

        //Basisklasse initalisieren
        parent::__construct();

        //pruefen ob App installiert ist
        if (ACCESS_METHOD_HTTP) {

            $found = false;
            foreach (self::$appList as $app) {

                if ($app['app'] == 'shc') {

                    $found = true;
                    break;
                }
            }

            if ($found === false) {

                throw new \Exception('Die App "SHC" ist nicht installiert', 1013);
            }
        }

        //SHC Initialisieren
        if (ACCESS_METHOD_HTTP) {

            //Template Ordner anmelden
            self::$template->addTemplateDir(PATH_SHC . 'data/templates');
            $this->redirection();
            $this->initStyle();
        } else {

            //CLI Anfrage
            if((ACCESS_METHOD_CLI && (in_array('-sh', $argv) || in_array('--sheduler', $argv))) ||
                (ACCESS_METHOD_CLI && (in_array('-sw', $argv) || in_array('--switch', $argv))) ||
                (ACCESS_METHOD_CLI && (in_array('-ds', $argv) || in_array('--daemonstate', $argv)) && file_exists(PATH_RWF . 'db.config.php'))) {

                $this->initDatabase();
                $this->initCliLanguage();
                $this->initSettings();
            } elseif((ACCESS_METHOD_CLI && (in_array('-ss', $argv) || in_array('--switchserver', $argv))) ||
                (ACCESS_METHOD_CLI && (in_array('-st', $argv) || in_array('--sensortransmitter', $argv))) ||
                (ACCESS_METHOD_CLI && (in_array('-h', $argv) || in_array('--help', $argv)))) {

                $this->initCliLanguage();
            }
        }
    }

    /**
     * Datenbankverbindung Initalisieren
     *
     * @throws \Exception
     */
    protected function initDatabase() {

        self::$redis = new Redis();
        self::$redis->connect();
    }

    /**
     * initialisiert die Einstellungen
     */
    protected function initSettings() {

        parent::initSettings();
        $settings = self::$settings;

        //SHC Einstellungen hinzufuegen
        //Allgemein
        $settings->addSetting('shc.ui.redirectActive', Settings::TYPE_BOOL, true);
        $settings->addSetting('shc.ui.redirectPcTo', Settings::TYPE_INT, 1);
        $settings->addSetting('shc.ui.redirectTabletTo', Settings::TYPE_INT, 3);
        $settings->addSetting('shc.ui.redirectSmartphoneTo', Settings::TYPE_INT, 3);
        $settings->addSetting('shc.ui.index.showUsersAtHome', Settings::TYPE_BOOL, true);
        $settings->addSetting('shc.title', Settings::TYPE_STRING, 'SHC 2.2');
        $settings->addSetting('shc.defaultStyle', Settings::TYPE_STRING, 'redmond');
        $settings->addSetting('shc.defaultMobileStyle', Settings::TYPE_STRING, 'default');

        //Sheduler
        $settings->addSetting('shc.shedulerDaemon.active', Settings::TYPE_BOOL, false);
        $settings->addSetting('shc.shedulerDaemon.blinkPin', Settings::TYPE_INT, -1);
        $settings->addSetting('shc.shedulerDaemon.performanceProfile', Settings::TYPE_INT, 2);
    }

    /**
     * initialisiert die Berechtigungen
     */
    protected function initPermissions() {

        $userEditor = UserEditor::getInstance();

        //Benutzerrechte
        $userEditor->addPermission('shc.ucp.viewUserAtHome', true);
        $userEditor->addPermission('shc.ucp.warnings', true);

        //Adminrechte
        $userEditor->addPermission('shc.acp.menu', false);
        $userEditor->addPermission('shc.acp.userManagement', false);
        $userEditor->addPermission('shc.acp.settings', false);
        $userEditor->addPermission('shc.acp.databaseManagement', false);
        $userEditor->addPermission('shc.acp.backupsManagement', false);
        $userEditor->addPermission('shc.acp.roomManagement', false);
        $userEditor->addPermission('shc.acp.switchableManagement', false);
        $userEditor->addPermission('shc.acp.sensorpointsManagement', false);
        $userEditor->addPermission('shc.acp.usersathomeManagement', false);
        $userEditor->addPermission('shc.acp.conditionsManagement', false);
        $userEditor->addPermission('shc.acp.switchpointsManagement', false);
        $userEditor->addPermission('shc.acp.eventsManagement', false);
        $userEditor->addPermission('shc.acp.switchserverManagement', false);
    }

    /**
     * XML Verwaltung initialisieren
     */
    protected function initXml() {
        
        $fileManager = XmlFileManager::getInstance();
        $fileManager->registerXmlFile(self::XML_SWITCHSERVER_SETTINGS, PATH_SHC_STORAGE . 'switchserversettings.xml', PATH_SHC_STORAGE . 'default/defaultSwitchserversettings.xml');
        $fileManager->registerXmlFile(self::XML_SENSOR_TRANSMITTER, PATH_SHC_STORAGE . 'sensortransmitter.xml', PATH_SHC_STORAGE . 'default/defaultSensortransmitter.xml');
    }
    
    /**
     * initialisiert den Style
     */
    protected function initStyle() {

        if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

            //Mobilen Style laden
            if (self::$visitor instanceof User && self::$visitor->getMobileStyle() != '') {

                $mobileStyle = self::$visitor->getMobileStyle();
            } else {

                $mobileStyle = self::getSetting('shc.defaultMobileStyle');
            }
            self::$style = StyleEditor::getInstance()->getMobileStyle($mobileStyle);
        } elseif(defined('RWF_DEVICE') && RWF_DEVICE == 'web') {

            //Webstyle laden
            if (self::$visitor instanceof User && self::$visitor->getWebStyle() != '') {

                $webStyle = self::$visitor->getWebStyle();
            } else {

                $webStyle = self::getSetting('shc.defaultStyle');
            }
            self::$style = StyleEditor::getInstance()->getWebStyle($webStyle);
        }
    }

    /**
     * prueft ob die Auto Umleitung aktiv ist und leitet bei einer neuen Session den Benutzer automatisch um
     */
    protected function redirection() {

        //Mobil Detect einbinden
        require_once(PATH_RWF_CLASSES . 'external/mobile_detect/Mobile_Detect.php');

        $mobilDetect = new \Mobile_Detect();

        //pruefen ob der Zugriff von der Android App kommt
        if($mobilDetect->getUserAgent() == "SHC Android App") {

            //nicht umleiten (unabhaenig von den Einstellungen)
            return;
        }

        //Umleitung fuer PC/Tablet/Smartphone

        /**
         * Einstellung der Umleitung
         *
         * 1 => auf PC oberflaeche leiten
         * 2 => auf Tablet Oberflache leiten
         * 3 => auf Smartphone oberflaeche leiten
         */
        //Geraet feststellen und Umleiten nach den jeweiligen Einstellungen
        $location = 'index.php?app=' . APP_NAME;
        if ($mobilDetect->isTablet()) {

            define('SHC_DETECTED_DEVICE', 'tablet');

            //Tablet
            if (self::$session->isNewSession() && self::$settings->getValue('shc.ui.redirectActive')) {
                switch (self::$settings->getValue('shc.ui.redirectTabletTo')) {

                    case 1:
                        //auf PC Oberflaeche
                        //es muss nicht umgeleitet werden
                        break;
                    case 3:
                        //auf Smartphone Oberflaeche
                        $location .= '&m';
                        break;
                    default :
                        //auf Tablet Oberflaeche
                        $location .= '&t';
                }
            }
        } elseif ($mobilDetect->isMobile()) {

            define('SHC_DETECTED_DEVICE', 'smartphone');

            //Smartphone
            if (self::$session->isNewSession() && self::$settings->getValue('shc.ui.redirectActive')) {
                switch (self::$settings->getValue('shc.ui.redirectSmartphoneTo')) {

                    case 1:
                        //auf PC Oberflaeche
                        //es muss nicht umgeleitet werden
                        break;
                    case 2:
                        //Auf Tablet Oberflaeche
                        $location .= '&t';
                        break;
                    default :
                        //auf Smartphone Oberflaeche
                        $location .= '&m';
                }
            }
        } else {

            define('SHC_DETECTED_DEVICE', 'pc');

            //PC und alles andere
            if (self::$session->isNewSession() && self::$settings->getValue('shc.ui.redirectActive')) {
                switch (self::$settings->getValue('shc.ui.redirectPcTo')) {

                    case 2:
                        //auf Tablet Oberflaeche
                        $location .= '&t';
                        break;
                    case 3:
                        //Auf Smartphone Oberflaeche
                        $location .= '&m';
                        break;
                    default :
                        //auf PC Oberflaeche
                        //es muss nicht umgeleitet werden
                }
            }
        }

        //Header setzen, senden und beenden
        if (self::$session->isNewSession() && self::$settings->getValue('shc.ui.redirectActive') && $location != 'index.php?app=' . APP_NAME) {
            self::$response->addLocationHeader($location);
            self::$response->setBody('');
            self::$response->flush();
            $this->finalize();
            exit(0);
        }
    }

    /**
     * initalisiert die Sprachpakete fuer die Kommandozeile
     */
    protected function initCliLanguage() {

        self::$language = new Language('de');
    }
    
    /**
     * gibt den Style zurueck
     * 
     * @return \RWF\Style\Style
     */
    public static function getStyle() {
        
        return self::$style;
    }

    /**
     * gibt das Datenbankobjekt zutueck
     *
     * @return \SHC\Database\NoSQL\Redis
     */
    public static function getDatabase() {

        return self::$redis;
    }

    /**
     * beendet die Anwendung
     */
    public function finalize() {

        //Sessionobjekt abschliesen
        if (self::$session instanceof Session) {

            self::$session->finalize();
        }

        //Datenbankverbindung beenden
        if(self::$redis instanceof Redis) {

            self::$redis->close();
        }
    }
}
