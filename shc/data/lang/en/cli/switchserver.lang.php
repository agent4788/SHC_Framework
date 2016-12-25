<?php

/**
 * ACP Info Sprachvariablen
 *
 * @author       Oliver Kleditzsch
 * @copyright    Copyright (c) 2014, Oliver Kleditzsch
 * @license      http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since        2.0.0-0
 * @version      2.0.0-0
 * @translation  Dietmar Franken
 * @SHC Forum    http://rpi-controlcenter.de/member.php?action=profile&uid=173
 */
 
$l = array();

//Eingaben
//Input

//$l['switchServer.input.active'] = 'Schaltserver aktiviert ({1:s}): ';
$l['switchServer.input.active'] = 'Switchserver enabled ({1:s}): ';
//$l['switchServer.input.active.invalid'] = 'Ungültige Eingabe';
$l['switchServer.input.active.invalid'] = 'Invalid input';
//$l['switchServer.input.active.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['switchServer.input.active.invalid.repeated'] = 'You have entered too many times an invalid data';
//$l['switchServer.input.ip'] = 'IP Adress ({1:s}): ';
$l['switchServer.input.ip'] = 'IP Adresse ({1:s}): ';
//$l['switchServer.input.ip.invalid'] = 'ungültige IP Adresse!';
$l['switchServer.input.ip.invalid'] = 'invalid IP Adress!';
//$l['switchServer.input.ip.invalid.repeated'] = 'du hast zu oft eine ungültige Adresse eingegeben';
$l['switchServer.input.ip.invalid.repeated'] = 'You have entered too many times an invalid address';
//$l['switchServer.input.port'] = 'Port ({1:s}): ';
$l['switchServer.input.port'] = 'Port ({1:s}): ';
//$l['switchServer.input.port.invalid'] = 'ungültiger Port';
$l['switchServer.input.port.invalid'] = 'invalid Port';
//$l['switchServer.input.port.invalid.repeated'] = 'du hast zu oft einen ungültigen Port eingegeben';
$l['switchServer.input.port.invalid.repeated'] = 'You have entered too many times an invalid port';
//$l['switchServer.input.ledPin'] = 'GPIO Pin für die sende LED ({1:s}): ';
$l['switchServer.input.ledPin'] = 'GPIO pin for transmitting LED ({1:s}): ';
//$l['switchServer.input.ledPin.invalid'] = 'ungültiger GPIO Pin';
$l['switchServer.input.ledPin.invalid'] = 'invalid GPIO Pin';
//$l['switchServer.input.ledPin.invalid.repeated'] = 'du hast zu oft einen ungültigen Port eingegeben';
$l['switchServer.input.ledPin.invalid.repeated'] = 'You have entered too many times an invalid port';
//$l['switchServer.input.ledPin.inactive'] = 'gib -1 ein um die sende LED zu deaktivieren';
$l['switchServer.input.ledPin.inactive'] = 'enter -1 to disable the transmitting LED';
//$l['switchServer.input.senderActive'] = 'Verfügt der RPi über einen 433MHz Sender? ({1:s}): ';
$l['switchServer.input.senderActive'] = 'Feature the RPI a 433MHz transmitter? ({1:s}): ';
//$l['switchServer.input.senderActive.invalid'] = 'Ungültige Eingabe';
$l['switchServer.input.senderActive.invalid'] = 'Invalid input';
//$l['switchServer.input.senderActive.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['switchServer.input.senderActive.invalid.repeated'] = 'You have entered too many times an invalid data';
//$l['switchServer.input.gpioRead'] = 'darf der Raspberry Pi GPIOs als Eingänge einlesen? ({1:s}): ';
$l['switchServer.input.gpioRead'] = 'may the Raspberry Pi GPIOs read as inputs? ({1:s}): ';
//$l['switchServer.input.gpioRead.invalid'] = 'Ungültige Eingabe';
$l['switchServer.input.gpioRead.invalid'] = 'Invalid input';
//$l['switchServer.input.gpioRead.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['switchServer.input.gpioRead.invalid.repeated'] = 'You have entered too many times an invalid data';
//$l['switchServer.input.gpioWrite'] = 'darf der Raspberry Pi GPIOs als Ausgänge schreiben? ({1:s}): ';
$l['switchServer.input.gpioWrite'] = 'may the Raspberry Pi GPIOs write as outputs? ({1:s}): ';
//$l['switchServer.input.gpioWrite.invalid'] = 'Ungültige Eingabe';
$l['switchServer.input.gpioWrite.invalid'] = 'Invalid input';
//$l['switchServer.input.gpioWrite.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['switchServer.input.gpioWrite.invalid.repeated'] = 'You have entered too many times an invalid data';

//Schaltserver Meldungen
//

//$l['switchServer.inactive'] = 'Der Schaltserver wurde deaktiviert';
$l['switchServer.inactive'] = 'The switching server has been disabled';
//$l['switchServer.startedSuccessfully'] = 'Der Server wurde erfolgreich unter "{1:s}:{2:s}" gestartet'
$l['switchServer.startedSuccessfully'] = 'The server has been successfully started under "{1:s}: {2:s}"';
//$l['switchServer.stoppedSuccessfully'] = 'Der Schaltserver wurde erfolgreich gestoppt';
$l['switchServer.stoppedSuccessfully'] = 'The switching server stopped successfully';
//$l['switchServer.input.save.success'] = 'Die Einstellungen wurden erfolgreich gespeichert und werden nach dem nächsten neustart des Servers aktiv';
$l['switchServer.input.save.success'] = 'The settings have been successfully saved and will be active after the next reboot of the server';
//$l['switchServer.input.save.error'] = 'Die Einstellungen konnten nicht gespeichert werden';
$l['switchServer.input.save.error'] = 'The settings could not be saved';
