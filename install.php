<?php

/**
 * Install SHC
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.3-0
 * @version    2.0.3-0
 */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Hilfsfunktionen //////////////////////////////////////////////////////////////////////////////////////////////////////
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
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Datenbank Einstellungen /////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$cli = new CliUtil();

if(!file_exists('./rwf/db.config.php')) {

    //IP Adresse
    $n = 0;
    $valid = true;
    $valid_address = '127.0.0.1';
    $address_not_change = false;
    while ($n < 5) {

        $address = $cli->input('Redis IP Adresse (127.0.0.1): ');

        //Adresse nicht aendern
        if (strlen($address) == 0) {

            $address_not_change = true;
            $valid = true;
            break;
        }

        //Adresse pruefen
        $parts = explode('.', $address);
        for ($i = 0; $i < 3; $i++) {

            if (isset($parts[$i]) && (int) $parts[$i] >= 0 && (int) $parts[$i] <= 255) {

                continue;
            }

            $cli->writeLnColored('ungültige IP Adresse', 'red');
            $n++;
            $valid = false;
            break;
        }

        if ($valid === true) {

            $valid_address = $address;
            break;
        }
    }

    if ($valid === false) {

        $cli->writeLnColored('ungültige Eingabe, versuche es später noch einmal', 'red');
        exit(1);
    }

    //Port
    $n = 0;
    $valid = true;
    $valid_port = '6379';
    $port_not_change = false;
    while ($n < 5) {

        $port = $cli->input('Redis Port (6379): ');

        //Port nicht aendern
        if (strlen($port) == 0) {

            $port_not_change = true;
            $valid = true;
            break;
        }

        if (!preg_match('#^[0-9]{1,5}$#', $port) || (int) $port <= 0 || (int) $port >= 65000) {

            $cli->writeLnColored('ungültiger Port', 'red');
            $n++;
            $valid = false;
            continue;
        }

        if ($valid === true) {

            $valid_port = $port;
            break;
        }
    }

    if ($valid === false) {

        $cli->writeLnColored('ungültige Eingabe, versuche es später noch einmal', 'red');
        exit(1);
    }

    //Timeout
    $n = 0;
    $valid = true;
    $valid_timeout = '1';
    $timeout_not_change = false;
    while ($n < 5) {

        $timeout = $cli->input('Redis Timeout (1): ');

        //Port nicht aendern
        if (strlen($timeout) == 0) {

            $timeout_not_change = true;
            $valid = true;
            break;
        }

        if (!preg_match('#^[0-9]{1,2}$#', $timeout) || (int) $timeout <= 0 || (int) $timeout > 10) {

            $cli->writeLnColored('ungültiger Timeout', 'red');
            $n++;
            $valid = false;
            continue;
        }

        if ($valid === true) {

            $valid_timeout = $timeout;
            break;
        }
    }

    if ($valid === false) {

        $cli->writeLnColored('ungültige Eingabe, versuche es später noch einmal', 'red');
        exit(1);
    }

    //Datenbank
    $n = 0;
    $valid = true;
    $valid_db = '0';
    $db_not_change = false;
    while ($n < 5) {

        $db = $cli->input('Redis Datenbank (0): ');

        //Port nicht aendern
        if (strlen($db) == 0) {

            $db_not_change = true;
            $valid = true;
            break;
        }

        if (!preg_match('#^[0-9]{1,2}$#', $db) || (int) $db < 0 || (int) $db > 30) {

            $cli->writeLnColored('ungültige Datenbank', 'red');
            $n++;
            $valid = false;
            continue;
        }

        if ($valid === true) {

            $valid_db = $db;
            break;
        }
    }

    if ($valid === false) {

        $cli->writeLnColored('ungültige Eingabe, versuche es später noch einmal', 'red');
        exit(1);
    }

    //Passwort
    $n = 0;
    $valid = true;
    $valid_password = '';
    $password_not_change = false;
    while ($n < 5) {

        $password = $cli->input('Redis Passwort (): ');

        //Adresse nicht aendern
        if (strlen($password) == 0) {

            $password_not_change = true;
            $valid = true;
            break;
        }

        //Adresse pruefen
        if(strlen($password) == 0 || strlen($password) > 20) {

            $cli->writeLnColored('ungültiges Passwort', 'red');
            $n++;
            $valid = false;
            break;
        }

        if ($valid === true) {

            $valid_password = $password;
            break;
        }
    }

    if ($valid === false) {

        $cli->writeLnColored('ungültige Eingabe, versuche es später noch einmal', 'red');
        exit(1);
    }

//DB Config erstellen
    $dbConfig =
        "<?php

/**
 * Redis NoSQL Dantenbank Konfiguration
 *
 * @created ". (new DateTime())->format('H:i d.m.Y') ."
 */

\$dbConfig = array(
    'host' => '$valid_address',
    'port' => $valid_port,
    'timeout' => $valid_timeout,
    'pass' => '$valid_password',
    'db' => $valid_db
);
";
    if(@file_put_contents('./rwf/db.config.php', $dbConfig)) {

        $cli->writeLineColored('Die Datenbankkonfiguration wurde erfolgreich erstellt', 'green');
    } else {

        $cli->writeLineColored('Die Datenbankkonfiguration konnte nicht erstellt werden', 'red');
        exit(1);
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Datenbankverbindung herstellen //////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$dbConfig = array();
if(file_exists('./rwf/db.config.php')) {

    require_once('./rwf/db.config.php');
} else {

    $cli->writeLineColored('Die Datenbankkonfiguration fehlt (db.config.php)', 'red');
    exit(1);
}

$host = $dbConfig['host'];
$port = $dbConfig['port'];
$timeout = $dbConfig['timeout'];
$db = $dbConfig['db'];
$pass = $dbConfig['pass'];

$redis = new \Redis();

//Verbinden
if(!$redis->connect($host, $port, $timeout)) {

    $cli->writeLineColored('Verbindung zur Datenbank fehlgeschlagen', 'red');
    exit(1);
}
//Anmelden
if($pass != '') {

    if(!$redis->auth($pass)) {

        $cli->writeLineColored('Authentifizierung Fehlgeschlagen', 'red');
        exit(1);
    }
}
//Datenbank auswaehlen
if(!$redis->select($db)) {

    $cli->writeLineColored('Auswahl der Datenbank Fehlgeschlagen', 'red');
    exit(1);
}

//Optionen
$redis->setOption(\Redis::OPT_PREFIX, 'rwf:');

$cli->writeLineColored('Datenbankverbindung erfolgreich hergestellt', 'green');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SHC installieren ////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//pruefen ob das SHC schon installiert ist
if(!$redis->hExists('apps', 'shc')) {

    //Abfragen ob das SHC installiert werden soll
    $i = 0;
    $valid = true;
    $installShc = false;
    while ($i < 5) {

        $safetyRequest = $cli->input('soll das SHC Installiert werden? (ja|nein): ');

        if (!preg_match('#^(ja)|(j)|(nein)|(n)$#i', $safetyRequest)) {

            $cli->writeLnColored('ungültige Eingabe', 'red');
            $i++;
            $valid = false;
            continue;
        }
        if ($valid === true && preg_match('#^(ja)|(j)$#i', $safetyRequest)) {

            $installShc = true;
            break;
        } elseif ($valid === true && preg_match('#^(nein)|(n)$#i', $safetyRequest)) {

            $installShc = false;
            break;
        }
    }

    if ($valid === false) {

        $cli->writeLnColored('ungültige Eingabe', 'red');
        exit(1);
    }

    //SHC installieren
    if($installShc === true) {

        //APP Daten anmelden
        $redis->hset('apps', 'shc', json_encode(array(
            'app' => 'shc',
            'name' => 'Raspberry Pi SmartHome Control',
            'icon' => './shc/inc/img/shc-icon.png',
            'order' => 10,
            'apLevel' => 13
        )));

        //App erfolgreich installiert
        $cli->writeLineColored('Das SHC wurde erfolgreich installiert', 'green');
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PCC installieren ////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//pruefen ob das PCC schon installiert ist
if(!$redis->hExists('apps', 'pcc')) {

    //Abfragen ob das SHC installiert werden soll
    $i = 0;
    $valid = true;
    $installPcc = false;
    while ($i < 5) {

        $safetyRequest = $cli->input('soll das PCC Installiert werden? (ja|nein): ');

        if (!preg_match('#^(ja)|(j)|(nein)|(n)$#i', $safetyRequest)) {

            $cli->writeLnColored('ungültige Eingabe', 'red');
            $i++;
            $valid = false;
            continue;
        }
        if ($valid === true && preg_match('#^(ja)|(j)$#i', $safetyRequest)) {

            $installPcc = true;
            break;
        } elseif ($valid === true && preg_match('#^(nein)|(n)$#i', $safetyRequest)) {

            $installPcc = false;
            break;
        }
    }

    if ($valid === false) {

        $cli->writeLnColored('ungültige Eingabe', 'red');
        exit(1);
    }

    //PCC installieren
    if($installPcc === true) {

        //APP Daten Anmelden
        $redis->hset('apps', 'pcc', json_encode(array(
            'app' => 'pcc',
            'name' => 'Raspberry Pi Control Center',
            'icon' => './pcc/inc/img/pcc-icon.png',
            'order' => 20,
            'apLevel' => 13
        )));

        //App erfolgreich installiert
        $cli->writeLineColored('Das PCC wurde erfolgreich installiert', 'green');
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Movie Base installieren ////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//pruefen ob die Movie schon installiert ist
if(!$redis->hExists('apps', 'mb')) {

    //Abfragen ob die Movie installiert werden soll
    $i = 0;
    $valid = true;
    $installMb = false;
    while ($i < 5) {

        $safetyRequest = $cli->input('soll die Movie Base Installiert werden? (ja|nein): ');

        if (!preg_match('#^(ja)|(j)|(nein)|(n)$#i', $safetyRequest)) {

            $cli->writeLnColored('ungültige Eingabe', 'red');
            $i++;
            $valid = false;
            continue;
        }
        if ($valid === true && preg_match('#^(ja)|(j)$#i', $safetyRequest)) {

            $installMb = true;
            break;
        } elseif ($valid === true && preg_match('#^(nein)|(n)$#i', $safetyRequest)) {

            $installMb = false;
            break;
        }
    }

    if ($valid === false) {

        $cli->writeLnColored('ungültige Eingabe', 'red');
        exit(1);
    }

    //PCC installieren
    if($installMb === true) {

        //APP Daten Anmelden
        $redis->hset('apps', 'mb', json_encode(array(
            'app' => 'mb',
            'name' => 'Movie Base',
            'icon' => './mb/inc/img/mb-icon.png',
            'order' => 30,
            'apLevel' => 12
        )));

        //App erfolgreich installiert
        $cli->writeLineColored('Die Movie Base wurde erfolgreich installiert', 'green');
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// sich selbst loeschen ////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

@unlink(__FILE__);