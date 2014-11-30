<?php

/**
 * Schaltbare Elemente Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Allgemein
$l['acp.switchableManagement.title'] = 'Schaltbare Elemente verwalten';

//Elemente Tabelle
$l['acp.switchableManagement.elementTable.table.head.name'] = 'Name';
$l['acp.switchableManagement.elementTable.table.head.command'] = 'Befehl';
$l['acp.switchableManagement.elementTable.table.head.type'] = 'Typ';

//Buttons
$l['acp.switchableManagement.button.listBoxes'] = 'Boxen verwalten';
$l['acp.switchableManagement.button.listElementsWithoutRoom'] = 'Raumlose Elemente';
$l['acp.switchableManagement.button.toggleCommand'] = 'Befehl umkehren';
$l['acp.switchableManagement.button.deleteFormContainer'] = 'entfernen';
$l['acp.switchableManagement.button.addElement'] = 'Element erstellen';
$l['acp.switchableManagement.button.addBox'] = 'Box erstellen';
$l['acp.switchableManagement.button.next'] = 'weiter';
$l['acp.switchableManagement.button.addToBox'] = 'Element hinzufügen';
$l['acp.switchableManagement.button.addToSwitchableContainer'] = 'hinzufügen';

//Typen
$l['acp.switchableManagement.element.activity'] = 'Aktivität';
$l['acp.switchableManagement.element.arduinoOutput'] = 'Arduino Ausgang';
$l['acp.switchableManagement.element.countdown'] = 'Countdown';
$l['acp.switchableManagement.element.radiosocket'] = 'Funksteckdose';
$l['acp.switchableManagement.element.rpiGpioOutput'] = 'RPi GPIO Ausgang';
$l['acp.switchableManagement.element.wakeOnLan'] = 'Wake On Lan';
$l['acp.switchableManagement.element.arduinoInput'] = 'Arduino Eingang';
$l['acp.switchableManagement.element.rpiGpioInput'] = 'RPi GPIO Eingang';
$l['acp.switchableManagement.element.BMP'] = 'BMP 085/150';
$l['acp.switchableManagement.element.DHT'] = 'DHT 11/22';
$l['acp.switchableManagement.element.DS18x20'] = 'DS18x20';
$l['acp.switchableManagement.element.Hygrometer'] = 'Feuchtigkeits Sensor';
$l['acp.switchableManagement.element.LDR'] = 'Lichtstärke Sensor';
$l['acp.switchableManagement.element.RainSensor'] = 'Regen Sensor';
$l['acp.switchableManagement.element.box'] = 'Box';

//Prompts
$l['acp.switchableManagement.prompt.deleteSwitchable.title'] = 'Schaltbares Element löschen';
$l['acp.switchableManagement.prompt.deleteSwitchable'] = 'Willst du das schaltbare Element wirklich löschen?';
$l['acp.switchableManagement.prompt.deleteReadable.title'] = 'Lesbares Element löschen';
$l['acp.switchableManagement.prompt.deleteReadable'] = 'Willst du das lesbare Element wirklich löschen?';
$l['acp.switchableManagement.prompt.deleteSensor.title'] = 'Sensor löschen';
$l['acp.switchableManagement.prompt.deleteSensor'] = 'Willst du den Sensor wirklich löschen?';
$l['acp.switchableManagement.prompt.deleteBox.title'] = 'Box löschen';
$l['acp.switchableManagement.prompt.deleteBox'] = 'Willst du die Box wirklich löschen?';

//Formularelemente
$l['acp.switchableManagement.form.addActivity.name'] = 'Name';
$l['acp.switchableManagement.form.addActivity.name.description'] = 'Name der Aktivität';
$l['acp.switchableManagement.form.addActivity.icon'] = 'Icon';
$l['acp.switchableManagement.form.addActivity.icon.description'] = 'Icon der Aktivität';
$l['acp.switchableManagement.form.addActivity.room'] = 'Raum';
$l['acp.switchableManagement.form.addActivity.room.description'] = 'Raum dem die Aktivität zugeordnet ist';
$l['acp.switchableManagement.form.addActivity.switchPoints'] = 'Schaltpunkte';
$l['acp.switchableManagement.form.addActivity.switchPoints.description'] = 'Schaltpunkte der Aktivität';
$l['acp.switchableManagement.form.addActivity.active'] = 'Aktiv';
$l['acp.switchableManagement.form.addActivity.active.description'] = 'Aktiviert/Deaktiviert die Aktivität';
$l['acp.switchableManagement.form.addActivity.visibility'] = 'Sichtbarkeit';
$l['acp.switchableManagement.form.addActivity.visibility.description'] = 'Sichtbarkeit der Aktivität';
$l['acp.switchableManagement.form.addActivity.allowedUsers'] = 'erlaubte Benutzer';
$l['acp.switchableManagement.form.addActivity.allowedUsers.description'] = 'legt fest welche Benutzer die Aktivität verwenden dürfen';

$l['acp.switchableManagement.form.addCountdown.name'] = 'Name';
$l['acp.switchableManagement.form.addCountdown.name.description'] = 'Name des Countdowns';
$l['acp.switchableManagement.form.addCountdown.icon'] = 'Icon';
$l['acp.switchableManagement.form.addCountdown.icon.description'] = 'Icon des Countdowns';
$l['acp.switchableManagement.form.addCountdown.room'] = 'Raum';
$l['acp.switchableManagement.form.addCountdown.room.description'] = 'Raum dem der Countdown zugeordnet ist';
$l['acp.switchableManagement.form.addCountdown.interval'] = 'Intervall';
$l['acp.switchableManagement.form.addCountdown.interval.description'] = 'Wartezeit in Sekunden bis zum automatischen umschalten der Befehle';
$l['acp.switchableManagement.form.addCountdown.switchPoints'] = 'Schaltpunkte';
$l['acp.switchableManagement.form.addCountdown.switchPoints.description'] = 'Schaltpunkte des Countdowns';
$l['acp.switchableManagement.form.addCountdown.active'] = 'Aktiv';
$l['acp.switchableManagement.form.addCountdown.active.description'] = 'Aktiviert/Deaktiviert den Countdown';
$l['acp.switchableManagement.form.addCountdown.visibility'] = 'Sichtbarkeit';
$l['acp.switchableManagement.form.addCountdown.visibility.description'] = 'Sichtbarkeit des Countdowns';
$l['acp.switchableManagement.form.addCountdown.allowedUsers'] = 'erlaubte Benutzer';
$l['acp.switchableManagement.form.addCountdown.allowedUsers.description'] = 'legt fest welche Benutzer den Countdown verwenden dürfen';

$l['acp.switchableManagement.form.addRadioSocket.name'] = 'Name';
$l['acp.switchableManagement.form.addRadioSocket.name.description'] = 'Name der Funksteckdose';
$l['acp.switchableManagement.form.addRadioSocket.icon'] = 'Icon';
$l['acp.switchableManagement.form.addRadioSocket.icon.description'] = 'Icon der Funsteckdose';
$l['acp.switchableManagement.form.addRadioSocket.room'] = 'Raum';
$l['acp.switchableManagement.form.addRadioSocket.room.description'] = 'Raum in dem sich die Funksteckdose befindet';
$l['acp.switchableManagement.form.addRadioSocket.protocol'] = 'Protokoll';
$l['acp.switchableManagement.form.addRadioSocket.protocol.description'] = 'Protokoll der Funksteckdose';
$l['acp.switchableManagement.form.addRadioSocket.systemCode'] = 'System Code';
$l['acp.switchableManagement.form.addRadioSocket.systemCode.description'] = 'System Code der Funksteckdose';
$l['acp.switchableManagement.form.addRadioSocket.deviceCode'] = 'Geräte Code';
$l['acp.switchableManagement.form.addRadioSocket.deviceCode.description'] = 'Geräte Code der Funksteckdose';
$l['acp.switchableManagement.form.addRadioSocket.continuous'] = 'Sendevorgänge';
$l['acp.switchableManagement.form.addRadioSocket.continuous.description'] = 'Hier kannst du einstellen wie oft der Schaltbefehl gesendet werden soll (dadurch lassen sich manche steckdosen zuverlässiger steuern)';
$l['acp.switchableManagement.form.addRadioSocket.switchPoints'] = 'Schaltpunkte';
$l['acp.switchableManagement.form.addRadioSocket.switchPoints.description'] = 'Schaltpunkte der Funksteckdose';
$l['acp.switchableManagement.form.addRadioSocket.active'] = 'Aktiv';
$l['acp.switchableManagement.form.addRadioSocket.active.description'] = 'Aktiviert/Deaktiviert die Funksteckdose';
$l['acp.switchableManagement.form.addRadioSocket.visibility'] = 'Sichtbarkeit';
$l['acp.switchableManagement.form.addRadioSocket.visibility.description'] = 'Sichtbarkeit der Funksteckdose';
$l['acp.switchableManagement.form.addRadioSocket.allowedUsers'] = 'erlaubte Benutzer';
$l['acp.switchableManagement.form.addRadioSocket.allowedUsers.description'] = 'legt fest welche Benutzer die Funksteckdose verwenden dürfen';

$l['acp.switchableManagement.form.addGpioOutput.name'] = 'Name';
$l['acp.switchableManagement.form.addGpioOutput.name.description'] = 'Name des GPIO`s';
$l['acp.switchableManagement.form.addGpioOutput.icon'] = 'Icon';
$l['acp.switchableManagement.form.addGpioOutput.icon.description'] = 'Icon des GPIO`s';
$l['acp.switchableManagement.form.addGpioOutput.room'] = 'Raum';
$l['acp.switchableManagement.form.addGpioOutput.room.description'] = 'Raum in dem sich der GPIO befindet';
$l['acp.switchableManagement.form.addGpioOutput.switchServer'] = 'Schaltserver';
$l['acp.switchableManagement.form.addGpioOutput.switchServer.description'] = 'Schaltserver zu dem der GPIO gehört';
$l['acp.switchableManagement.form.addGpioOutput.gpioPin'] = 'GPIO Pin';
$l['acp.switchableManagement.form.addGpioOutput.gpioPin.description'] = 'wiringPi Pin Nummer des GPIO`s';
$l['acp.switchableManagement.form.addGpioOutput.switchPoints'] = 'Schaltpunkte';
$l['acp.switchableManagement.form.addGpioOutput.switchPoints.description'] = 'Schaltpunkte des GPIO`s';
$l['acp.switchableManagement.form.addGpioOutput.active'] = 'Aktiv';
$l['acp.switchableManagement.form.addGpioOutput.active.description'] = 'Aktiviert/Deaktiviert den GPIO';
$l['acp.switchableManagement.form.addGpioOutput.visibility'] = 'Sichtbarkeit';
$l['acp.switchableManagement.form.addGpioOutput.visibility.description'] = 'Sichtbarkeit des GPIO';
$l['acp.switchableManagement.form.addGpioOutput.allowedUsers'] = 'erlaubte Benutzer';
$l['acp.switchableManagement.form.addGpioOutput.allowedUsers.description'] = 'legt fest welche Benutzer den GPIO verwenden dürfen';

$l['acp.switchableManagement.form.addWol.name'] = 'Name';
$l['acp.switchableManagement.form.addWol.name.description'] = 'Name des WOL Gerätes';
$l['acp.switchableManagement.form.addWol.room'] = 'Raum';
$l['acp.switchableManagement.form.addWol.room.description'] = 'Raum in dem sich das WOL Gerät befindet';
$l['acp.switchableManagement.form.addWol.mac'] = 'MAC Adresse';
$l['acp.switchableManagement.form.addWol.mac.description'] = 'MAC Adresse der Wak On Lan fähigem Netzwerkchips';
$l['acp.switchableManagement.form.addWol.ip'] = 'IP Adresse';
$l['acp.switchableManagement.form.addWol.ip.description'] = 'IP Adresse der Wak On Lan fähigem Netzwerkchips';
$l['acp.switchableManagement.form.addWol.switchPoints'] = 'Schaltpunkte';
$l['acp.switchableManagement.form.addWol.switchPoints.description'] = 'Schaltpunkte des WOL Gerätes';
$l['acp.switchableManagement.form.addWol.active'] = 'Aktiv';
$l['acp.switchableManagement.form.addWol.active.description'] = 'Aktiviert/Deaktiviert das WOL Gerät';
$l['acp.switchableManagement.form.addWol.visibility'] = 'Sichtbarkeit';
$l['acp.switchableManagement.form.addWol.visibility.description'] = 'Sichtbarkeit des WOL Gerätes';
$l['acp.switchableManagement.form.addWol.allowedUsers'] = 'erlaubte Benutzer';
$l['acp.switchableManagement.form.addWol.allowedUsers.description'] = 'legt fest welche Benutzer das WOL Gerät einschalten dürfen';

$l['acp.switchableManagement.form.sensorForm.name'] = 'Name';
$l['acp.switchableManagement.form.sensorForm.name.description'] = 'Name des Sensors';
$l['acp.switchableManagement.form.sensorForm.room'] = 'Raum';
$l['acp.switchableManagement.form.sensorForm.room.description'] = 'Raum in dem sich der Sensor befindet';
$l['acp.switchableManagement.form.sensorForm.visibility'] = 'Sichtbarkeit';
$l['acp.switchableManagement.form.sensorForm.visibility.description'] = 'Sichtbarkeit des Sensors';
$l['acp.switchableManagement.form.sensorForm.temperatureVisibility'] = 'Temperatur Anzeigen';
$l['acp.switchableManagement.form.sensorForm.temperatureVisibility.description'] = 'Aktiviert/Deaktiviert die Sichtbarkeit der Temperatur des Sensors';
$l['acp.switchableManagement.form.sensorForm.humidityVisibility'] = 'Luftfeuchte Anzeigen';
$l['acp.switchableManagement.form.sensorForm.humidityVisibility.description'] = 'Aktiviert/Deaktiviert die Sichtbarkeit der Luftfeuchte des Sensors';
$l['acp.switchableManagement.form.sensorForm.pressureVisibility'] = 'Luftdruck Anzeigen';
$l['acp.switchableManagement.form.sensorForm.pressureVisibility.description'] = 'Aktiviert/Deaktiviert die Sichtbarkeit des Luftdruckes des Sensors';
$l['acp.switchableManagement.form.sensorForm.altitudeVisibility'] = 'Standorthöhe Anzeigen';
$l['acp.switchableManagement.form.sensorForm.altitudeVisibility.description'] = 'Aktiviert/Deaktiviert die Sichtbarkeit der Standorthöhe des Sensors';
$l['acp.switchableManagement.form.sensorForm.valueVisibility'] = 'Wert Anzeigen';
$l['acp.switchableManagement.form.sensorForm.valueVisibility.description'] = 'Aktiviert/Deaktiviert die Sichtbarkeit des Wertes des Sensors';
$l['acp.switchableManagement.form.sensorForm.dataRecording'] = 'Datenaufzeichnung';
$l['acp.switchableManagement.form.sensorForm.dataRecording.description'] = 'Sollen die Sensordaten aufgezeichnet werden?';

$l['acp.switchableManagement.form.box.name'] = 'Name';
$l['acp.switchableManagement.form.box.name.description'] = 'Name der Box';
$l['acp.switchableManagement.form.box.room'] = 'Raum';
$l['acp.switchableManagement.form.box.room.description'] = 'Raum in dem sich die Box befindet';
$l['acp.switchableManagement.form.box.elements'] = 'Elemente';
$l['acp.switchableManagement.form.box.elements.description'] = 'Elemente der Box';

//Meldungen
$l['acp.switchableManagement.form.addActivity.success'] =  'Die Aktivität wurde erfolgreich erstellt';
$l['acp.switchableManagement.form.addElementToActivity.success'] =  'Das Element wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.deleteElementFromActivity.success'] =  'Das Element wurde erfolgreich entfernt';
$l['acp.switchableManagement.form.editActivity.success'] =  'Die Aktivität wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.addCountdown.success'] =  'Der Countdown wurde erfolgreich erstellt';
$l['acp.switchableManagement.form.editCountdown.success'] =  'Der Countdown wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.addRadioSocket.success'] =  'Die Funksteckdose wurde erfolgreich erstellt';
$l['acp.switchableManagement.form.editRadioSocket.success'] =  'Die Funksteckdose wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.addGpioOutput.success'] =  'Der GPIO wurde erfolgreich erstellt';
$l['acp.switchableManagement.form.editGpioOutput.success'] =  'Der GPIO wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.addWol.success'] =  'Das Wake On Lan Gerät wurde erfolgreich erstellt';
$l['acp.switchableManagement.form.editWol.success'] =  'Das Wake On Lan Gerät wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.addbox.success'] =  'Die Box wurde erfolgreich erstellt';
$l['acp.switchableManagement.form.editbox.success'] =  'Die Box wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.delete.success'] =  'Das Element wurde erfolgreich gelöscht';
$l['acp.switchableManagement.form.editSensor.success'] =  'Der Sensor wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.editSensor.delete.success'] =  'Der Sensor wurde erfolgreich gelöscht';
$l['acp.switchableManagement.form.deleteSensor.success'] =  'Der Sensor wurde erfolgreich gelöscht';
$l['acp.switchableManagement.form.deleteBox.success'] =  'Die Box wurde erfolgreich gelöscht';

$l['acp.switchableManagement.form.error.id'] =  'Ungültige ID';
$l['acp.switchableManagement.form.error.command'] =  'Ungültiger Befehl';
$l['acp.switchableManagement.form.addActivity.error.1507'] =  'Der Name der Aktivität ist schon vergeben';
$l['acp.switchableManagement.form.addActivity.error.1102'] =  'Die Aktivität konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchableManagement.form.addActivity.error'] =  'Die Aktivität konnte nicht gespeichert werden';
$l['acp.switchableManagement.form.addElementToActivity.error.1102'] =  'Das Element konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchableManagement.form.addElementToActivity.error'] =  'Das Element konnte nicht gespeichert werden';
$l['acp.switchableManagement.form.addCountdown.error.1507'] =  'Der Name des Countdowns ist schon vergeben';
$l['acp.switchableManagement.form.addCountdown.error.1102'] =  'Der Countdown konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchableManagement.form.addCountdown.error'] =  'Der Countdown konnte nicht gespeichert werden';
$l['acp.switchableManagement.form.addRadioSocket.error.1507'] =  'Der Name der Funksteckdose ist schon vergeben';
$l['acp.switchableManagement.form.addRadioSocket.error.1102'] =  'Die Funksteckdose konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchableManagement.form.addRadioSocket.error'] =  'Die Funksteckdose konnte nicht gespeichert werden';
$l['acp.switchableManagement.form.addGpioOutput.error.1507'] =  'Der Name des GPIO`s ist schon vergeben';
$l['acp.switchableManagement.form.addGpioOutput.error.1102'] =  'Der GPIO konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchableManagement.form.addGpioOutput.error'] =  'Der GPIO konnte nicht gespeichert werden';
$l['acp.switchableManagement.form.addWol.error.1507'] =  'Der Name des WOL Gerätes ist schon vergeben';
$l['acp.switchableManagement.form.addWol.error.1102'] =  'Das WOL Gerät konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchableManagement.form.addWol.error'] =  'Das WOL Gerät konnte nicht gespeichert werden';
$l['acp.switchableManagement.form.addbox.error.1507'] =  'Der Name der Box ist schon vergeben';
$l['acp.switchableManagement.form.addbox.error.1102'] =  'Die Box konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchableManagement.form.addbox.error'] =  'Die Box konnte nicht gespeichert werden';
$l['acp.switchableManagement.form.delete.error.1102'] =  'Das Element konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.switchableManagement.form.delete.error'] =  'Das Element konnte nicht gelöscht werden';
$l['acp.switchableManagement.form.editSensor.error.1507'] =  'Der Name des Sensors ist schon vergeben';
$l['acp.switchableManagement.form.editSensor.error.1102'] =  'Der Sensor konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchableManagement.form.editSensor.error'] =  'Der Sensor konnte nicht gespeichert werden';
$l['acp.switchableManagement.form.deleteSensor.error.1102'] =  'Der Sensor konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.switchableManagement.form.deleteSensor.error'] =  'Der Sensor konnte nicht gelöscht werden';
$l['acp.switchableManagement.form.deleteBox.error.1102'] =  'Die Box konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.switchableManagement.form.deleteBox.error'] =  'Die Box konnte nicht gelöscht werden';
$l['acp.switchableManagement.form.deleteElementFromActivity.error.1102'] =  'Das Element konnte wegen fehlender Schreibrechte nicht entfernt werden';
$l['acp.switchableManagement.form.deleteElementFromActivity.error'] =  'Das Element konnte nicht entfernt werden';

$l['acp.switchableManagement.form.success.order'] =  'Die Sortierung wurde erfolgreich gespeichert';
$l['acp.switchableManagement.form.error.order'] =  'Die Sortierung konnte nicht gespeichert werden';