<?php

namespace RWF\Request;

//Imports
use RWF\Util\CliUtil;

/**
 * CLI Antwort
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class CliResponse implements Response {

    /**
     * inhalt der Ausgabe
     * 
     * @var String 
     */
    protected $body = '';

    /**
     * CLI Util
     * 
     * @var \RWF\Util\CliUtil 
     */
    protected $cli = null;

    public function __construct() {

        $this->cli = new CliUtil(false);
    }

    /**
     * setzt den HTTP Status der Antwort
     * 
     * @param String $state HTTP Status
     */
    public function setState($state) {
        
    }

    /**
     * setzt den MimeType der Antwort
     * 
     * @param String $contentType MimeType
     */
    public function setContentType($contentType) {
        
    }

    /**
     * fuegt einen Header hinzu
     * 
     * @param String  $name      Name
     * @param String  $value     Wert
     * @param Boolean $overwrite bestehenden Header ueberschreiben
     */
    public function addHeader($name, $value, $overwrite = false) {
        
    }

    /**
     * fuegt einen Location Header hinzu
     * 
     * @param String $location Zieladtesse
     */
    public function addLocationHeader($location) {
        
    }

    /**
     * fuegt Header hinzu damit die Seite nicht vom Browser gecasht wird
     */
    public function addNoCacheHeader() {
        
    }

    /**
     * entfernt die No Cash header wieder
     */
    public function removeNoCacheHeader() {
        
    }

    /**
     * fuegt der Antwort ein neues Cookie hinzu
     * 
     * @param Cookie $cookie Cookieobjekt
     */
    public function addCookie(Cookie $cookie) {
        
    }

    /**
     * entfernt ein Cookie wieder
     * 
     * @param Cookie $cookie Cookieobjekt
     */
    public function removeCookie(Cookie $cookie) {
        
    }

    /**
     * entfernt alle Cookies
     */
    public function removAllCookies() {
        
    }

    /**
     * gibt den vollstaendigen HTTP Body zurueck
     * 
     * @return String
     */
    public function getBody() {

        return $this->body;
    }

    /**
     * setzt den HTTP Body (ueberschreibt alle vorherigen Daten)
     * 
     * @param String $content Body
     */
    public function setBody($content) {

        $this->body = $content;
    }

    /**
     * schreibt eine Zeichenkette in den Body
     * 
     * @param String $content Inhalt
     */
    public function write($content) {

        print($content);
    }

    /**
     * schreibt eine Zeichenkette in den Body gefolgt von einem Zeilenumbruch
     * 
     * @param String $content Inhalt
     */
    public function writeLn($content) {

        print($content . "\n");
    }

    /**
     * schreibt eine farbige Zeichenkette in den Body
     * 
     * @param String $content        Inhalt
     * @param String $color          Fordergrundfarbe
     * @param String $backgrundColor Hintergrundfarbe
     */
    public function writeColored($content, $color, $backgrundColor = '') {

        print($this->cli->writeColored($content, $color, $backgrundColor));
    }

    /**
     * schreibt eine farbige Zeichenkette in den Body gefolgt von einem Zeilenumbruch
     * 
     * @param String $content        Inhalt
     * @param String $color          Fordergrundfarbe
     * @param String $backgrundColor Hintergrundfarbe
     */
    public function writeLnColored($content, $color, $backgrundColor = '') {

        print($this->cli->writeLineColored($content, $color, $backgrundColor));
    }

    /**
     * fuegt eine Zeichenkette in den Body ein
     * 
     * @param String  $content   Zeichenkette
     * @param Integer $start     Startposition
     * @param Integer $length    Laenge
     * @param Boolean $overwrite Ueberschreiben
     */
    public function insert($content, $start, $length = -1, $overwrite = false) {
        
    }

    /**
     * ersetzt eine Zeichenkette im Body
     * 
     * @param  String  $search        Suchstring
     * @param  String  $replace       Ersatzstring
     * @param  Integer $limit         Anzahl an Ersetzungen
     * @param  Boolean $caseIntensive Groß- und Kleinschreibung beachten
     * @return Integer                Anzahl der Ersetzungen
     */
    public function replace($search, $replace, $limit = -1, $caseIntensive = true) {
        
    }

    /**
     * ersetzt eine Zeichenkette im Body
     * 
     * @param String  $content       Zeichenkette
     * @param Integer $replaceStart  Startposition
     * @param Integer $replaceLength Laenge
     */
    public function replaceByPosition($content, $replaceStart, $replaceLength = -1) {
        
    }

    /**
     * gibt den inhalt auf den Standart Datenstrom aus
     */
    public function flush() {

        echo $this->body;
    }

}
