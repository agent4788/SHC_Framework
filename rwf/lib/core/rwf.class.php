<?php

namespace RWF\Core;

//Imports
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
 * @version    2.0.0-0
 */
class RWF {

    /**
     * Version
     *
     * @var String
     */
    const VERSION = '2.0.0';

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
            $this->initSettings();
            $this->initRequest();
            $this->initSession();
            $this->initUser();
            $this->initLanguage();
            $this->initTemplate();
        } else {
            
            //CLI Anfrage
            $this->initSettings();
            $this->initRequest();
            $this->initLanguage();
        }
    }

    /**
     * initialisiert die Einstellungen
     */
    protected function initSettings() {

        self::$settings = new Settings();
    }

    /**
     * initalisiert die Anfrage und Antwortobjekte
     */
    protected function initRequest() {

        if (ACCESS_METHOD_HTTP) {

            //HTTP Anfrageobjekt initialisieren
            self::$request = new HttpRequest();

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

        self::$template = new Template(array(), PATH_RWF_CACHE_TEMPLATES, APP_NAME . '_', DEVELOPMENT_MODE);
    }

    /**
     * gibt das Einstellungsobjekt zurueck
     * 
     * @return \RWF\Setting\Settings
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
    }

}
