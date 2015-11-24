<?php

/**
 * Sprachvariablen fuer den Sensor Reciver
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Eingaben
$l['sensorReciver.input.active'] = 'Sensor Empfänger aktiviert ({1:s}): ';
$l['sensorReciver.input.active.invalid'] = 'Ungültige Eingabe';
$l['sensorReciver.input.active.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['sensorReciver.input.ip'] = 'IP Adresse ({1:s}): ';
$l['sensorReciver.input.ip.invalid'] = 'ungültige IP Adresse!';
$l['sensorReciver.input.ip.invalid.repeated'] = 'du hast zu oft eine ungültige Adressen eingegeben';
$l['sensorReciver.input.port'] = 'Port ({1:s}): ';
$l['sensorReciver.input.port.invalid'] = 'ungültiger Port';
$l['sensorReciver.input.port.invalid.repeated'] = 'du hast zu oft einen ungültigen Ports eingegeben';

//Schaltserver Meldungen
$l['sensorReciver.inactive'] = 'Der Arduino Reciver wurde deaktiviert';
$l['sensorReciver.start'] = 'Der Server wurde erfolgreich unter "{1:s}:{2:s}" gestartet';
$l['sensorReciver.stoppedSuccessfully'] = 'Der Sensor Reciver wurde erfolgreich gestoppt';

$l['sensorReciver.input.save.success'] = 'Die Einstellungen wurden erfolgreich gespeichert und werden nach dem nächsten neustart des Servers aktiv';
$l['sensorReciver.input.save.error'] = 'Die Einstellungen konnten nicht gespeichert werden';
