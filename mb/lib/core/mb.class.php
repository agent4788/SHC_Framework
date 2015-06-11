<?php

namespace MB\Core;

//Imports
use MB\Database\NoSQL\Redis;
use RWF\Core\RWF;
use RWF\Session\Session;
use RWF\Settings\Settings;
use RWF\Style\StyleEditor;
use RWF\User\User;

/**
 * Kernklasse (initialisiert die MovieBase)
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.3.0-0
 * @version    2.3.0-0
 */
class MB extends RWF {

    /**
     * Version
     *
     * @var String
     */
    const VERSION = '2.3.0 alpha 1';

    /**
     * Style
     *
     * @var \RWF\Style\Style
     */
    protected static $style = null;

    /**
     * Datenbank
     *
     * @var \MB\Database\NoSQL\Redis
     */
    protected static $redis = null;

    public function __construct() {

        //pruefen ob APP installiert ist
        if(!file_exists(PATH_MB .'app.json')) {

            throw new \Exception('Die App "PCC" ist nicht installiert', 1013);
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
            self::$template->addTemplateDir(PATH_MB . 'data/templates');
            $this->initStyle();
        }
    }

    /**
     * XML Verwaltung initialisieren
     */
    protected function initXml() {


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
            if (self::$visitor instanceof User && self::$visitor->getMobileStyle() != '') {

                $mobileStyle = self::$visitor->getMobileStyle();
            } else {

                $mobileStyle = self::getSetting('pcc.defaultMobileStyle');
            }
            self::$style = StyleEditor::getInstance()->getMobileStyle($mobileStyle);
        } elseif(defined('RWF_DEVICE') && RWF_DEVICE == 'web') {

            //Webstyle laden
            if (self::$visitor instanceof User && self::$visitor->getWebStyle() != '') {

                $webStyle = self::$visitor->getWebStyle();
            } else {

                $webStyle = self::getSetting('pcc.defaultStyle');
            }
            self::$style = StyleEditor::getInstance()->getWebStyle($webStyle);
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
     * @return \MB\Database\NoSQL\Redis
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