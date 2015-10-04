<?php

namespace RWF\Request;

/**
 * Server Sent Event
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SSEResponse implements Response {

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
    protected $httpContentType = 'text/event-stream';

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
     * ist beim ersten durchlauf Wahr
     *
     * @var bool
     */
    protected $firstRun = true;

    /**
     * setzt den HTTP Status der Antwort
     *
     * @param String $state HTTP Status
     */
    public function setState($state) {

        $this->httpState = $state;
    }

    /**
     * setzt den Statuscode auf No Coneten, dadurch verbindet sich der Browser nicht automatisch erneut
     */
    public function setNoReconnectHeader() {

        $this->httpState = '204 No Content';
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
    public function addLocationHeader($location) {}

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
    public function addCookie(Cookie $cookie) {}

    /**
     * entfernt ein Cookie wieder
     *
     * @param Cookie $cookie Cookieobjekt
     */
    public function removeCookie(Cookie $cookie) {}

    /**
     * entfernt alle Cookies
     */
    public function removAllCookies() {}

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
     * fuegt ein Data element hinzu
     *
     * @param String $content Inhalt
     */
    public function addData($content) {

        $this->writeLn('data: ' . str_replace(array("\n", "\r"), '', $content));
    }

    /**
     * setzt den Intervall bis zur automatuschen neuverbindung
     *
     * @param int $interval Warteizeit in Milisekunden
     */
    public function addRetry($interval) {

        $this->writeLn('retry: ' . str_replace(array("\n", "\r"), '', $interval));
    }

    /**
     * fuegt eine ID fuer den Datensatz hinzu
     * bei erneuter Vebindung ist die letzte ID im Header "Last-Event-ID" zu finden
     *
     * @param String $id ID
     */
    public function addId($id) {

        $this->writeLn('id: ' . str_replace(array("\n", "\r"), '', $id));
    }

    /**
     * setzt ein Event
     *
     * @param String $event Name des Events
     */
    public function addEvent($event) {

        $this->writeLn('event: ' . str_replace(array("\n", "\r"), '', $event));
    }

    /**
     * fuegt ein Eindimensionales Array als JSON hinzu
     *
     * @param array $data
     */
    public function addArrayAsJson(array $data) {

        $this->addData(json_encode($data));
    }

    /**
     * gibt an ob es sich noch um den ersten durchlauf handelt
     *
     * @return Boolean
     */
    public function isFirstRun() {

        return $this->firstRun;
    }

    /**
     * schreibt eine farbige Zeichenkette in den Body
     *
     * @param String $content        Inhalt
     * @param String $color          Fordergrundfarbe
     * @param String $backgrundColor Hintergrundfarbe
     */
    public function writeColored($content, $color, $backgrundColor = '') {}

    /**
     * schreibt eine farbige Zeichenkette in den Body gefolgt von einem Zeilenumbruch
     *
     * @param String $content        Inhalt
     * @param String $color          Fordergrundfarbe
     * @param String $backgrundColor Hintergrundfarbe
     */
    public function writeLnColored($content, $color, $backgrundColor = '') {}

    /**
     * fuegt eine Zeichenkette in den Body ein
     *
     * @param String  $content   Zeichenkette
     * @param Integer $start     Startposition
     * @param Integer $length    Laenge
     * @param Boolean $overwrite Ueberschreiben
     */
    public function insert($content, $start, $length = -1, $overwrite = false) {}

    /**
     * ersetzt eine Zeichenkette im Body
     *
     * @param  String  $search        Suchstring
     * @param  String  $replace       Ersatzstring
     * @param  Integer $limit         Anzahl an Ersetzungen
     * @param  Boolean $caseIntensive Groß- und Kleinschreibung beachten
     * @return Integer                Anzahl der Ersetzungen
     */
    public function replace($search, $replace, $limit = -1, $caseIntensive = true) {}

    /**
     * ersetzt eine Zeichenkette im Body
     *
     * @param String  $content       Zeichenkette
     * @param Integer $replaceStart  Startposition
     * @param Integer $replaceLength Laenge
     */
    public function replaceByPosition($content, $replaceStart, $replaceLength = -1) {}

    /**
     * gibt den inhalt auf den Standart Datenstrom aus
     */
    public function flush() {

        //nur beim ersten Script durchlauf den Header mit senden
        if($this->firstRun === true) {

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
        }
        //Content Ausgeben
        echo $this->httpBody . "\n";

        //Objekt zuruecksetzen
        $this->firstRun = false;
        $this->httpBody = '';
        $this->i = 0;

        //Ausgabe ohne Cache direkt an den Browser senden
        ob_flush();
        flush();
    }

}
