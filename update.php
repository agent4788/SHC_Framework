<?php

/**
 * Update von Version 2.2.3 auf 2.2.4
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.2-0
 * @version    2.2.2-0
 */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Hilfsfunktionen /////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function randomStr($length = 10) {

    $set = array("a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J",
        "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r", "R", "s", "S", "t", "T",
        "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z", "1", "2", "3", "4", "5", "6", "7", "8",
        "9");
    $str = '';

    for ($i = 1; $i <= $length; ++$i) {

        $ch = mt_rand(0, count($set) - 1);
        $str .= $set[$ch];
    }

    return $str;
}

/**
 * Kommandozeilen Hilfsfunktionen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class CliUtil {

    /**
     * STDIN Datenstrom
     *
     * @var recource
     */
    protected static $in = null;

    /**
     * Daten direkt ausgeben
     *
     * @var Boolean
     */
    protected $print = true;

    /**
     * Vordergrundfarben
     *
     * @var Array
     */
    protected $foregroundColors = array(
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
        'black_u' => '4;30', // underlined
        'red_u' => '4;31',
        'green_u' => '4;32',
        'yellow_u' => '4;33',
        'blue_u' => '4;34',
        'purple_u' => '4;35',
        'cyan_u' => '4;36',
        'white_u' => '4;37'
    );

    /**
     * Hintergrundfarben
     *
     * @var Array
     */
    protected $backgroundColors = array(
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47'
    );

    /**
     * @param Boolean $print Daten direkt ausgeben
     */
    public function __construct($print = true) {

        $this->print = $print;
    }

    /**
     * gibt den Text an die Kommandozeile aus
     *
     * @param  String $str Text
     * @return String
     */
    public function write($str) {

        if (!$this->print) {

            return $str;
        }
        print($str);
    }

    /**
     * gibt eine Zeile mit dem Text an die Kommandozeile aus
     *
     * @param  String $str Text
     * @return String
     */
    public function writeLine($str) {

        if (!$this->print) {

            return $str . "\n";
        }
        print($str . "\n");
    }

    /**
     * gibt den Text an die Kommandozeile aus
     *
     * @param  String $str             Text
     * @param  String $color           Vordergrundfarbe
     * @param  String $backgroundColor Hintergrundfarbe
     * @return String
     */
    public function writeColored($str, $color, $backgroundColor = null) {

        if (!$this->print) {

            $content = $this->colorStart($color, $backgroundColor);
            $content .= $this->write($str);
            $content .= $this->reset();
            return $content;
        }
        $this->colorStart($color, $backgroundColor);
        $this->write($str);
        $this->reset();
    }

    /**
     * gibt den Text an die Kommandozeile aus
     *
     * @param  String $str             Text
     * @param  String $color           Vordergrundfarbe
     * @param  String $backgroundColor Hintergrundfarbe
     * @return String
     */
    public function writeLineColored($str, $color, $backgroundColor = null) {

        if (!$this->print) {

            $content = $this->colorStart($color, $backgroundColor);
            $content .= $this->writeLine($str);
            $content .= $this->reset();
            return $content;
        }
        $this->colorStart($color, $backgroundColor);
        $this->writeLine($str);
        $this->reset();
    }

    /**
     * gibt eine Eingabeaufforderung aus und gibt die Eingabe als String rurueck
     *
     * @param  String   $message Meldung
     * @param  recource $handle  Eingabestrom
     * @return String            EIngabe
     */
    public function input($message, &$handle = null) {

        //Eingabestrom oeffnen
        if ($handle === null && self::$in == null) {

            self::$in = fopen('php://stdin', 'r');
            $in = self::$in;
        } elseif (self::$in !== null) {

            $in = self::$in;
        } else {

            $in = $handle;
        }

        //Daten vom Eingabestrom lesen
        print($message);
        $data = trim(fgets($in));

        return $data;
    }

    /**
     * alle nach dieser Funktion ausgegebenen Zeichen werden in den festgelegten Fraben angezeigt
     *
     * @param  String $color           Vordergrundfarbe
     * @param  String $backgroundColor Hintergrundfarbe
     * @return String
     */
    public function colorStart($color, $backgroundColor) {

        $return = '';
        if (isset($this->foregroundColors[$color])) {

            if (!$this->print) {

                $return .= "\033[" . $this->foregroundColors[$color] . 'm';
            } else {

                print("\033[" . $this->foregroundColors[$color] . 'm');
            }
        }
        if (isset($this->backgroundColors[$backgroundColor])) {

            if (!$this->print) {

                $return .= "\033[" . $this->backgroundColors[$backgroundColor] . 'm';
            } else {

                print("\033[" . $this->backgroundColors[$backgroundColor] . 'm');
            }
        }

        if (!$this->print) {

            return $return;
        }
    }

    /**
     * alle nach dieser Funktion ausgegebenen Zeichen werden in den neu festgelegten Fraben angezeigt
     *
     * @param  String $color           Vordergrundfarbe
     * @param  String $backgroundColor Hintergrundfarbe
     * @return String
     */
    public function colorNext($color) {

        if (!$this->print) {

            return "\033[" . $this->foregroundColors[$color] . 'm';
        }
        print("\033[" . $this->foregroundColors[$color] . 'm');
    }

    /**
     * setzt alle Farben zurueck
     *
     * @return String
     */
    public function reset() {

        if (!$this->print) {

            return "\033[0m";
        }
        print("\033[0m");
    }

    /**
     * gibt eine Liste mit allen Vordergrundfarben
     *
     * @return Array
     */
    public function listColors() {

        return array_keys($this->foregroundColors);
    }

    /**
     * gibt eine Liste mit allen Hintergrundfarben
     *
     * @return Array
     */
    public function listBackgroundColors() {

        return array_keys($this->backgroundColors);
    }

}

$cli = new CliUtil();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Datenbank verbinden /////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// DB config laden
$dbConfig = array();
if(file_exists(__DIR__ .'/rwf/db.config.php')) {

    require_once(__DIR__ .'/rwf/db.config.php');
} else {

    $cli->writeColored("Das RWF ist nicht installiert, installier das RWF und ab der nächsten Version kannst du dann das Update durchführen!", "red");
    exit(1);
}

$host = $dbConfig['host'];
$port = $dbConfig['port'];
$timeout = $dbConfig['timeout'];
$db = $dbConfig['db'];
$pass = $dbConfig['pass'];

//Redis Datenbank Verbindung aufbauen
$redis = new Redis();

//Verbinden
if(!$redis->connect($host, $port, $timeout)) {

    throw new \Exception('Verbindung zur Datenbank fehlgeschlagen', 1200);
}

//Anmelden
if($pass != '') {

    if(!$redis->auth($pass)) {

        throw new \Exception('Authentifizierung Fehlgeschlagen', 1201);
    }
}

//Datenbank auswaehlen
if(!$redis->select($db)) {

    throw new \Exception('Auswahl der Datenbank Fehlgeschlagen', 1202);
}

//Optionen
$redis->setOption(Redis::OPT_PREFIX, 'rwf:');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update vorbereiten //////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//Initialisieren
$shcInstalled = false;
$shcApiLevel = 12;
$pccInstalled = false;
$pccApiLevel = 12;

//SHC App Daten laden
if($redis->hExists("apps", "shc")) {

    $shcInstalled = true;
    $shcAppData = json_decode($redis->hGet("apps", "shc"), true);
    if(isset($shcAppData['apLevel'])) {

        $shcApiLevel = (int) $shcAppData['apLevel'];
    }
}

//PCC App Daten laden
if($redis->hExists("apps", "pcc")) {

    $pccInstalled = true;
    $pccAppData = json_decode($redis->hGet("apps", "pcc"), true);
    if(isset($pccAppData['apLevel'])) {

        $pccApiLevel = (int) $pccAppData['apLevel'];
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update v2.2.3 auf v2.2.4  (API Level 12 auf 13) /////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//SHC
if($shcInstalled === true && $shcApiLevel == 12) {

    //Update Funktionen
    // nichts zu tun
    $shcApiLevel = 13;
}

//PCC
if($pccInstalled === true && $pccApiLevel == 12) {

    //Update Funktionen
    // nichts zu tun
    $pccApiLevel = 13;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update v2.2.4 auf v2.2.5  (API Level 13 auf 14) /////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//SHC
if($shcInstalled === true && $shcApiLevel == 13) {

    //Update Funktionen
    // nichts zu tun
    $shcApiLevel = 14;
}

//PCC
if($pccInstalled === true && $pccApiLevel == 13) {

    //Update Funktionen
    // nichts zu tun
    $pccApiLevel = 14;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// neue app.json Dateien schreiben /////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//SHC
if($shcInstalled === true) {

    $shcAppData["apLevel"] = $shcApiLevel;
    $redis->hSet("apps", "shc", json_encode($shcAppData));
}

//PCC
if($pccInstalled === true) {

    $pccAppData["apLevel"] = $pccApiLevel;
    $redis->hSet("apps", "pcc", json_encode($pccAppData));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Datenbankverbindung beenden /////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$redis->close();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update erfolgreich //////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$cli->writeLineColored('Update erfolgreich', 'green');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// sich selbst loeschen ////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

@unlink(__FILE__);