<?php

namespace RWF\Request;

//Imports
use RWF\Util\DataTypeUtil;
use RWF\Util\ArrayUtil;

/**
 * HTTP Anfrage
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class HttpRequest implements Request {

    /**
     * HTTP Variablen
     *
     * @var array
     */
    protected $httpVars = array();

    /**
     * Cookies
     * 
     * @var Array
     */
    protected $cookies = array();

    /**
     * Initalisiert einen HTTPRequest
     */
    public function __construct() {

        $superglobals = array('_GET', '_POST', '_SERVER', '_COOKIE', '_FILES', '_ENV', 'GLOBALS');
        foreach ($superglobals as $var) {
            if (isset($_REQUEST[$var]) || isset($_FILES[$var])) {
                throw new \Exception('Hacking versuch entdeckt', 10000);
            }
        }

        //Magic Quotes rueckgaengig machen
        if (version_compare(PHP_VERSION, '5.3', '<')) {
            if (get_magic_quotes_gpc()) {
                $_POST = ArrayUtil::stripSlashes($_POST);
                $_GET = ArrayUtil::stripSlashes($_GET);
                $_COOKIE = ArrayUtil::stripSlashes($_COOKIE);
                $_SERVER = ArrayUtil::stripSlashes($_SERVER);
            }
            set_magic_quotes_runtime(0);
            @ini_set('magic_quotes_gpc', 0);
            @ini_set('magic_quotes_runtime', 0);
        }

        //Register Globals rueckguengig machen
        if ((bool) @ini_get('register_globals')) {
            $superglobals = array('_GET', '_POST', '_SERVER', '_COOKIE', '_FILES', '_ENV');
            foreach ($superglobals as $superglobal) {
                $superglobal = array_keys($superglobal);
                foreach ($superglobal as $global) {
                    if (isset($GLOBALS[$global])) {
                        unset($GLOBALS[$global]);
                        unset($GLOBALS[$global]);
                    }
                }
            }
        }

        //Superglobale Variablen
        $this->httpVars['post'] = $_POST;
        $this->httpVars['get'] = $_GET;
        $this->httpVars['server'] = $_SERVER;
        $this->httpVars['cookie'] = $_COOKIE;
        $this->httpVars['file'] = $_FILES;
        $this->httpVars['env'] = $_ENV;
        $this->httpVars['request'] = $_REQUEST;
        
        //Cookies Einlesen
        $this->readCookies();
    }

    /**
     * prueft ob ein Parameter vorhanden ist
     * 
     * @param  String  $name   Name des Parameters
     * @param  String  $method Datenquelle
     * @return Boolean
     */
    public function issetParam($name, $method = self::GET) {

        return isset($this->httpVars[$method][$name]);
    }

    /**
     * gibt den Wert eines Parameters zurueck
     * 
     * @param  String  $name     Name des Parameters
     * @param  String  $method   Datenquelle
     * @param  Integer $dataType Erwarteter Datentyp
     * @return Mixed
     */
    public function getParam($name, $method = self::GET, $dataType = DataTypeUtil::PLAIN) {

        if ($this->issetParam($name, $method)) {

            return DataTypeUtil::checkAndConvert($this->httpVars[$method][$name], $dataType);
        }
        return null;
    }

    /**
     * gibt eine Liste mit allen Parametern zurueck
     * 
     * @param  String $method Datenquelle
     * @return Array
     */
    public function listParamNames($method = 'all') {

        if ($method == 'all') {

            return array_keys($_REQUEST);
        }
        return array_keys($this->httpVars[$method]);
    }

    /**
     * gibt eine Header Variable zurueck
     * 
     * @param  String  $name     Name der Variable
     * @param  Integer $dataType Datentyp
     * @return Mixed
     */
    public function getHeader($name, $dataType = DataTypeUtil::STRING) {

        if ($this->issetParam($name, self::SERVER)) {

            return $this->getParam($name, self::SERVER, $dataType);
        }
        return null;
    }

    /**
     * prueft ob es sich um eine AJAX Anfrage handelt
     * 
     * @return Boolean
     */
    public function isAjaxRequest() {

        if ($this->issetParam('X_REQUESTED_WITH', self::SERVER)) {

            return true;
        }
        return false;
    }

    /**
     * liest alle Cookies ein
     */
    protected function readCookies() {

        foreach ($this->httpVars['cookie'] as $index => $value) {

            $name = str_replace(RWF_COOKIE_PREFIX, '', $index);
            $cookie = new Cookie(RWF_COOKIE_PREFIX, $name, $value);
            $this->cookies[$name] = $cookie;
        }
    }

    /**
     * gibt ein Cookie zurueck
     * 
     * @param  String $name Name des Cookies
     * @return \RWF\Request\Cookie
     */
    public function getCookie($name) {

        if (isset($this->cookies[$name])) {

            return $this->cookies[$name];
        }

        return null;
    }

    /**
     * gibt eine Liste mit allen Cookies zurueck
     * 
     * @return Array
     */
    public function getCookies() {

        return $this->cookies;
    }

    /**
     * gibt ein Dateiobjektz zur Hochgeladenen Datei zurueck
     * 
     * @param  String $name Name des Upload Feldes
     * @return \RWF\Request\File
     */
    public function getFile($name) {

        if (isset($_FILES[$name])) {

            return new File($name, $_FILES[$name]);
        }
        return null;
    }

    /**
     * gibt eine Liste mit allen Hochgeladenen dateiobjekten zurueck
     * 
     * @return Array
     */
    public function getFiles() {

        $array = array();
        foreach ($_FILES as $name => $file) {

            $array[] = new File($name, $file);
        }

        return $array;
    }

}
