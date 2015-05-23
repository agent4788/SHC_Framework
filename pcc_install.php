<?php

/**
 * Install PCC
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Einstellungen ///////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!file_exists('./rwf/data/storage/settings.xml')) {

    $settingsXml = new SimpleXMLElement('./rwf/data/storage/default/defaultSettings.xml', null, true);
} else {

    $settingsXml = new SimpleXMLElement('./rwf/data/storage/settings.xml', null, true);
}

//Allgemeine Einstellungen
addSetting('pcc.ui.redirectActive', 'true', TYPE_BOOLEAN);
addSetting('pcc.ui.redirectPcTo', '1', TYPE_INTEGER);
addSetting('pcc.ui.redirectTabletTo', '1', TYPE_INTEGER);
addSetting('pcc.ui.redirectSmartphoneTo', '1', TYPE_INTEGER);
addSetting('pcc.title', 'PCC%202.2', TYPE_STRING);
addSetting('pcc.defaultStyle', 'redmond', TYPE_STRING);
addSetting('pcc.defaultMobileStyle', 'default', TYPE_STRING);

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
    addPremission($group, 'pcc.ucp.viewSysState', '1');
    addPremission($group, 'pcc.ucp.viewSysData', '1');
    addPremission($group, 'pcc.acp.menu', '0');
    addPremission($group, 'pcc.acp.userManagement', '0');
    addPremission($group, 'pcc.acp.settings', '0');
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// APP als Installiert markieren ///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$content =
'{
    "app": "pcc",
    "name": "Raspberry Pi Control Center",
    "icon": "./pcc/inc/img/pcc-icon.png",
    "order": 20,
    "installed": true
}';
file_put_contents('./pcc/app.json', $content);

print("PCC erfolgreich installiert\n");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// sich selbst loeschen ////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

@unlink(__FILE__);