<?php

namespace RWF\Session;

//Imports
use RWF\Core\RWF;
use RWF\Request\Cookie;
use RWF\Util\FileUtil;
use RWF\Util\DataTypeUtil;
use RWF\Util\JSON;
use RWF\Util\Message;
use RWF\Util\String;

/**
 * Session
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Session {

    /**
     * Session Variablen
     * 
     * @var Array
     */
    protected $sessionVars = array();

    /**
     * Session Id
     * 
     * @var String
     */
    protected $sid = '';

    /**
     * gibt an ob die Session neu ist
     * 
     * @var Boolean
     */
    protected $newSession = false;

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'sessions';

    /**
     * initialisiert die Session
     */
    public function __construct() {

        /* @var $request \RWF\Request\HttpRequest */
        $request = RWF::getRequest();
        $response = RWF::getResponse();

        //Datenbank
        $db = RWF::getDatabase();

        //Dauerhafte Anmeldung
        $authCodeCookie = $request->getCookie('authCode');
        if ($authCodeCookie instanceof Cookie) {

            $this->set('authCode', $authCodeCookie->getValue(DataTypeUtil::STRING_64));
        }

        //Session Id
        $sessionCookie = $request->getCookie('session');
        if ($sessionCookie instanceof Cookie) {

            $this->sid = $sessionCookie->getValue(DataTypeUtil::STRING_64);
        } else {

            $this->sid = String::randomStr(64);
            $this->newSession = true;
        }

        //Session Daten Laden
        if($db->exists(self::$tableName .':'. $this->sid)) {

            $this->sessionVars = $db->getArray(self::$tableName .':'. $this->sid);
        } else {

            $this->newSession = true;
        }
        
        //Cookie setzen
        if (!$sessionCookie instanceof Cookie) {
            
            //neues Cookie erzeugen
            $sessionCookie = new Cookie(RWF_COOKIE_PREFIX, 'session', $this->sid);
            $response->addCookie($sessionCookie);
        }
        $sessionCookie->setTimeByInterval(0, 0, 0, 0, 0, 15, 0);
    }

    /**
     * gibt die Session ID Zurueck
     * 
     * @return String Session ID
     */
    public function getSessionId() {

        return $this->sid;
    }

    /**
     * aendert die Session Id
     * 
     * @return Boolean
     */
    public function regenerateSessionId() {

        $oldSid = $this->sid;
        $this->sid = String::randomStr(64);
        $sessionCookie = new Cookie(RWF_COOKIE_PREFIX, 'session', $this->sid);
        $sessionCookie->setTimeByInterval(0, 0, 0, 0, 0, 15);
        RWF::getResponse()->addCookie($sessionCookie);

        $db = RWF::getDatabase();
        $data = $db->getArray(self::$tableName .':'. $oldSid);
        $db->setArray(self::$tableName .':'. $this->sid, $data);
        $db->expire(self::$tableName .':'. $this->sid, 900);
        $db->delete(self::$tableName .':'. $oldSid);

        return true;
    }

    /**
     * gibt den Wert einer Session Variable zurueck
     * 
     * @param String $var Variablenname
     */
    public function get($var) {

        if (isset($this->sessionVars['vars'][$var])) {

            return $this->sessionVars['vars'][$var];
        }

        return null;
    }

    /**
     * setzt eine Session Variable
     * 
     * @param String $var   Variablenname
     * @param Mixed  $value Wert
     */
    public function set($var, $value) {

        $this->sessionVars['vars'][$var] = $value;
    }

    /**
     * Session Variable loeschen
     * 
     * @param String $var Variable
     */
    public function remove($var) {

        if (isset($this->sessionVars['vars'][$var])) {

            $this->sessionVars['vars'][$var] = null;
            unset($this->sessionVars['vars'][$var]);
        }
    }

    /**
     * pruft ob eine Session Variable existiert
     * 
     * @param  String  $name Variablenname
     * @return Boolean
     */
    public function issetVar($name) {

        if (isset($this->sessionVars['vars'][$name])) {

            return true;
        }

        return false;
    }

    /**
     * setzt eine Meldung
     * 
     * @param Message $message Meldung
     */
    public function setMessage(Message $message) {

        $this->sessionVars['message'] = $message;
    }

    /**
     * gibt die Meldung zurueck
     * 
     * @return \RWF\Util\Message
     */
    public function getMessage() {

        if (isset($this->sessionVars['message'])) {

            return $this->sessionVars['message'];
        }

        return null;
    }

    /**
     * loescht die Meldung
     */
    public function removeMessage() {

        unset($this->sessionVars['message']);
    }

    /**
     * gibt an ob die Session neu erstellt wurde
     * 
     * @return Boolean
     */
    public function isNewSession() {

        return $this->newSession;
    }

    /**
     * speichert die Session Daten
     */
    public function finalize() {

        $db = RWF::getDatabase();
        $db->setArray(self::$tableName .':'. $this->sid, $this->sessionVars);
        $db->expire(self::$tableName .':'. $this->sid, 900);
    }

}
