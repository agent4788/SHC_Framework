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
// Einstellungen ///////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!file_exists('./rwf/data/storage/settings.xml')) {

    $settingsXml = new SimpleXMLElement('./rwf/data/storage/default/defaultSettings.xml', null, true);
} else {

    $settingsXml = new SimpleXMLElement('./rwf/data/storage/settings.xml', null, true);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Datenbank Einstellungen /////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

    $port = $cli->input('Redis Port (6379)');

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

//Datenbank
addSetting('shc.redis.host', $valid_address, TYPE_STRING);
addSetting('shc.redis.port', $valid_port, TYPE_INTEGER);
addSetting('shc.redis.timeout', $valid_timeout, TYPE_INTEGER);
addSetting('shc.redis.db', $valid_db, TYPE_INTEGER);
addSetting('shc.redis.pass', $valid_password, TYPE_STRING);

//Allgemeine Einstellungen
addSetting('shc.ui.redirectActive', 'true', TYPE_BOOLEAN);
addSetting('shc.ui.redirectPcTo', '1', TYPE_INTEGER);
addSetting('shc.ui.redirectTabletTo', '1', TYPE_INTEGER);
addSetting('shc.ui.redirectSmartphoneTo', '1', TYPE_INTEGER);
addSetting('shc.ui.index.showUsersAtHome', 'true', TYPE_BOOLEAN);
addSetting('shc.title', 'SHC%202.2', TYPE_STRING);
addSetting('shc.defaultStyle', 'redmond', TYPE_STRING);
addSetting('shc.defaultMobileStyle', 'default', TYPE_STRING);

//Sheduler Daemon
addSetting('shc.shedulerDaemon.active', 'true', TYPE_BOOLEAN);

//Schaltserver
addSetting('shc.switchServer.active', 'true', TYPE_BOOLEAN);
addSetting('shc.switchServer.ip', '127.0.0.1', TYPE_STRING);
addSetting('shc.switchServer.port', '9274', TYPE_STRING);
addSetting('shc.switchServer.senderActive', 'true', TYPE_BOOLEAN);
addSetting('shc.switchServer.sendCommand', '/usr/local/bin/pilight-send', TYPE_STRING);
addSetting('shc.switchServer.gpioCommand', '/usr/local/bin/gpio', TYPE_STRING);
addSetting('shc.switchServer.rcswitchPiCommand', '/opt/rcswitch-pi/send', TYPE_STRING);
addSetting('shc.switchServer.sendLedPin', '-1', TYPE_INTEGER);
addSetting('shc.switchServer.writeGpio', 'true', TYPE_BOOLEAN);
addSetting('shc.switchServer.readGpio', 'true', TYPE_BOOLEAN);

//Sensor Transmitter
addSetting('shc.sensorTransmitter.active', 'false', TYPE_BOOLEAN);
addSetting('shc.sensorTransmitter.ip', '127.0.0.1', TYPE_STRING);
addSetting('shc.sensorTransmitter.port', '80', TYPE_STRING);
addSetting('shc.sensorTransmitter.pointId', '-1', TYPE_INTEGER);

//XML Speichern
$settingsXml->asXML('./rwf/data/storage/settings.xml');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Berechtigungen //////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$newXml = false;
if(!file_exists('./rwf/data/storage/users.xml')) {

    $usersXml = new SimpleXMLElement('./rwf/data/storage/default/defaultUsers.xml', null, true);
    $newXml = true;
} else {

    $usersXml = new SimpleXMLElement('./rwf/data/storage/users.xml', null, true);
}

//Gruppenrechte vorbereiten
foreach($usersXml->groups->group as $group) {

    $group = $group->premissions;
    addPremission($group, 'shc.ucp.viewUserAtHome', '1');
    addPremission($group, 'shc.acp.menu', '0');
    addPremission($group, 'shc.acp.userManagement', '0');
    addPremission($group, 'shc.acp.settings', '0');
    addPremission($group, 'shc.acp.backupsManagement', '0');
    addPremission($group, 'shc.acp.roomManagement', '0');
    addPremission($group, 'shc.acp.switchableManagement', '0');
    addPremission($group, 'shc.acp.sensorpointsManagement', '0');
    addPremission($group, 'shc.acp.usersathomeManagement', '0');
    addPremission($group, 'shc.acp.conditionsManagement', '0');
    addPremission($group, 'shc.acp.switchpointsManagement', '0');
    addPremission($group, 'shc.acp.eventsManagement', '0');
    addPremission($group, 'shc.acp.switchserverManagement', '0');
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Benutzer ////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($newXml === true) {

    //Passwort Libary einbinden fuer PHP Versionen < PHP 5.5
    require_once('./rwf/lib/external/password/password.php');

    //Hauptbenutzer vorbereiten
    $user = $usersXml->users->user[0];
    $user->name = 'admin';
    $user->password = password_hash('admin', PASSWORD_DEFAULT);
    $user->authCode = randomStr(64);
    $user->register = (new DateTime('now'))->format('Y-m-d');
}

//XML Speichern
$usersXml->asXML('./rwf/data/storage/users.xml');


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// APP als Installiert markieren ///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$content = file_get_contents('./shc/app.json');
$content = str_replace('"installed": false', '"installed": true', $content);
file_put_contents('./shc/app.json', $content);

print("SHC erfolgreich installiert\n");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// sich selbst loeschen ////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

@unlink(__FILE__);