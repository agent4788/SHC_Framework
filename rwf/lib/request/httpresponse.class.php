<?php

namespace RWF\Request;

//Imports
use RWF\Util\String;
use RWF\Core\RWF;

/**
 * HTTP Antwort
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class HttpResponse implements Response {

    /**
     * HTTP Status
     *
     * @var string
     */
    protected $httpState = '200 OK';

    /**
     * HTTP Content Type
     *
     * @var string
     */
    protected $httpContentType = 'text/html';

    /**
     * HTTP Header Felder
     *
     * @var array
     */
    protected $httpHeader = array();

    /**
     * HTTP Body
     *
     * @var string
     */
    protected $httpBody = '';

    /**
     * Cookieobjekte
     * 
     * @var Array
     */
    protected $cookies = array();

    /**
     * Zaehlervariable
     *
     * @var int
     */
    protected $i = 0;
    
    /**
     * setzt den HTTP Status der Antwort
     * 
     * @param String $state HTTP Status
     */
    public function setState($state) {

        $this->httpState = $state;
    }

    /**
     * setzt den MimeType der Antwort
     * 
     * @param String $contentType MimeType
     */
    public function setContentType($contentType) {

        $this->httpContentType = $contentType;
    }

    /**
     * fuegt einen Header hinzu
     * 
     * @param String  $name      Name
     * @param String  $value     Wert
     * @param Boolean $overwrite bestehenden Header ueberschreiben
     */
    public function addHeader($name, $value, $overwrite = false) {

        $this->httpHeader[$this->i]['name'] = $name;
        $this->httpHeader[$this->i]['value'] = $value;
        if ($overwrite == true) {

            $this->httpHeader[$this->i]['overwrite'] = true;
        } else {

            $this->httpHeader[$this->i]['overwrite'] = false;
        }
        $this->i++;
    }

    /**
     * fuegt einen Location Header hinzu
     * 
     * @param String $location Zieladtesse
     */
    public function addLocationHeader($location) {

        $this->addHeader('Location', $location, true);
    }

    /**
     * fuegt Header hinzu damit die Seite nicht vom Browser gecasht wird
     */
    public function addNoCacheHeader() {

        $this->addHeader('Expires', 'Mon, 11 Jul 1980 00:00:00 GMT', true);
        $this->addHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
        $this->addHeader('Cache-Control', 'no-cache, must-revalidate', true);
        $this->addHeader('Pragma', 'no-cache', true);
    }

    /**
     * entfernt die No Cash header wieder
     */
    public function removeNoCacheHeader() {

        foreach ($this->httpHeader as $key => $value) {

            if ($value['name'] == 'Expires' || $value['name'] == 'Last-Modified' || $value['name'] == 'Cache-Control' || $value['name'] == 'Pragma') {
                unlink($this->httpHeader[$key]);
            }
        }
    }

    /**
     * fuegt der Antwort ein neues Cookie hinzu
     * 
     * @param Cookie $cookie Cookieobjekt
     */
    public function addCookie(Cookie $cookie) {

        $this->cookies[] = $cookie;
    }

    /**
     * entfernt ein Cookie wieder
     * 
     * @param Cookie $cookie Cookieobjekt
     */
    public function removeCookie(Cookie $cookie) {

        $this->cookies = array_diff($this->cookies, array($cookie));
    }

    /**
     * entfernt alle Cookies
     */
    public function removAllCookies() {

        $this->cookies = array();
    }

    /**
     * gibt den vollstaendigen HTTP Body zurueck
     * 
     * @return String
     */
    public function getBody() {

        return $this->httpBody;
    }

    /**
     * setzt den HTTP Body (ueberschreibt alle vorherigen Daten)
     * 
     * @param String $content Body
     */
    public function setBody($content) {

        $this->httpBody = $content;
    }

    /**
     * schreibt eine Zeichenkette in den Body
     * 
     * @param String $content Inhalt
     */
    public function write($content) {

        $this->httpBody .= $content;
    }

    /**
     * schreibt eine Zeichenkette in den Body gefolgt von einem Zeilenumbruch
     * 
     * @param String $content Inhalt
     */
    public function writeLn($content) {

        $this->httpBody .= $content . "\n";
    }

    /**
     * schreibt eine farbige Zeichenkette in den Body
     * 
     * @param String $content        Inhalt
     * @param String $color          Fordergrundfarbe
     * @param String $backgrundColor Hintergrundfarbe
     */
    public function writeColored($content, $color, $backgrundColor = '') {

        $this->httpBody .= '<span style="color' . $color . ';' . ($backgrundColor != '' ? 'background-color: ' . $backgrundColor . ';' : '') . '">' . $content . '</span>';
    }

    /**
     * schreibt eine farbige Zeichenkette in den Body gefolgt von einem Zeilenumbruch
     * 
     * @param String $content        Inhalt
     * @param String $color          Fordergrundfarbe
     * @param String $backgrundColor Hintergrundfarbe
     */
    public function writeLnColored($content, $color, $backgrundColor = '') {

        $this->httpBody .= '<span style="color' . $color . ';' . ($backgrundColor != '' ? 'background-color: ' . $backgrundColor . ';' : '') . '">' . $content . '</span><br/>' . "\n";
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

        //Pruefen ob die Header schon gesendet wurden
        if (headers_sent()) {

            throw new \Exception('Die HTTP Header wurden schon gesendet', 1012);
        }

        //Header
        @header('HTTP/1.0 ' . $this->httpState, true);
        @header('Content-type: ' . $this->httpContentType . '; charset=utf-8', true);

        foreach ($this->httpHeader as $header) {

            @header($header['name'] . ': ' . $header['value'], $header['overwrite']);
        }

        //Cookies senden
        $cookies = RWF::getRequest()->getCookies();
        foreach ($cookies as $cookie) {

            if ($cookie instanceof Cookie && $cookie->isModified()) {

                @header($cookie->fetchString(), false);
            }
        }

        foreach ($this->cookies as $cookie) {

            if ($cookie instanceof Cookie) {

                @header($cookie->fetchString(), false);
            }
        }

        //Content Ausgeben
        echo $this->httpBody;

        //Objekt zuruecksetzen
        $this->httpHeader = array();
        $this->httpState = self::STATE_OK;
        $this->httpContentType = 'text/html';
        $this->httpBody = '';
        $this->cookies = array();
        $this->i = 0;
    }

}
