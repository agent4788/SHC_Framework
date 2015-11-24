<?php

/**
 * Sprachvariablen fuer den Sensor Transmitter
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Eingaben
$l['sensorTransmitter.input.active'] = 'Sensor Transmitter Dienst aktiviert ({1:s}): ';
$l['sensorTransmitter.input.active.invalid'] = 'Ungültige Eingabe';
$l['sensorTransmitter.input.active.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';
$l['sensorTransmitter.input.ip'] = 'IP Adresse des Sensor Empfängers ({1:s}): ';
$l['sensorTransmitter.input.ip.invalid'] = 'ungültige IP Adresse!';
$l['sensorTransmitter.input.ip.invalid.repeated'] = 'du hast zu oft eine ungültige Adressen eingegeben';
$l['sensorTransmitter.input.port'] = 'Port des Sensor Empfängers ({1:s}): ';
$l['sensorTransmitter.input.port.invalid'] = 'ungültiger Port';
$l['sensorTransmitter.input.port.invalid.repeated'] = 'du hast zu oft einen ungültigen Ports eingegeben';
$l['sensorTransmitter.input.sensorPointId'] = 'Sensor Punkt ID ({1:s}): ';
$l['sensorTransmitter.input.sensorPointId.invalid'] = 'ungültige ID';
$l['sensorTransmitter.input.sensorPointId.invalid.repeated'] = 'du hast zu oft eine ungültige ID eingegeben';
$l['sensorTransmitter.input.sensorPointId.info'] = 'Die Sensor Punkt ID muss im gesamten Netzwerk eindeutig sein, über diese ID können die Sensoren einem Standort besser zugeordnet werden';

$l['sensorTransmitter.input.blinkPin'] = 'Status LED Pin [-1 wenn deaktiviert] ({1:s}): ';
$l['sensorTransmitter.input.blinkPin.invalid'] = 'Ungültige Eingabe';
$l['sensorTransmitter.input.blinkPin.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';

//Meldungen
$l['sensorTransmitter.input.save.success'] = 'Die Einstellungen wurden erfolgreich gespeichert und werden nach dem nächsten neustart des Servers aktiv';
$l['sensorTransmitter.input.save.error'] = 'Die Einstellungen konnten nicht gespeichert werden';
