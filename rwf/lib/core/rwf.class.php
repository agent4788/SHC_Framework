<?php

namespace RWF\Core;

//Imports
use RWF\Database\NoSQL\Redis;
use RWF\Request\Request;
use RWF\Request\SSEResponse;
use RWF\Settings\Settings;
use RWF\Request\HttpRequest;
use RWF\Request\HttpResponse;
use RWF\Request\CliRequest;
use RWF\Request\CliResponse;
use RWF\Session\Session;
use RWF\User\UserEditor;
use RWF\User\User;
use RWF\Language\Language;
use RWF\Template\Template;

/**
 * Kernklasse (initialisiert das RWF)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.2.0-0
 */
class RWF {

    /**
     * Version
     *
     * @var String
     */
    const VERSION = '2.2.4';

    /**
     * Einstellungen
     * 
     * @var \RWF\Settings\Settings 
     */
    protected static $settings = null;

    /**
     * Anfrageobjekt
     * 
     * @var \RWF\Request\Request 
     */
    protected static $request = null;

    /**
     * Antwortobjekt
     * 
     * @var \RWF\Request\Response 
     */
    protected static $response = null;

    /**
     * Sessionobjekt
     * 
     * @var \RWF\Session\Session 
     */
    protected static $session = null;

    /**
     * Besucher Objekt
     * 
     * @var \RWF\User\Visitor 
     */
    protected static $visitor = null;

    /**
     * Sprachverwaltung
     * 
     * @var \RWF\Language\Language 
     */
    protected static $language = null;

    /**
     * Template Engine
     * 
     * @var \RWF\Template\Template 
     */
    protected static $template = null;

    /**
     * Datenbank
     *
     * @var \RWF\Database\NoSQL\Redis
     */
    protected static $redis = null;

    /**
     * liste mit den installierten Apps
     *
     * @var Array
     */
    protected static $appList = array();

    public function __construct() {

        //Multibyte Engine Konfigurieren
        if (function_exists('mb_internal_encoding')) {

            mb_internal_encoding('UTF-8');
            define('MULTIBYTE_STRING', true);
        } else {

            define('MULTIBYTE_STRING', false);
        }

        //Anfrage/Antwort initialisieren
        if (ACCESS_METHOD_HTTP) {
            
            //Anfrage vom Browser
            $this->initDatabase();
            $this->loadApps();
            $this->initSettings();
            $this->initRequest();
            $this->initSession();
            $this->initUser();
            $this->initLanguage();
            $this->initTemplate();
        } else {

            $this->initRequest();
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
     * pruefen ob die angeforderte App installiert ist
     */
    protected function loadApps() {

        $db = self::getDatabase();
        $apps = $db->hGetAllArray('apps');
        foreach($apps as $app) {

            self::$appList[] = $app;
        }

        //APP Liste sortieren
        $orderFunction = function($a, $b) {

            if($a['order'] == $b['order']) {

                return 0;
            }

            if($a['order'] < $b['order']) {

                return -1;
            }
            return 1;
        };
        usort(self::$appList, $orderFunction);
    }

    /**
     * initialisiert die Einstellungen
     */
    protected function initSettings() {

        $settings = new Settings();

        //RWF Einstellungen hinzufuegen
        //Session
        $settings->addSetting('rwf.session.allowLongTimeLogin', Settings::TYPE_BOOL, true);

        //Datum/Zeit
        $settings->addSetting('rwf.date.Timezone', Settings::TYPE_STRING, 'Europe/Berlin');
        $settings->addSetting('rwf.date.defaultDateFormat', Settings::TYPE_STRING, 'd.m.Y');
        $settings->addSetting('rwf.date.defaultTimeFormat', Settings::TYPE_STRING, 'H:i:s');
        $settings->addSetting('rwf.date.useTimeline', Settings::TYPE_BOOL, true);
        $settings->addSetting('rwf.date.sunriseOffset', Settings::TYPE_INT, 0);
        $settings->addSetting('rwf.date.sunsetOffset', Settings::TYPE_INT, 0);
        $settings->addSetting('rwf.date.Latitude', Settings::TYPE_FLOAT, 50.0);
        $settings->addSetting('rwf.date.Longitude', Settings::TYPE_FLOAT, 12.0);

        //Sprache
        $settings->addSetting('rwf.language.defaultLanguage', Settings::TYPE_STRING, 'de');

        //Fritz!Box
        $settings->addSetting('rwf.fritzBox.address', Settings::TYPE_STRING, '');
        $settings->addSetting('rwf.fritzBox.has5GHzWlan', Settings::TYPE_BOOL, false);
        $settings->addSetting('rwf.fritzBox.user', Settings::TYPE_STRING, '');
        $settings->addSetting('rwf.fritzBox.password', Settings::TYPE_STRING, '');

        self::$settings = $settings;
    }

    /**
     * initalisiert die Anfrage und Antwortobjekte
     */
    protected function initRequest() {

        if (ACCESS_METHOD_HTTP) {

            //HTTP Anfrageobjekt initialisieren
            self::$request = new HttpRequest();
            //Angeforderten Geraetetyp ermitteln
            if (self::$request->issetParam('m')) {

                //Smartphone Ansicht
                define('RWF_DEVICE', 'smartphone');
            } elseif (self::$request->issetParam('t')) {

                //Tablet Ansicht
                define('RWF_DEVICE', 'tablet');
            } elseif (self::$request->issetParam('a')) {

                //fuer alle Geraetetypen
                define('RWF_DEVICE', 'all');
            } else {

                //PC/Web Ansicht
                define('RWF_DEVICE', 'web');
            }

            if(self::$request->issetParam('sync', Request::GET)) {

                //Server Sent Event Antwortobjekt initalisieren
                self::$response = new SSEResponse();
            } else {

                //Standard HTTP Antwortobjekt initalisieren
                self::$response = new HttpResponse();
            }
            self::$response->addNoCacheHeader();
        } else {

            //Kommandozeilen Anfrage und Antwort initalisieren
            self::$request = new CliRequest();
            self::$response = new CliResponse();
        }
    }

    /**
     * initialisiert die Session
     */
    protected function initSession() {

        self::$session = new Session();
    }

    /**
     * intialisiert den Benutzer
     */
    protected function initUser() {

        UserEditor::getInstance()->loadData();
        $user = UserEditor::getInstance()->getUserByAuthCode(self::$session->get('authCode'));
        if ($user instanceof User) {

            self::$visitor = $user;
        } else {

            self::$visitor = UserEditor::getInstance()->getGuest();
        }
    }

    /**
     * initialisiert die Sprache
     */
    protected function initLanguage() {

        if (self::$visitor instanceof User) {

            self::$language = new Language(self::$visitor->getLanguage());
        } else {

            self::$language = new Language(self::$settings->getValue('rwf.language.defaultLanguage'));
        }
    }

    /**
     * initialisiert die Template Engine
     */
    protected function initTemplate() {

        $prefix = APP_NAME . '_web_';
        if(RWF_DEVICE == 'smartphone') {

            $prefix = APP_NAME . '_smartphone_';
        } elseif(RWF_DEVICE == 'tablet') {

            $prefix = APP_NAME . '_tablet_';
        } elseif(RWF_DEVICE == 'all') {

            $prefix = APP_NAME . '_all_';
        }

        self::$template = new Template(array(), PATH_RWF_CACHE_TEMPLATES, $prefix, DEVELOPMENT_MODE);
    }

    /**
     * gibt das Datenbankobjekt zutueck
     *
     * @return \RWF\Database\NoSQL\Redis
     */
    public static function getDatabase() {

        return self::$redis;
    }

    /**
     * gibt das Einstellungsobjekt zurueck
     * 
     * @return \RWF\Settings\Settings
     */
    public static function getSettings() {

        return self::$settings;
    }

    /**
     * gibt das Anfrageobjekt zurueck
     * 
     * @return \RWF\Request\Request
     */
    public static function getRequest() {

        return self::$request;
    }

    /**
     * gibt das Antwortobjekt zurueck
     * 
     * @return \RWF\Request\Response
     */
    public static function getResponse() {

        return self::$response;
    }

    /**
     * gibt das Sessionobjekt zurueck
     * 
     * @return \RWF\Session\Session
     */
    public static function getSession() {

        return self::$session;
    }

    /**
     * gibt das Objekt des Besuchers zurueck
     * 
     * @return \RWF\User\Visitor
     */
    public static function getVisitor() {

        return self::$visitor;
    }

    /**
     * gibt das Sprachobjekt zurueck
     * 
     * @return \RWF\Language\Language
     */
    public static function getLanguage() {

        return self::$language;
    }

    /**
     * gibt das Template Objekt zurueck
     * 
     * @return \RWF\Template\Template 
     */
    public static function getTemplate() {

        return self::$template;
    }

    /**
     * gibt den Wert einer Einstellung zurueck
     * 
     * @param  String $name Name der Einstellung
     * @return Mixed
     */
    public static function getSetting($name) {

        return self::$settings->getValue($name);
    }

    /**
     * gibt eine liste mit den installierten Apps zurueck
     *
     * @return Array
     */
    public static function listApps() {

        return self::$appList;
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
