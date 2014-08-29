<?php

namespace RWF\Session;

//Imports
use RWF\Core\RWF;
use RWF\Request\Cookie;
use RWF\Util\FileUtil;
use RWF\Util\DataTypeUtil;
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
     * initialisiert die Session
     */
    public function __construct() {

        /* @var $request \RWF\Request\HttpRequest */
        $request = RWF::getRequest();
        $response = RWF::getResponse();

        //Session Ordner erstellen falls nicht vorhanden
        if (!is_dir(PATH_RWF_SESSION)) {

            FileUtil::createDirectory(PATH_RWF_SESSION);
        }

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
        if (file_exists(PATH_RWF_SESSION . $this->sid . '.session.dat')) {

            $this->sessionVars = unserialize(file_get_contents(PATH_RWF_SESSION . $this->sid . '.session.dat'));
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

        //Alte Sessions Loeschen
        $dir = opendir(PATH_RWF_SESSION);
        while ($file = readdir($dir)) {

            if (preg_match('#^(\.|\.\.)$#', $file)) {

                continue;
            }

            $now = new \DateTime('now');
            $now->sub(new \DateInterval('PT15M'));
            $time = new \DateTime();
            $time->setTimestamp(@filemtime(SESSION . $file));
            if ($time < $now) {

                @unlink(SESSION . $file);
            }
        }
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
        setcookie('shcsession', $this->sid, TIME_NOW + 3600);

        @rename(PATH_RWF_SESSION . $oldSid . '.session.dat', PATH_RWF_SESSION . $this->sid . '.session.dat');

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
    public function delete($var) {

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
     * @return utils\Message
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

        $data = serialize($this->sessionVars);
        //Daten Schreiben
        @file_put_contents(PATH_RWF_SESSION . $this->sid . '.session.dat', $data);
    }

}
