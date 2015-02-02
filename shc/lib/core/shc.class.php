<?php

namespace SHC\Core;

//Imports
use RWF\Core\RWF;
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
    const VERSION = '2.2.0';

    /**
     * Raeume XML Datei
     * 
     * @var String
     */
    const XML_ROOM = 'rooms';
    
    /**
     * Raeume UI XML Datei
     * 
     * @var String
     */
    const XML_ROOM_VIEW = 'roomview';
    
    /**
     * Schaltserver XML Datei
     * 
     * @var String
     */
    const XML_SWITCHSERVER = 'switchserver';
    
    /**
     * Bedingungen XML
     * 
     * @var String
     */
    const XML_CONDITIONS = 'conditions';
    
    /**
     * Schaltpunkte XML
     * 
     * @var String
     */
    const XML_SWITCHPOINTS = 'switchpoints';
    
    /**
     * Schaltbare Elemente
     * 
     * @var String
     */
    const XML_SWITCHABLES = 'switchables';
    
    /**
     * Benutzer zu Hause
     *
     * @var String
     */
    const XML_USERS_AT_HOME = 'usersathome';

    /**
     * Sensor Transmitter
     *
     * @var String
     */
    const XML_SENSOR_TRANSMITTER = 'sensortransmitter';

    /**
     * Ereignisse
     *
     * @var String
     */
    const XML_EVENTS = 'events';
    
    /**
     * Style
     * 
     * @var \RWF\Style\Style 
     */
    protected static $style = null;

    /**
     * Datenbank
     *
     * @var \SHC\Database\NoSQL\Redis
     */
    protected static $redis = null;
    
    public function __construct() {

        //XML Initialisieren
        $this->initXml();

        //Basisklasse initalisieren
        parent::__construct();

        //Datenbank Initalisieren
        $this->initDatabase();

        //SHC Initialisieren
        if (ACCESS_METHOD_HTTP) {

            //Template Ordner anmelden
            self::$template->addTemplateDir(PATH_SHC . 'data/templates');
            $this->redirection();
            $this->initStyle();
        }
    }

    /**
     * XML Verwaltung initialisieren
     */
    protected function initXml() {
        
        $fileManager = XmlFileManager::getInstance();
        //$fileManager->registerXmlFile(self::XML_ROOM, PATH_SHC_STORAGE . 'rooms.xml', PATH_SHC_STORAGE . 'default/defaultRooms.xml');
        //$fileManager->registerXmlFile(self::XML_ROOM_VIEW, PATH_SHC_STORAGE . 'roomview.xml', PATH_SHC_STORAGE . 'default/defaultRoomview.xml');
        //$fileManager->registerXmlFile(self::XML_SWITCHSERVER, PATH_SHC_STORAGE . 'switchserver.xml', PATH_SHC_STORAGE . 'default/defaultSwitchserver.xml');
        $fileManager->registerXmlFile(self::XML_CONDITIONS, PATH_SHC_STORAGE . 'conditions.xml', PATH_SHC_STORAGE . 'default/defaultConditions.xml');
        $fileManager->registerXmlFile(self::XML_SWITCHPOINTS, PATH_SHC_STORAGE . 'switchpoints.xml', PATH_SHC_STORAGE . 'default/defaultSwitchpoints.xml');
        $fileManager->registerXmlFile(self::XML_SWITCHABLES, PATH_SHC_STORAGE . 'switchables.xml', PATH_SHC_STORAGE . 'default/defaultSwitchables.xml');
        $fileManager->registerXmlFile(self::XML_USERS_AT_HOME, PATH_SHC_STORAGE . 'usersathome.xml', PATH_SHC_STORAGE . 'default/defaultUsersathome.xml');
        $fileManager->registerXmlFile(self::XML_SENSOR_TRANSMITTER, PATH_SHC_STORAGE . 'sensortransmitter.xml', PATH_SHC_STORAGE . 'default/defaultSensortransmitter.xml');
        $fileManager->registerXmlFile(self::XML_EVENTS, PATH_SHC_STORAGE . 'events.xml', PATH_SHC_STORAGE . 'default/defaultEvents.xml');
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
     * initialisiert den Style
     */
    protected function initStyle() {

        if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

            //Mobilen Style laden
            $mobileStyle = '';
            if (self::$visitor instanceof User && self::$visitor->getMobileStyle() != '') {

                $mobileStyle = self::$visitor->getMobileStyle();
            } else {

                $mobileStyle = self::getSetting('shc.defaultMobileStyle');
            }
            self::$style = StyleEditor::getInstance()->getMobileStyle($mobileStyle);
        } elseif(defined('RWF_DEVICE') && RWF_DEVICE == 'web') {

            //Webstyle laden
            $webStyle = '';
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

        //Einstellungen Speichern
        if (self::$settings instanceof Settings) {

            self::$settings->finalize();
        }

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
