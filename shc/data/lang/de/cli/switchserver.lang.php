<?php

/**
 * Sprachvariablen fuer den Schaltserver
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Eingaben
$l['switchServer.input.active'] = 'Schaltserver aktiviert ({1:s}): ';
$l['switchServer.input.active.invalid'] = 'Ungültige Eingabe';
$l['switchServer.input.active.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['switchServer.input.ip'] = 'IP Adresse ({1:s}): ';
$l['switchServer.input.ip.invalid'] = 'ungültige IP Adresse!';
$l['switchServer.input.ip.invalid.repeated'] = 'du hast zu oft eine ungültige Adressen eingegeben';
$l['switchServer.input.port'] = 'Port ({1:s}): ';
$l['switchServer.input.port.invalid'] = 'ungültiger Port';
$l['switchServer.input.port.invalid.repeated'] = 'du hast zu oft einen ungültigen Ports eingegeben';
$l['switchServer.input.ledPin'] = 'GPIO Pin für die sende LED ({1:s}): ';
$l['switchServer.input.ledPin.invalid'] = 'ungültiger GPIO Pin';
$l['switchServer.input.ledPin.invalid.repeated'] = 'du hast zu oft einen ungültigen Ports eingegeben';
$l['switchServer.input.ledPin.inactive'] = 'gib -1 ein um die sende LED zu deaktivieren';
$l['switchServer.input.senderActive'] = 'Verfügt der RPi über einen 433MHz Sender? ({1:s}): ';
$l['switchServer.input.senderActive.invalid'] = 'Ungültige Eingabe';
$l['switchServer.input.senderActive.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['switchServer.input.gpioRead'] = 'darf der Raspberry Pi GPIOs als Eingänge einlesen? ({1:s}): ';
$l['switchServer.input.gpioRead.invalid'] = 'Ungültige Eingabe';
$l['switchServer.input.gpioRead.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['switchServer.input.gpioWrite'] = 'darf der Raspberry Pi GPIOs als Ausgänge schreiben? ({1:s}): ';
$l['switchServer.input.gpioWrite.invalid'] = 'Ungültige Eingabe';
$l['switchServer.input.gpioWrite.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';

//Schaltserver Meldungen
$l['switchServer.inactive'] = 'Der Schaltserver wurde deaktiviert';
$l['switchServer.startedSuccessfully'] = 'Der Server wurde erfolgreich unter "{1:s}:{2:s}" gestartet';
$l['switchServer.stoppedSuccessfully'] = 'Der Schaltserver wurde erfolgreich gestoppt';

$l['switchServer.input.save.success'] = 'Die Einstellungen wurden erfolgreich gespeichert und werden nach dem nächsten neustart des Servers aktiv';
$l['switchServer.input.save.error'] = 'Die Einstellungen konnten nicht gespeichert werden';