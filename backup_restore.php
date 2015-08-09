<?php

/**
 * Wiederherstellen von Backups
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// CLI Hilfsklasse /////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
     * @var Recource
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
     * @param  Recource $handle  Eingabestrom
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
     * @param  String $color Vordergrundfarbe
     * @return String
     * @internal param String $backgroundColor Hintergrundfarbe
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
// Zugriffsart pruefen /////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(PHP_SAPI != 'cli') {

    echo 'bitte über die Kommanozeile ausführen!';
    exit(1);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Auswahl der Backup Datei ////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$cli = new CliUtil();

//Dateien einlesen
$dir = opendir('./shc/backup/');
$files = array();
while($file = readdir($dir)) {

    if(is_file('./shc/backup/'. $file) && preg_match('#\.zip$#i', $file)) {

        $files[] = $file;
    }
}
closedir($dir);

//gefundene Dateien auflisten
echo "+- ID -+- Dateiname --------------------------------------------------------------------------------------------------------------------------------------------+\n";
foreach($files as $index => $fileName) {

    echo "| ". str_pad($index, 4, ' ', STR_PAD_LEFT) ." | ". str_pad($fileName, 150, ' ', STR_PAD_RIGHT) ." |\n";
}
echo "+------+--------------------------------------------------------------------------------------------------------------------------------------------------------+\n";

//Eingabe abfragen
$i = 0;
$valid = false;
while (true) {

    $in = intval($cli->input('wähle die Datei die wiederhergestellt werden soll: '));

    if (array_key_exists($in, $files)) {

        //Datei
        $restoreFile = $files[$in];
        $valid = true;
        break;
    } elseif($i < 5) {

        $cli->writeLineColored('ungültige Datei gewählt', 'red');
        $i++;
    } elseif ($i == 5) {

        $cli->writeLineColored('ungültige Datei gewählt, versuche es später noch einmal', 'red');
        exit(1);
    }
}
if ($valid === false) {

    $cli->writeLnColored('ungültige Eingabe', 'red');
    exit(1);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Sicherheitsabfrage //////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$i = 0;
$valid = true;
while ($i < 5) {

    $safetyRequest = $cli->input('bist du sicher das alle bestehenden Daten gelöscht und durch die Daten des Backups ersetzt werden sollen? (ja|nein): ');

    if (!preg_match('#^(ja)|(j)|(nein)|(n)$#i', $safetyRequest)) {

        $cli->writeLnColored('ungültige Eingabe', 'red');
        $i++;
        $valid = false;
        continue;
    }
    if ($valid === true && preg_match('#^(ja)|(j)$#i', $safetyRequest)) {

        break;
    } elseif ($valid === true && preg_match('#^(nein)|(n)$#i', $safetyRequest)) {

        exit(0);
    }
}

if ($valid === false) {

    $cli->writeLnColored('ungültige Eingabe', 'red');
    exit(1);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Auswahl nach /temp entpacken ////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$zip = new ZipArchive();
if($zip->open('./shc/backup/'. $restoreFile) === true) {

    //Temp Ordner
    if(!is_dir('/tmp/shcBackupRestore')) {

        if(!@mkdir('/tmp/shcBackupRestore', 0777, true)) {

            $cli->writeLineColored('Der Temp Ordner konnte nich erstellt werden', 'red');
        }
    }

    //Entpacken
    if($zip->extractTo('/tmp/shcBackupRestore')) {


    } else {

        $cli->writeLineColored('Die Datei "'. $restoreFile .'" konnte nicht entpackt werden', 'red');
    }
} else {

    $cli->writeLineColored('Die Datei "'. $restoreFile .'" konnte nicht geladen werden', 'red');
}

$cli->writeLineColored('Daten erfolgreich entpackt', 'green');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Redis Verbindungsdaten abfragen /////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

        if (isset($parts[$i]) && (int)$parts[$i] >= 0 && (int)$parts[$i] <= 255) {

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

    $port = $cli->input('Redis Port (6379)');

    //Port nicht aendern
    if (strlen($port) == 0) {

        $port_not_change = true;
        $valid = true;
        break;
    }

    if (!preg_match('#^[0-9]{1,5}$#', $port) || (int)$port <= 0 || (int)$port >= 65000) {

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
$valid_timeout = '6379';
$timeout_not_change = false;
while ($n < 5) {

    $timeout = $cli->input('Redis Timeout (1): ');

    //Port nicht aendern
    if (strlen($timeout) == 0) {

        $timeout_not_change = true;
        $valid = true;
        break;
    }

    if (!preg_match('#^[0-9]{1,2}$#', $timeout) || (int)$timeout <= 0 || (int)$timeout > 10) {

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

    if (!preg_match('#^[0-9]{1,2}$#', $db) || (int)$db < 0 || (int)$db > 30) {

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

//IP Adresse
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
    if (strlen($password) == 0 || strlen($password) > 20) {

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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// mit Redis verbinden /////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$redis = new \Redis();

//Verbinden
if (!$redis->connect($valid_address, $valid_port, $valid_timeout)) {

    $cli->writeLineColored('Verbindung zur Datenbank fehlgeschlagen', 'red');
    exit(1);
}

//Anmelden
if ($valid_password != '') {

    if (!$redis->auth($valid_password)) {

        $cli->writeLineColored('Authentifizierung Fehlgeschlagen', 'red');
        exit(1);
    }
}

//Datenbank auswaehlen
if (!$redis->select($valid_db)) {

    $cli->writeLineColored('Auswahl der Datenbank Fehlgeschlagen', 'red');
    exit(1);
}

//Optionen setzen
$redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
$redis->setOption(\Redis::OPT_PREFIX, 'shc:');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Redis Datenbank leeren //////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$redis->flushDB();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Redis Daten einspielen //////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data = json_decode(file_get_contents('/tmp/shcBackupRestore/database_dump.json'), true);

if(is_array($data)) {

    //AutoIncrements
    $redis->incrBy('autoIncrement:conditions', $data['autoIncrement:conditions']);
    $redis->incrBy('autoIncrement:events', $data['autoIncrement:events']);
    $redis->incrBy('autoIncrement:roomView', $data['autoIncrement:roomView']);
    $redis->incrBy('autoIncrement:roomView_order', $data['autoIncrement:roomView_order']);
    $redis->incrBy('autoIncrement:rooms', $data['autoIncrement:rooms']);
    $redis->incrBy('autoIncrement:switchServers', $data['autoIncrement:switchServers']);
    $redis->incrBy('autoIncrement:switchables', $data['autoIncrement:switchables']);
    $redis->incrBy('autoIncrement:switchpoints', $data['autoIncrement:switchpoints']);
    $redis->incrBy('autoIncrement:usersrathome', $data['autoIncrement:usersrathome']);

    $cli->writeLineColored('Auto Increment Daten erfolgreich wiederhergestellt', 'green');

    //Bedingungen
    if(isset($data['conditions'])) {

        foreach($data['conditions'] as $key => $dataSet) {

            $redis->hSet('conditions', $key, $dataSet);
        }
        $cli->writeLineColored('Bedingungen erfolgreich wiederhergestellt', 'green');
    }

    //Ereignisse
    if(isset($data['events'])) {

        foreach($data['events'] as $key => $dataSet) {

            $redis->hSet('events', $key, $dataSet);
        }
        $cli->writeLineColored('Ereignisse erfolgreich wiederhergestellt', 'green');
    }

    //Raum uebersicht
    if(isset($data['roomView'])) {

        foreach($data['roomView'] as $key => $dataSet) {

            $redis->hSet('roomView', $key, $dataSet);
        }
        $cli->writeLineColored('Raum Übersicht erfolgreich wiederhergestellt', 'green');
    }

    //Raeume
    if(isset($data['rooms'])) {

        foreach($data['rooms'] as $key => $dataSet) {

            $redis->hSet('rooms', $key, $dataSet);
        }
        $cli->writeLineColored('Räume erfolgreich wiederhergestellt', 'green');
    }

    //Sensorpunkte
    if(isset($data['sensors:sensorPoints'])) {

        foreach($data['sensors:sensorPoints'] as $key => $dataSet) {

            $redis->hSet('sensors:sensorPoints', $key, $dataSet);
        }
        $cli->writeLineColored('Sensorpunkte erfolgreich wiederhergestellt', 'green');
    }

    //Sensoren
    if(isset($data['sensors:sensors'])) {

        foreach($data['sensors:sensors'] as $key => $dataSet) {

            $redis->hSet('sensors:sensors', $key, $dataSet);
        }
        $cli->writeLineColored('Sensoren erfolgreich wiederhergestellt', 'green');
    }

    //Sensor Daten
    if(isset($data['sensors:sensorData'])) {

        foreach($data['sensors:sensorData'] as $key => $dataSet) {

            ksort($dataSet, SORT_NUMERIC | SORT_ASC);
            foreach($dataSet as $index => $value) {

                $redis->lPush('sensors:sensorData:'. $key, $value);
            }
        }
        $cli->writeLineColored('Sensor Daten erfolgreich wiederhergestellt', 'green');
    }

    //Schaltserver
    if(isset($data['switchServers'])) {

        foreach($data['switchServers'] as $key => $dataSet) {

            $redis->hSet('switchServers', $key, $dataSet);
        }
        $cli->writeLineColored('Schaltserver erfolgreich wiederhergestellt', 'green');
    }

    //schaltbare Elemente
    if(isset($data['switchables'])) {

        foreach($data['switchables'] as $key => $dataSet) {

            $redis->hSet('switchables', $key, $dataSet);
        }
        $cli->writeLineColored('schaltbare Elemente erfolgreich wiederhergestellt', 'green');
    }

    //Schaltpunkte
    if(isset($data['switchpoints'])) {

        foreach($data['switchpoints'] as $key => $dataSet) {

            $redis->hSet('switchpoints', $key, $dataSet);
        }
        $cli->writeLineColored('Schaltpunkte erfolgreich wiederhergestellt', 'green');
    }

    //Benutzer zu Hause
    if(isset($data['usersrathome'])) {

        foreach($data['usersrathome'] as $key => $dataSet) {

            $redis->hSet('usersrathome', $key, $dataSet);
        }
        $cli->writeLineColored('Benutzer zu Hause erfolgreich wiederhergestellt', 'green');
    }

} else {

    $cli->writeLineColored('Die Datenbanksicherung konnte nicht gelesen werden', 'red');
}

//Datenbankverbindung trennen
$redis->close();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// XML Dateien kopieren ////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//SensorTransmitter
if(file_exists('/tmp/shcBackupRestore/sensortransmitter.xml')) {

    //alte Datei loeschen falls sie existiert
    if(file_exists('/var/www/shc/shc/data/storage/sensortransmitter.xml')) {

        @unlink('/var/www/shc/shc/data/storage/sensortransmitter.xml');
    }

    if(!copy('/tmp/shcBackupRestore/sensortransmitter.xml', '/var/www/shc/shc/data/storage/sensortransmitter.xml')) {

        $cli->writeLineColored('die sensortransmitter.xml Datei konnte nicht kopiert werden', 'red');
    }
    @chmod('/var/www/shc/shc/data/storage/sensortransmitter.xml', 0777);
    @clearstatcache();
    $cli->writeLineColored('die sensortransmitter.xml Datei wurde erfolgreich wiederhergestellt', 'green');
}

//Settings
if(file_exists('/tmp/shcBackupRestore/settings.xml')) {

    //alte Datei loeschen falls sie existiert
    if(file_exists('/var/www/shc/rwf/data/storage/settings.xml')) {

        @unlink('/var/www/shc/rwf/data/storage/settings.xml');
    }

    if(!copy('/tmp/shcBackupRestore/settings.xml', '/var/www/shc/rwf/data/storage/settings.xml')) {

        $cli->writeLineColored('die settings.xml Datei konnte nicht kopiert werden', 'red');
    }
    @chmod('/var/www/shc/rwf/data/storage/settings.xml', 0777);
    @clearstatcache();
    $cli->writeLineColored('die settings.xml Datei wurde erfolgreich wiederhergestellt', 'green');
}

//Users
if(file_exists('/tmp/shcBackupRestore/users.xml')) {

    //alte Datei loeschen falls sie existiert
    if(file_exists('/var/www/shc/rwf/data/storage/users.xml')) {

        @unlink('/var/www/shc/rwf/data/storage/users.xml');
    }

    if(!copy('/tmp/shcBackupRestore/users.xml', '/var/www/shc/rwf/data/storage/users.xml')) {

        $cli->writeLineColored('die users.xml Datei konnte nicht kopiert werden', 'red');
    }
    @chmod('/var/www/shc/rwf/data/storage/users.xml', 0777);
    @clearstatcache();
    $cli->writeLineColored('die users.xml Datei wurde erfolgreich wiederhergestellt', 'green');
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Aufräumen ///////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

@unlink('/tmp/shcBackupRestore/sensortransmitter.xml');
@unlink('/tmp/shcBackupRestore/settings.xml');
@unlink('/tmp/shcBackupRestore/users.xml');
@unlink('/tmp/shcBackupRestore/database_dump.json');
@rmdir('/tmp/shcBackupRestore');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Restore erfolgreich /////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$cli->writeLineColored('Wiederherstellung erfolgreich durchgeführt', 'green');