<?php

/**
 * Update von Version 2.2.1 auf 2.2.1
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
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
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

$cli = new CliUtil();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update vorbereiten //////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//Initialisieren
$shcInstalled = false;
$shcApiLevel = 10;
$pccInstalled = false;
$pccApiLevel = 10;

//SHC
if(file_exists('./shc/app.json')) {

    $shcInstalled = true;
    $shcApp = json_decode(file_get_contents('./shc/app.json'), true);
    if(isset($shcApp['apLevel'])) {

        $shcApiLevel = (int) $shcApp['apLevel'];
    }
}

//PCC
if(file_exists('./pcc/app.json')) {

    $pccInstalled = true;
    $pccApp = json_decode(file_get_contents('./pcc/app.json'), true);
    if(isset($pccApp['apLevel'])) {

        $pccApiLevel = (int) $pccApp['apLevel'];
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update v2.2.1 auf v2.2.2  (API Level 10 auf 11) /////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//SHC
if($shcInstalled === true && $shcApiLevel == 10) {

    //Update Funktionen

    //Neue Einstellungen
    $settingsXml = new SimpleXMLElement('./rwf/data/storage/settings.xml', null, true);

    addSetting('shc.shedulerDaemon.performanceProfile', '2', TYPE_INTEGER);

    //XML Speichern
    $settingsXml->asXML('./rwf/data/storage/settings.xml');

    //apiLevel hochzaehlen
    $shcApiLevel++;
}

//PCC
if($pccInstalled === true && $pccApiLevel == 10) {

    //Update Funktionen

    //Neue Einstellungen
    $settingsXml = new SimpleXMLElement('./rwf/data/storage/settings.xml', null, true);

    addSetting('pcc.fritzBox.dslConnected', 'true', TYPE_BOOLEAN);

    //XML Speichern
    $settingsXml->asXML('./rwf/data/storage/settings.xml');

    //Fix Berechtigungen
    $usersXml = new SimpleXMLElement('./rwf/data/storage/users.xml', null, true);

    //Gruppenrechte vorbereiten
    foreach($usersXml->groups->group as $group) {
        
        $group = $group->premissions;
        addPremission($group, 'pcc.ucp.viewSysState', '1');
        addPremission($group, 'pcc.ucp.viewSysData', '1');
        addPremission($group, 'pcc.acp.menu', '0');
        addPremission($group, 'pcc.acp.userManagement', '0');
        addPremission($group, 'pcc.acp.settings', '0');
    }

    //XML Speichern
    $usersXml->asXML('./rwf/data/storage/users.xml');

    //apiLevel hochzaehlen
    $pccApiLevel++;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// neue app.json Dateien schreiben /////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//SHC
if($shcInstalled === true) {

    $content = '
        {
            "app": "shc",
            "name": "Raspberry Pi SmartHome Control",
            "icon": "./shc/inc/img/shc-icon.png",
            "order": 10,
            "installed": true,
            "apLevel": '. $shcApiLevel .'
        }';
    file_put_contents('./shc/app.json', $content);
}

//PCC
if($pccInstalled === true) {

    $content = '
        {
            "app": "pcc",
            "name": "Raspberry Pi Control Center",
            "icon": "./pcc/inc/img/pcc-icon.png",
            "order": 20,
            "installed": true,
            "apLevel": '. $pccApiLevel .'
        }';
    file_put_contents('./pcc/app.json', $content);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// neue app.json Dateien schreiben /////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$cli->writeLineColored('Update erfolgreich', 'green');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// sich selbst loeschen ////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

@unlink(__FILE__);