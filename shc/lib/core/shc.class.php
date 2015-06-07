<?php

namespace SHC\Core;

//Imports
use RWF\Core\RWF;
use RWF\Session\Session;
use RWF\Settings\Settings;
use RWF\Util\CliUtil;
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
    const VERSION = '2.2.1';

    /**
     * Sensor Transmitter
     *
     * @var String
     */
    const XML_SENSOR_TRANSMITTER = 'sensortransmitter';
    
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

        global $argv;

        //pruefen ob APP installiert ist
        if(!file_exists(PATH_SHC .'app.json')) {

            throw new \Exception('Die App "SHC" ist nicht installiert', 1013);
        }

        //XML Initialisieren
        $this->initXml();

        //Basisklasse initalisieren
        parent::__construct();

        //SHC Initialisieren
        if (ACCESS_METHOD_HTTP) {

            //Datenbank Initalisieren
            $this->initDatabase();

            //Template Ordner anmelden
            self::$template->addTemplateDir(PATH_SHC . 'data/templates');
            $this->redirection();
            $this->initStyle();
        } elseif((ACCESS_METHOD_CLI && (in_array('-sh', $argv) || in_array('--sheduler', $argv)))
            || (ACCESS_METHOD_CLI && (in_array('-sw', $argv) || in_array('--switch', $argv)))) {

            //Sheduler initalisieren

            //Datenbank Initalisieren
            $this->initDatabase();
        }
    }

    /**
     * XML Verwaltung initialisieren
     */
    protected function initXml() {
        
        $fileManager = XmlFileManager::getInstance();
        $fileManager->registerXmlFile(self::XML_SENSOR_TRANSMITTER, PATH_SHC_STORAGE . 'sensortransmitter.xml', PATH_SHC_STORAGE . 'default/defaultSensortransmitter.xml');
    }

    /**
     * Datenbankverbindung Initalisieren
     *
     * @throws \Exception
     */
    protected function initDatabase() {

        self::$redis = new Redis();

        if(ACCESS_METHOD_CLI) {

            //Zugriff ueber Kommandozeile
            $cli = new CliUtil();
            $error = 0;
            while(true) {

                try {

                    self::$redis->connect();
                    break;
                } catch(\Exception $e) {

                    if($error < 6) {

                        $cli->writeLineColored('Verbindung zur Datenbank Fehlgeschlagen, erneuter Versuch in 10 Sekunden', 'yellow');
                        $error++;
                        sleep(10);
                    } else {

                        $cli->writeLineColored('verbindungsaufbau zur Datenbank Fehlgeschlagen', 'red');
                        throw $e;
                    }
                }
            }
        } else {

            //Webzugriff
            self::$redis->connect();
        }
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
