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

//Allgemein
//General

//$l['acp.sensorpointsManagement.title'] = 'Sensorpunkte verwalten';
$l['acp.sensorpointsManagement.title'] = 'Manage sensor points';
//$l['acp.sensorpointsManagement.title.sensors'] = 'Sensoren';
$l['acp.sensorpointsManagement.title.sensors'] = 'Sensors';

//Raumuebersicht
//Room overview

//$l['acp.sensorpointsManagement.roomList.table.head.name'] = 'Name';
$l['acp.sensorpointsManagement.roomList.table.head.name'] = 'Name';
//$l['acp.sensorpointsManagement.roomList.table.head.lastConnect'] = 'letzter Kontakt';
$l['acp.sensorpointsManagement.roomList.table.head.lastConnect'] = 'Last contact';
//$l['acp.sensorpointsManagement.roomList.table.head.voltage'] = 'Spannung';
$l['acp.sensorpointsManagement.roomList.table.head.voltage'] = 'Voltage';

//Prompts

//$l['acp.sensorpointsManagement.prompt.deleteSensorPoint.title'] = 'Sensorpunkt löschen';
$l['acp.sensorpointsManagement.prompt.deleteSensorPoint.title'] = 'Delete sensor point';
//$l['acp.sensorpointsManagement.prompt.deleteSensorPoint'] = 'Willst du den Sensorpunkt wirklich löschen? Dabei werden auch alle zum Sensorpunkt gehörenden Sensoren gelöscht!';
$l['acp.sensorpointsManagement.prompt.deleteSensorPoint'] = 'Want to delete the sensor point really? Here also all the sensor point belonging sensors are deleted!';

//Formulare
//Forms

//$l['acp.sensorpointsManagement.form.sensorPoint.name'] = 'Name';
$l['acp.sensorpointsManagement.form.sensorPoint.name'] = 'Name';
//$l['acp.sensorpointsManagement.form.sensorPoint.name.description'] = 'Name des Sensorpunktes';
$l['acp.sensorpointsManagement.form.sensorPoint.name.description'] = 'Name of the sensor point';
//$l['acp.sensorpointsManagement.form.sensorPoint.warnLevel'] = 'Warnungsgrenze';
$l['acp.sensorpointsManagement.form.sensorPoint.warnLevel'] = 'Warning limit';
//$l['acp.sensorpointsManagement.form.sensorPoint.warnLevel.description'] = 'Setzt die Grenzspannung in Volt unter der eine Meldung Angezeigt wird (mit 0 ist der Überwachung deaktiviert)';
$l['acp.sensorpointsManagement.form.sensorPoint.warnLevel.description'] = 'Sets the limiting voltage in Volt under which a message is displayed (with 0 is the monitoring deactivated)';

//Meldungen
//Messages

//$l['acp.sensorpointsManagement.form.success'] =  'Der Sensorpunkt wurde erfolgreich gespeichert';
$l['acp.sensorpointsManagement.form.success'] =  'The sensor point has been saved successfully';
//$l['acp.sensorpointsManagement.form.success.del'] =  'Der Sensorpunkt wurde erfolgreich gelöscht';
$l['acp.sensorpointsManagement.form.success.del'] =  'The sensor point has been successfully deleted';
//['acp.sensorpointsManagement.form.success.del.info'] =  'Der Sensorpunkt wird automatisch neu erstellt wenn neue Daten vom Sensorpunkt empfangen werden';
$l['acp.sensorpointsManagement.form.success.del.info'] =  'The sensor point will automatically be recreated when new data is received from the sensor point';
//$l['acp.sensorpointsManagement.form.error.1507'] =  'Der Name des Sensorpunktes ist schon vergeben';
$l['acp.sensorpointsManagement.form.error.1507'] =  'The name of the sensor point is already taken';
//$l['acp.sensorpointsManagement.form.error.1102'] =  'Der Sensorpunkt konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.sensorpointsManagement.form.error.1102'] =  'The sensor point could not be saved due to lack of write access';
//$l['acp.sensorpointsManagement.form.error'] =  'Der Sensorpunkt konnte nicht gespeichert werden';
$l['acp.sensorpointsManagement.form.error'] =  'The sensor point could not be saved';
//$l['acp.sensorpointsManagement.form.error.1102.del'] =  'Der Sensorpunkt konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.sensorpointsManagement.form.error.1102.del'] =  'The sensor point could not be deleted due to lack of write access';
//$l['acp.sensorpointsManagement.form.error.del'] =  'Der Sensorpunkt konnte nicht gelöscht werden';
$l['acp.sensorpointsManagement.form.error.del'] =  'The sensor point could not be deleted';
//$l['acp.sensorpointsManagement.form.error.id'] =  'Ungültige ID';
$l['acp.sensorpointsManagement.form.error.id'] =  'Invalid ID';
