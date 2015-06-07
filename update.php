<?php

/**
 * Update von Version 2.0.2 auf 2.0.3
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
define('TYPE_BOOLEAN', 'bool');
define('TYPE_STRING', 'string');
define('TYPE_INTEGER', 'int');

function addSetting($name, $value, $type) {

    global $settingsXml;

    //pruefen ob Einstellung schon vorhanden
    foreach($settingsXml->setting as $setting) {

        $attr = $setting->attributes();
        if($attr->name == $name) {

            return;
        }
    }

    $setting = $settingsXml->addChild('setting');
    $setting->addAttribute('name', $name);
    $setting->addAttribute('value', $value);
    $setting->addAttribute('type', $type);
}

function addPremission($xml, $name, $value) {

    //pruefen ob Recht schon vorhanden
    foreach($xml->premission as $premission) {

        $attr = $premission->attributes();
        if($attr->name == $name) {

            return;
        }
    }

    $premission = $xml->addChild('premission');
    $premission->addAttribute('name', $name);
    $premission->addAttribute('value', $value);
}

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
// Update v2.0.0-2 auf v2.0.3 //////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//Datenupdate Ereignisse
if(file_exists(__DIR__ .'/shc/data/storage/events.xml')) {

    $xml = new SimpleXMLElement(__DIR__ .'/shc/data/storage/events.xml', null, true);

    foreach($xml->event as $event) {

        if(!isset($event->lastExecute)) {

            $event->addChild('lastExecute', '2000-01-01 00:00:00');
        }
    }

    $xml->asXML(__DIR__ .'/shc/data/storage/events.xml');
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update v2.0.3 auf v2.2.0 ////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(file_exists('./shc/data/storage/switchables.xml')) {

    //Redis Einstellungen abfragen
    $cli = new CliUtil();

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

    //neue Einstellungen und Rechte eintragen
    $settingsXml = new SimpleXMLElement('./rwf/data/storage/settings.xml', null, true);

    //Datenbank
    addSetting('shc.redis.host', $valid_address, TYPE_STRING);
    addSetting('shc.redis.port', $valid_port, TYPE_INTEGER);
    addSetting('shc.redis.timeout', $valid_timeout, TYPE_INTEGER);
    addSetting('shc.redis.db', $valid_db, TYPE_INTEGER);
    addSetting('shc.redis.pass', $valid_password, TYPE_STRING);

    //Sonstige Einstellungen
    addSetting('rwf.date.useTimeline', 'true', TYPE_BOOLEAN);
    addSetting('shc.shedulerDaemon.blinkPin', '-1', TYPE_INTEGER);
    addSetting('shc.sensorTransmitter.blinkPin', '-1', TYPE_INTEGER);

    //XML Speichern
    $settingsXml->asXML('./rwf/data/storage/settings.xml');

    $usersXml = new SimpleXMLElement('./rwf/data/storage/users.xml', null, true);

    //Gruppenrechte vorbereiten
    foreach ($usersXml->groups->group as $group) {

        //Rechte
        addPremission($group, 'shc.ucp.warnings', '0');
        addPremission($group, 'shc.acp.databaseManagement', '0');
    }

    //XML Speichern
    $usersXml->asXML('./rwf/data/storage/users.xml');

    //Daten aus den XML Dateien in Redis Übertragen
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

    //Bedingungen importieren
    if(file_exists('./shc/data/storage/conditions.xml')) {

        $conditionsXml = new SimpleXMLElement('./shc/data/storage/conditions.xml', null, true);

        //Daten einlesen
        foreach ($conditionsXml->condition as $condition) {

            //Variablen Vorbereiten
            $data = array();
            foreach ($condition as $index => $value) {

                if (!in_array($index, array('id', 'name', 'class', 'enabled'))) {
                    $data[$index] = (string) $value;
                }
            }

            $conditionData = array(
                'id' => (int) $condition->id,
                'class' => (string) $condition->class,
                'name' => (string) $condition->name,
                'enabled' => (((int) $condition->enabled == 1 ? true : false) == true ? true : false)
            );
            $conditionData = array_merge($conditionData, $data);

            if($redis->hSetNx('conditions', (int) $condition->id, $conditionData) == 0) {

                //Import Fehler
                $cli->writeLineColored('Import der Bedingung "'. (string) $condition->name .'" fehlgeschlagen', 'red');
            }
        }

        //Autoincrement Setzen
        $redis->incrBy('autoIncrement:conditions', (int) $conditionsXml->nextAutoIncrementId);
    }

    //Ereignisse importieren
    if(file_exists('./shc/data/storage/events.xml')) {

        $eventsXml = new SimpleXMLElement('./shc/data/storage/events.xml', null, true);

        //Daten einlesen
        foreach ($eventsXml->event as $event) {

            $data = array();
            foreach ($event as $index => $value) {

                if (!in_array($index, array('id', 'name', 'class', 'enabled', 'conditions', 'lastExecute', 'switchable'))) {

                    $data[$index] = (string) $value;
                }
            }

            $eventData = array(
                'id' => (int) $event->id,
                'class' => (string) $event->class,
                'name' => (string) $event->name,
                'enabled' => ((int) $event->enabled == true ? true : false),
                'conditions' => explode(',', (string) $event->conditions),
                'lastExecute' => (string) $event->lastExecute
            );
            $eventData = array_merge($eventData, $data);

            if($redis->hSetNx('events', (int) $event->id, $eventData) == 0) {

                //Import Fehler
                $cli->writeLineColored('Import des Ereignisses "'. (string) $event->name .'" fehlgeschlagen', 'red');
            }
        }

        //Autoincrement Setzen
        $redis->incrBy('autoIncrement:events', (int) $eventsXml->nextAutoIncrementId);
    }

    //Raeume importieren
    if(file_exists('./shc/data/storage/rooms.xml')) {

        $roomsXml = new SimpleXMLElement('./shc/data/storage/rooms.xml', null, true);

        //Daten einlesen
        foreach ($roomsXml->room as $room) {

            $roomData = array(
                'id' => (int) $room->id,
                'name' => (string) $room->name,
                'orderId' => (int) $room->orderId,
                'enabled' => ((int) $room->enabled == 1 ? true : false),
                'allowedUserGroups' => explode(',', (string) $room->allowedUserGroups)
            );

            if($redis->hSetNx('rooms', (int) $room->id, $roomData) == 0) {

                //Import Fehler
                $cli->writeLineColored('Import des Raum "'. (string) $room->name .'" fehlgeschlagen', 'red');
            }
        }

        //Autoincrement Setzen
        $redis->incrBy('autoIncrement:rooms', (int) $roomsXml->nextAutoIncrementId);
    }

    //Schaltbare Elemente importieren
    if(file_exists('./shc/data/storage/switchables.xml')) {

        $switchablesXml = new SimpleXMLElement('./shc/data/storage/switchables.xml', null, true);

        //Daten einlesen
        foreach ($switchablesXml->switchable as $switchable) {

            //Objekte initialisiernen und Spezifische Daten setzen
            $data = array();
            switch ((int) $switchable->type) {

                case 1:

                    //Aktivitaet
                    $list = array();
                    foreach ($switchable->switchable as $activitySwitchable) {

                        /* @var $activitySwitchable \SimpleXmlElement */
                        $attributes = $activitySwitchable->attributes();
                        $list[] = array('id' => (int) $attributes->id, 'command' => (int) $attributes->command);
                    }
                    $data = array(
                        'switchable' => $list,
                        'buttonText' => 1
                    );
                    break;
                case 4:

                    //Countdown
                    $list = array();
                    foreach ($switchable->switchable as $countdownSwitchable) {

                        /* @var $countdownSwitchable \SimpleXmlElement */
                        $attributes = $countdownSwitchable->attributes();
                        $list[] = array('id' => (int) $attributes->id, 'command' => (int) $attributes->command);
                    }
                    $data = array(
                        'switchable' => $list,
                        'interval' => (string) $switchable->interval,
                        'switchOffTime' => '2000-01-01 00:00:00'
                    );
                    break;
                case 8:

                    //Funksteckdose
                    $data = array(
                        'protocol' => (string) $switchable->protocol,
                        'systemCode' => (string) $switchable->systemCode,
                        'deviceCode' => (string) $switchable->deviceCode,
                        'continuous' => (string) $switchable->continuous,
                        'buttonText' => 1
                    );
                    break;
                case 16:

                    //GPIO Output
                    $data = array(
                        'switchServer' => (int) $switchable->switchServer,
                        'pinNumber' => (int) $switchable->pinNumber,
                        'buttonText' => 1
                    );
                    break;

                case 32:

                    //Wake On Lan
                    $data = array(
                        'mac' => (string) $switchable->mac,
                        'ipAddress' => (string) $switchable->mac
                    );
                    break;
                case 128:

                    //GPIO Input
                    $data = array(
                        'switchServer' => (int) $switchable->switchServer,
                        'pinNumber' => (int) $switchable->pinNumber
                    );
                    break;
                default:

                    $cli->writeLineColored('Import des schaltbaren Elementes "'. (string) $switchable->name .'" fehlgeschlagen', 'red');
                    continue;
            }

            $switchableData = array(
                'type' => (int) $switchable->type,
                'id' => (int) $switchable->id,
                'name' => (string) $switchable->name,
                'order' => array(),
                'enabled' => ((string) $switchable->enabled == 1 ? true : false),
                'visibility' => ((string) $switchable->visibility == 1 ? true : false),
                'state' => (int) $switchable->state,
                'icon' => (string) $switchable->icon,
                'rooms' => array(),
                'switchPoints' => explode(',', (string) $switchable->switchPoints),
                'allowedUserGroups' => explode(',', (string) $switchable->allowedUserGroups)
            );
            $switchableData = array_merge($switchableData, $data);

            if($redis->hSetNx('switchables', (int) $switchable->id, $switchableData) == 0) {

                //Import Fehler
                $cli->writeLineColored('Import des schaltbaren Elementes "'. (string) $switchable->name .'" fehlgeschlagen', 'red');
            }
        }

        //Autoincrement Setzen
        $redis->incrBy('autoIncrement:switchables', (int) $roomsXml->nextAutoIncrementId);
    }

    //Schaltpunkte importieren
    if(file_exists('./shc/data/storage/switchpoints.xml')) {

        $switchpointsXml = new SimpleXMLElement('./shc/data/storage/switchpoints.xml', null, true);

        //Daten einlesen
        foreach ($switchpointsXml->switchPoint as $switchPoint) {

            $switchPointData = array(
                'id' => (int) $switchPoint->id,
                'name' => (string) $switchPoint->name,
                'enabled' => ((int) $switchPoint->enabled == 1 ? true : false),
                'command' => (int) $switchPoint->command,
                'year' => explode(',', (string) $switchPoint->year),
                'month' => explode(',', (string) $switchPoint->month),
                'week' => explode(',', (string) $switchPoint->week),
                'day' => explode(',', (string) $switchPoint->day),
                'hour' => explode(',', (string) $switchPoint->hour),
                'minute' => explode(',', (string) $switchPoint->minute),
                'lastExecute' => (string) $switchPoint->lastExecute,
                'conditions' => explode(',', (string) $switchPoint->conditions)
            );

            if($redis->hSetNx('switchpoints', (int) $switchPoint->id, $switchPointData) == 0) {

                //Import Fehler
                $cli->writeLineColored('Import des Schaltpunktes "'. (string) $room->name .'" fehlgeschlagen', 'red');
            }
        }

        //Autoincrement Setzen
        $redis->incrBy('autoIncrement:switchpoints', (int) $switchpointsXml->nextAutoIncrementId);
    }

    //Schaltserver importieren
    if(file_exists('./shc/data/storage/switchserver.xml')) {

        $switchServerXml = new SimpleXMLElement('./shc/data/storage/switchserver.xml', null, true);

        //Daten einlesen
        foreach ($switchServerXml->switchserver as $switchserver) {

            $switchServerData = array(
                'id' => (int) $switchserver->id,
                'name' => (string) $switchserver->name,
                'enabled' => ((int) $switchserver->enabled == 1 ? true : false),
                'address' => (string) $switchserver->address,
                'port' => (int) $switchserver->port,
                'model' => (int) $switchserver->model,
                'radioSockets' => ((int) $switchserver->radioSockets == 1 ? true : false),
                'writeGpios' => ((int) $switchserver->writeGpios == 1 ? true : false),
                'readGpios' => ((int) $switchserver->readGpios == 1 ? true : false),
                'timeout' => (int) $switchserver->timeout,
            );

            if($redis->hSetNx('switchServers', (int) $switchserver->id, $switchServerData) == 0) {

                //Import Fehler
                $cli->writeLineColored('Import des Schaltservers "'. (string) $room->name .'" fehlgeschlagen', 'red');
            }
        }

        //Autoincrement Setzen
        $redis->incrBy('autoIncrement:switchServers', (int) $switchServerXml->nextAutoIncrementId);
    }

    //Benutzer zu Hause importieren
    if(file_exists('./shc/data/storage/usersathome.xml')) {

        $userAtHomeXml = new SimpleXMLElement('./shc/data/storage/usersathome.xml', null, true);

        //Daten einlesen
        foreach ($userAtHomeXml->user as $usersAtHome) {

            $switchServerData = array(
                'id' => (int) $usersAtHome->id,
                'name' => (string) $usersAtHome->name,
                'enabled' => ((int) $usersAtHome->enabled == 1 ? true : false),
                'ipAddress' => (string) $usersAtHome->ipAddress,
                'orderId' => (int) $usersAtHome->orderId,
                'visibility' => ((int) $usersAtHome->visibility == 1 ? true : false),
                'state' => (int) $usersAtHome->state
            );

            if($redis->hSetNx('usersrathome', (int) $usersAtHome->id, $switchServerData) == 0) {

                //Import Fehler
                $cli->writeLineColored('Import des Benutzers "'. (string) $room->name .'" fehlgeschlagen', 'red');
            }
        }

        //Autoincrement Setzen
        $redis->incrBy('autoIncrement:usersrathome', (int) $switchServerXml->nextAutoIncrementId);
    }

    //alte XML Dateien loeschen
    @unlink('./shc/data/storage/conditions.xml');
    @unlink('./shc/data/storage/events.xml');
    @unlink('./shc/data/storage/rooms.xml');
    @unlink('./shc/data/storage/switchables.xml');
    @unlink('./shc/data/storage/switchpoints.xml');
    @unlink('./shc/data/storage/switchserver.xml');
    @unlink('./shc/data/storage/usersathome.xml');

    //App Json anpassen
    $content = file_get_contents('./shc/app.json');
    $content = str_replace('"installed": false', '"installed": true', $content);
    file_put_contents('./shc/app.json', $content);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update v2.2.0 auf v2.2.1 ////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(file_exists('./shc/app.json') || file_exists('./pcc/app.json')) {

    //neue RWF Einstellungen
    $settingsXml = new SimpleXMLElement('./rwf/data/storage/settings.xml', null, true);

    //Datenbank
    addSetting('rwf.fritzBox.address', 'fritz.box', TYPE_STRING);
    addSetting('rwf.fritzBox.has5GHzWlan', 'false', TYPE_BOOLEAN);
    addSetting('rwf.fritzBox.user', '', TYPE_STRING);
    addSetting('rwf.fritzBox.password', '', TYPE_STRING);

    //XML Speichern
    $settingsXml->asXML('./rwf/data/storage/settings.xml');
}

if(file_exists('./pcc/app.json')) {

    //neue PCC Einstellungen
    $settingsXml = new SimpleXMLElement('./rwf/data/storage/settings.xml', null, true);

    //Datenbank
    addSetting('pcc.fritzBox.showState', 'true', TYPE_BOOLEAN);
    addSetting('pcc.fritzBox.showSmartHomeDevices', 'true', TYPE_BOOLEAN);
    addSetting('pcc.fritzBox.showCallList', 'true', TYPE_BOOLEAN);
    addSetting('pcc.fritzBox.callListMax', '25', TYPE_INTEGER);
    addSetting('pcc.fritzBox.callListDays', '999', TYPE_INTEGER);

    //XML Speichern
    $settingsXml->asXML('./rwf/data/storage/settings.xml');

    //neue Benutzerechte erstellen
    $usersXml = new SimpleXMLElement('./rwf/data/storage/users.xml', null, true);

    //Gruppenrechte vorbereiten
    foreach ($usersXml->groups->group as $group) {

        //Rechte
        addPremission($group, 'pcc.ucp.fbState', '1');
        addPremission($group, 'pcc.ucp.fbSmartHomeDevices', '1');
        addPremission($group, 'pcc.ucp.fbCallList', '1');
    }

    //XML Speichern
    $usersXml->asXML('./rwf/data/storage/users.xml');
}

print("Update erfolgreich\n");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// sich selbst loeschen ////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

@unlink(__FILE__);