<?php

/**
 * Sensorpunkte Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Allgemein
$l['acp.sensorpointsManagement.title'] = 'Sensorpunkte verwalten';
$l['acp.sensorpointsManagement.title.sensors'] = 'Sensoren';

//Raumuebersicht
$l['acp.sensorpointsManagement.roomList.table.head.name'] = 'Name';
$l['acp.sensorpointsManagement.roomList.table.head.lastConnect'] = 'letzter Kontakt';
$l['acp.sensorpointsManagement.roomList.table.head.voltage'] = 'Spannung';

//Prompts
$l['acp.sensorpointsManagement.prompt.deleteSensorPoint.title'] = 'Snesorpunkt löschen';
$l['acp.sensorpointsManagement.prompt.deleteSensorPoint'] = 'Willst du den Sensorpunkt wirklich löschen? Dabei werden auch alle zum Sensorpunkt gehörenden Sensoren gelöscht!';

//Formulare
$l['acp.sensorpointsManagement.form.sensorPoint.name'] = 'Name';
$l['acp.sensorpointsManagement.form.sensorPoint.name.description'] = 'Name des Sensorpunktes';
$l['acp.sensorpointsManagement.form.sensorPoint.warnLevel'] = 'Warnungsgrenze';
$l['acp.sensorpointsManagement.form.sensorPoint.warnLevel.description'] = 'Setzt die Grenzspannung in Volt unter der eine Meldung Angezeigt wird (mit 0 ist der Überwachung deaktiviert)';

//Meldungen
$l['acp.sensorpointsManagement.form.success'] =  'Der Sensorpunkt wurde erfolgreich gespeichert';
$l['acp.sensorpointsManagement.form.success.del'] =  'Der Sensorpunkt wurde erfolgreich gelöscht';
$l['acp.sensorpointsManagement.form.success.del.info'] =  'Der Sensorpunkt wird automatisch neu erstellt wenn neue Daten vom Sensorpunkt empfangen werden';
$l['acp.sensorpointsManagement.form.error.1507'] =  'Der Name des Sensorpunktes ist schon vergeben';
$l['acp.sensorpointsManagement.form.error.1102'] =  'Der Sensorpunkt konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.sensorpointsManagement.form.error'] =  'Der Sensorpunkt konnte nicht gespeichert werden';
$l['acp.sensorpointsManagement.form.error.1102.del'] =  'Der Sensorpunkt konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.sensorpointsManagement.form.error.del'] =  'Der Sensorpunkt konnte nicht gelöscht werden';
$l['acp.sensorpointsManagement.form.error.id'] =  'Ungültige ID';