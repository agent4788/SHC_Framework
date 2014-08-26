<?php

namespace RWF\Request;

//Imports

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

        $this->body .= $content;
    }

    /**
     * schreibt eine Zeichenkette in den Body gefolgt von einem Zeilenumbruch
     * 
     * @param String $content Inhalt
     */
    public function writeLn($content) {

        $this->body .= $content . "\n";
    }

    /**
     * schreibt eine farbige Zeichenkette in den Body
     * 
     * @param String $content        Inhalt
     * @param String $color          Fordergrundfarbe
     * @param String $backgrundColor Hintergrundfarbe
     */
    public function writeColored($content, $color, $backgrundColor = '') {
        
        $this->body .= $content;
    }

    /**
     * schreibt eine farbige Zeichenkette in den Body gefolgt von einem Zeilenumbruch
     * 
     * @param String $content        Inhalt
     * @param String $color          Fordergrundfarbe
     * @param String $backgrundColor Hintergrundfarbe
     */
    public function writeLnColored($content, $color, $backgrundColor = '') {
        
        $this->body .= $content . "\n";
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
        
        $newBody = String::subString($this->httpBody, 0, $start);
        $newBody .= $content;
        if ($overwrite == true) {

            $newBody .= String::subString($this->httpBody, ($start + ($length === -1 ? String::length($content) : $length)));
        } else {

            $newBody .= String::subString($this->httpBody, $start);
        }

        $this->httpBody = $newBody;
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
        
        $search = preg_quote($search, '#');
        $count = 0;
        $this->httpBody = preg_replace('#' . $search . '#' . ($caseIntensive == true ? '' : 'i'), $replace, $this->httpBody, $limit, $count);
        return $count;
    }

    /**
     * ersetzt eine Zeichenkette im Body
     * 
     * @param String  $content       Zeichenkette
     * @param Integer $replaceStart  Startposition
     * @param Integer $replaceLength Laenge
     */
    public function replaceByPosition($content, $replaceStart, $replaceLength = -1) {
        
        $this->insert($content, $replaceStart, $replaceLength, true);
    }

    /**
     * gibt den inhalt auf den Standart Datenstrom aus
     */
    public function flush() {
        
        echo $this->body;
    }

}
