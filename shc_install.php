<?php

//SHC Einstellungen
$settingsXml = new SimpleXMLElement('./rwf/data/storage/default/defaultSettings.xml', null, true);

//Einstellung hinzufuegen
define('TYPE_BOOLEAN', 'bool');
define('TYPE_STRING', 'string');
define('TYPE_INTEGER', 'int');
function addSetting($name, $value, $type) {

    global $settingsXml;

    $setting = $settingsXml->addChild('setting');
    $setting->addAttribute('name', $name);
    $setting->addAttribute('value', $value);
    $setting->addAttribute('type', $type);
}

//Allgemeine Einstellungen
addSetting('shc.ui.redirectActive', 'true', TYPE_BOOLEAN);
addSetting('shc.ui.redirectPcTo', '1', TYPE_INTEGER);
addSetting('shc.ui.redirectTabletTo', '1', TYPE_INTEGER);
addSetting('shc.ui.redirectSmartphoneTo', '1', TYPE_INTEGER);
addSetting('shc.ui.index.showUsersAtHome', 'true', TYPE_BOOLEAN);

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

//Arduino Reciver
addSetting('shc.arduinoReciver.active', 'false', TYPE_BOOLEAN);
addSetting('shc.arduinoReciver.interface', '/dev/ttyAMA0', TYPE_STRING);
addSetting('shc.arduinoReciver.baudRate', '9600', TYPE_INTEGER);
addSetting('shc.arduinoReciver.ip', '127.0.0.1', TYPE_STRING);
addSetting('shc.arduinoReciver.port', '9275', TYPE_STRING);

//Sensor Reciver
addSetting('shc.sensorReciver.active', 'false', TYPE_BOOLEAN);
addSetting('shc.sensorReciver.ip', '127.0.0.1', TYPE_STRING);
addSetting('shc.sensorReciver.port', '9275', TYPE_STRING);

//Sensor Transmitter
addSetting('shc.sensorReciver.active', 'false', TYPE_BOOLEAN);
addSetting('shc.sensorReciver.ip', '127.0.0.1', TYPE_STRING);
addSetting('shc.sensorReciver.port', '9275', TYPE_STRING);
addSetting('shc.sensorReciver.pointId', '-1', TYPE_INTEGER);

//Allgemeine SHC Einstellungen
addSetting('shc.title', 'SHC%202.0', TYPE_STRING);
addSetting('shc.defaultStyle', 'redmond', TYPE_STRING);

//XML Speichern
$settingsXml->asXML('./rwf/data/storage/settings.xml');

//Benutzerverwaltung Konfigurieren
$usersXml = new SimpleXMLElement('./rwf/data/storage/default/defaultUsers.xml', null, true);

//Funktionen
function addPremission($xml, $name, $value) {

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

//Gruppenrechte vorbereiten
foreach($usersXml->groups->group as $group) {

    $group = $group->premissions;
    addPremission($group, 'shc.ucp.viewUserAtHome', '0');
    addPremission($group, 'shc.ucp.reboot', '0');
    addPremission($group, 'shc.ucp.shutdown', '0');
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

//Passwort Libary einbinden fuer PHP Versionen < PHP 5.5
require_once('./rwf/lib/external/password/password.php');

//Hauptbenutzer vorbereiten
$user = $usersXml->users->user[0];
$user->name = 'admin';
$user->password = password_hash('admin', PASSWORD_DEFAULT);
$user->authCode = randomStr(64);
$user->register = (new DateTime('now'))->format('Y-m-d');

//XML Speichern
$usersXml->asXML('./rwf/data/storage/users.xml');