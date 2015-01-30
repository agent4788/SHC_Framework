<?php

/**
 * Startseite Sprachvariablen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

$l['index.button.acpMenu'] = 'Administration';
$l['index.button.ucp'] = 'Benutzer Bereich';
$l['index.button.mobile.acpMenu'] = 'Admin-CP';
$l['index.button.mobile.ucp'] = 'Benutzer-CP';
$l['index.button.login'] = 'anmelden';
$l['index.button.logout'] = 'abmelden';
$l['index.button.back'] = 'zurück';
$l['index.login.user'] = 'angemeldet als {1:s}';
$l['index.specialFunctions'] = 'Sonderfunktionen';

//Benutzer zu Hause
$l['index.userAtHome.boxTitle'] = 'Benutzer zu Hause';
$l['index.userAtHome.online'] = 'online';
$l['index.userAtHome.offline'] = 'offline';

//Login
$l['index.login.boxTitle'] = 'Anmelden';
$l['index.login.name'] = 'Benutzername';
$l['index.login.password'] = 'Passwort';
$l['index.login.longTime'] = 'merken?';
$l['index.login.submit'] = 'anmelden';
$l['index.login.requestError'] = 'Anfrage Fehlgeschlagen';
$l['index.login.error'] = 'Benutzername oder Passwort falsch';

//Warnungen
$l['index.warnings'] = 'folgende Fehler sind aufgetreten:';
$l['index.warnings.noRunningServer'] = 'Kein laufender Schaltserver gefunden. Für die Funktion des SHC muss mindestens ein Schaltserver erreichbar sein!';
$l['index.warnings.switchserver.stop'] = 'Der Schaltserver "{1:s}" ist nicht erreichbar';
$l['index.warnings.sheduler.stop'] = 'Der Sheduler läuft nicht';
$l['index.warnings.arduinoSensorReciver.stop'] = 'Der Arduino Sensor Receiver läuft nicht';
$l['index.warnings.sensorDataReciver.stop'] = 'Der Sensor Receiver läuft nicht';
$l['index.warnings.sensorDataTransmitter.stop'] = 'Der Sensor Transmitter läuft nicht';
$l['index.warnings.sensorPoint.stop'] = 'Der Sensorpunkt "{1:s}" hat seit mehr als 2 Stunden keine Daten mehr übermittelt';