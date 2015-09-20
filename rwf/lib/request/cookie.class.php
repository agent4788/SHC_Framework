<?php

namespace RWF\Request;

//Imports
use RWF\Util\DataTypeUtil;

/**
 * Cookie Objekt
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Cookie {

    /**
     * Cookie Name
     * 
     * @var String
     */
    protected $name = '';

    /**
     * Cookie Wert
     * 
     * @var String
     */
    protected $value = '';

    /**
     * Cookie Lebensdauer
     * 
     * @var Integer
     */
    protected $time = 0;

    /**
     * Cookie Pfad
     * 
     * @var String
     */
    protected $path = '';

    /**
     * Cookie Domain
     * 
     * @var String
     */
    protected $domain = '';

    /**
     * Sicheres Cookie
     * 
     * @var Boolean
     */
    protected $secure = false;

    /**
     * Cookie Prefix
     * 
     * @var String
     */
    protected $prefix = '';

    /**
     * Cookie HttpOnly
     * 
     * @var Boolean
     */
    protected $httpOnly = true;

    /**
     * Cookie Veraendert
     * 
     * @var Boolean
     */
    protected $modified = false;

    /**
     * @param String $prefix Prefix
     * @param String $name   Name
     * @param String $value  Wert
     */
    public function __construct($prefix, $name, $value = '') {

        $this->prefix = $prefix;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * gibt den Cookie Namen zurueck
     * 
     * @return String Name
     */
    public function getName() {

        return $this->name;
    }

    /**
     * gibt den Wert des Cookies zurueck
     * 
     * @param Integer $dataType Datentyp
     */
    public function getValue($dataType = DataTypeUtil::PLAIN) {

        return DataTypeUtil::checkAndConvert($this->value, $dataType);
    }

    /**
     * gibt den Wert des Cookies als Array zurueck
     * 
     * @return Array Wert
     */
    public function getValueAsArray() {

        return unserialize($this->value);
    }

    /**
     * gibt das Cookie Prefix zurueck
     * 
     * @return String Prefix
     */
    public function getPrefix() {

        return $this->prefix;
    }

    /**
     * setzt den Cookie Namen
     * 
     * @param  String $name name
     * @return \RWF\Request\Cookie
     */
    public function setName($name) {

        $this->name = $name;
        $this->modified = true;

        return $this;
    }

    /**
     * setzt den Wert des Cookies
     * 
     * @param  String $value Wert
     * @return \RWF\Request\Cookie
     */
    public function setValue($value) {

        $this->value = $value;
        $this->modified = true;

        return $this;
    }

    /**
     * setzt den Wert des Cookies von einem Array
     * 
     * @param  Array $value Wert
     * @return \RWF\Request\Cookie
     */
    public function setValueAsArray(array $value) {

        $this->value = serialize($value);
        $this->modified = true;

        return $this;
    }

    /**
     * setzt die Lebenszeit des Cookies
     * 
     * @param  Integer $time Zeit
     * @return \RWF\Request\Cookie
     */
    public function setLiveTime($time) {

        $this->time = TIME_NOW + $time;
        $this->modified = true;

        return $this;
    }

    /**
     * setzt die Lebenszeit des Cookies nach einem Interval
     * 
     * @param  Integer $jears   Jahre
     * @param  Integer $month   Monate
     * @param  Integer $weeks   Wochen
     * @param  Integer $days    Tage
     * @param  Integer $hours   Stunden
     * @param  Integer $minutes Minuten
     * @param  Integer $seconds Sekunden
     * @return \RWF\Request\Cookie
     */
    public function setTimeByInterval($jears = 0, $month = 0, $weeks = 0, $days = 0, $hours = 0, $minutes = 0, $seconds = 0) {

        $time = 0;

        if ($jears > 0) {

            $time += ($jears * 365 * 24 * 60 * 60);
        }

        if ($month > 0) {

            $time += ($month * 30 * 24 * 60 * 60);
        }

        if ($weeks > 0) {

            $time += ($weeks * 7 * 24 * 60 * 60);
        }

        if ($days > 0) {

            $time += ($days * 24 * 60 * 60);
        }

        if ($minutes > 0) {

            $time += ($minutes * 60);
        }

        if ($seconds > 0) {

            $time += $seconds;
        }

        $this->time = $time;
        $this->modified = true;

        return $this;
    }

    /**
     * setzt den Cookie Pfad
     * 
     * @param  String $path Pfad
     * @return \RWF\Request\Cookie
     */
    public function setPath($path) {

        $this->path = $path;
        $this->modified = true;

        return $this;
    }

    /**
     * setzt die Cookie Domein
     * 
     * @param  String  $domain Domain
     * @param  Boolean $secure Port
     * @return \RWF\Request\Cookie
     */
    public function setDomain($domain, $secure = false) {

        $this->domain = $domain;
        if ($secure == true) {

            $this->secure = true;
        } else {

            $this->secure = false;
        }
        $this->modified = true;

        return $this;
    }

    /**
     * Cookie nur ueber HTTP Abrufbar
     * 
     * @param  Boolean $enabled
     * @return \RWF\Request\Cookie
     */
    public function setHttpOnly($enabled = true) {

        if ($enabled == true) {

            $this->httpOnly = true;
        } else {

            $this->httpOnly = false;
        }
        $this->modified = true;

        return $this;
    }

    /**
     * setzt das Cookie Prefix
     * 
     * @param  String $prefix Prefix
     * @return \RWF\Request\Cookie
     */
    public function setPrefix($prefix) {

        $this->prefix = $prefix;
        $this->modified = true;

        return $this;
    }

    /**
     * loescht das Cookie
     */
    public function remove() {

        $this->time = -900;
        $this->modified = true;
    }

    /**
     * gibt an ob das Objekt veraendert wurde
     * 
     * @return Boolean
     */
    public function isModified() {

        return $this->modified;
    }

    /**
     * gibt den HTTP Cookie String zurueck
     * 
     * @return String
     */
    public function fetchString() {

        $code = 'Set-Cookie: ';

        //Name=Wert
        $code .= rawurlencode($this->prefix . $this->name) . '=' . rawurlencode($this->value);

        //Lebensdauer
        if ($this->time != 0) {

            $code .= '; expires=' . gmdate('D, d-M-Y H:i:s', TIME_NOW + $this->time) . ' GMT';
        }

        //Cookie Pfad
        if ($this->path != '') {

            $code .= '; path=' . $this->path;
        }

        //Domain
        if ($this->domain != '') {

            $code .= '; domain=' . $this->domain;
        }

        //Port
        if ($this->secure === true) {

            $code .= '; secure';
        }

        //HttpOnly
        if ($this->httpOnly === true) {

            $code .= '; HttpOnly';
        }

        return $code;
    }

    /**
     * konvertiert die Klasse zu einem String
     * 
     * @return String
     */
    public function __toString() {

        return $this->fetchString();
    }

}
