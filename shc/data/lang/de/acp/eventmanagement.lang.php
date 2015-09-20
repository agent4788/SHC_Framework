<?php

/**
 * Ereignisse Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Allgemein
$l['acp.eventsManagement.title'] = 'Ereignisse verwalten';
$l['acp.eventsManagement.title.info'] = 'Info';
$l['acp.eventsManagement.title.conditions'] = 'Bedingungen';
$l['acp.eventsManagement.title.switchables'] = 'schaltbare Elemente';
$l['acp.eventsManagement.title.addCondition'] = 'Bedingung hinzufügen';
$l['acp.eventsManagement.title.addSwitchable'] = 'schaltbares Element hinzufügen';

//Ereignisse uebersicht
$l['acp.eventsManagement.eventList.table.head.name'] = 'Name';
$l['acp.eventsManagement.eventList.table.head.type'] = 'Typ';
$l['acp.eventsManagement.eventList.table.head.lastExecute'] = 'letzte Ausführung';
$l['acp.eventsManagement.eventList.table.head.never'] = 'nie';

//Buttons
$l['acp.eventsManagement.button.next'] = 'weiter';
$l['acp.eventsManagement.button.addEvent'] = 'neues Ereignis';
$l['acp.eventsManagement.button.toggleCommand'] = 'Befehl umkehren';
$l['acp.eventsManagement.button.deleteFormContainer'] = 'entfernen';

//Prompts
$l['acp.eventsManagement.prompt.deleteEvent.title'] = 'Ereignis löschen';
$l['acp.eventsManagement.prompt.deleteEvent'] = 'Willst du das Ereignis wirklich löschen?';

//Ereignisse
$l['acp.eventsManagement.events.HumidityClimbOver'] = 'Luftfeuchte steigt';
$l['acp.eventsManagement.events.HumidityFallsBelow'] = 'Luftfeuchre fällt';
$l['acp.eventsManagement.events.InputHigh'] = 'Eingang positive Flanke';
$l['acp.eventsManagement.events.InputLow'] = 'Eingang negaive Flanke';
$l['acp.eventsManagement.events.LightIntensityClimbOver'] = 'Lichtstärke steigt';
$l['acp.eventsManagement.events.LightIntensityFallBelow'] = 'Lichtstärke fällt';
$l['acp.eventsManagement.events.MoistureClimbOver'] = 'Feuchtigkeit steigt';
$l['acp.eventsManagement.events.MoistureFallsBelow'] = 'Feuchtigkeit fällt';
$l['acp.eventsManagement.events.TemperatureClimbOver'] = 'Temperatur steigt';
$l['acp.eventsManagement.events.TemperatureFallsBelow'] = 'Temperatur fällt';
$l['acp.eventsManagement.events.UserComesHome'] = 'Benutzer kommt nach Hause';
$l['acp.eventsManagement.events.UserLeavesHome'] = 'Benutzer verlässt das Haus';
$l['acp.eventsManagement.events.Sunrise'] = 'Sonnenaufgang';
$l['acp.eventsManagement.events.Sunset'] = 'Sonnenuntergang';
$l['acp.eventsManagement.events.FileCreate'] = 'Datei erstellt';
$l['acp.eventsManagement.events.FileDelete'] = 'Datei gelöscht';

//Formulare
$l['acp.eventsManagement.form.event.name'] = 'Name';
$l['acp.eventsManagement.form.event.name.description'] = 'Name des Ereignisses';
$l['acp.eventsManagement.form.event.condition'] = 'Bedingungen';
$l['acp.eventsManagement.form.event.condition.decription'] = 'Bedingungen die erfüllt sein müssen damit das Ereigniss ausgeführt wird';
$l['acp.eventsManagement.form.event.sensors'] = 'Sensoren';
$l['acp.eventsManagement.form.event.sensors.description'] = 'Sensoren welche vom Ereignis überwacht werden sollen';
$l['acp.eventsManagement.form.event.file'] = 'Datei';
$l['acp.eventsManagement.form.event.file.description'] = 'Datei welche vom Ereignis überwacht werden sollen';
$l['acp.eventsManagement.form.event.limit'] = 'Grenzwert';
$l['acp.eventsManagement.form.event.humidityLimit.description'] = 'steigt oder fällt die Luftfeuchte über/unter diesen Wert wird das Ereignis ausgelöst (Wert in %)';
$l['acp.eventsManagement.form.event.lightIntensityLimit.description'] = 'steigt oder fällt die Lichstärke über/unter diesen Wert wird das Ereignis ausgelöst (Wert in %)';
$l['acp.eventsManagement.form.event.mouistureLimit.description'] = 'steigt oder fällt die Feuchtigkeit über/unter diesen Wert wird das Ereignis ausgelöst (Wert in %)';
$l['acp.eventsManagement.form.event.temperatureLimit.description'] = 'steigt oder fällt die Temperatur über/unter diesen Wert wird das Ereignis ausgelöst  (Wert in °C)';
$l['acp.eventsManagement.form.event.inputs'] = 'Eingänge';
$l['acp.eventsManagement.form.event.inputs.description'] = 'wähle die Eingänge die vom EReignis überwacht werden sollen';
$l['acp.eventsManagement.form.event.users'] = 'Benutzer';
$l['acp.eventsManagement.form.event.users.description'] = 'wähle die Benutzer die durch das Ereignis überwacht werden sollen';
$l['acp.eventsManagement.form.event.interval'] = 'Sperrzeit';
$l['acp.eventsManagement.form.event.interval.description'] = 'Zeit in Sekunden, in dieser Zeit wird das Ereignis nach einem erfolgreichen auslösen nicht erneut ausgelöst';
$l['acp.eventsManagement.form.event.active'] = 'Aktiv';
$l['acp.eventsManagement.form.event.active.description'] = 'aktiviert/deaktiviert das Ereignis';

//Meldungen
$l['acp.eventsManagement.form.success.addEvent'] =  'Das Ereignis wurde erfolgreich erstellt';
$l['acp.eventsManagement.form.success.editEvent'] =  'Das Ereignis wurde erfolgreich erstellt';
$l['acp.eventsManagement.form.event.error.1502'] =  'Der Name des Ereignisses ist schon vergeben';
$l['acp.eventsManagement.form.event.error.1102'] =  'Das Ereignis konnte wegen fehlender schreibrechte nicht gespeichert werden';
$l['acp.eventsManagement.form.event.error'] =  'Das Ereignis konnte nicht gespeichert werden';
$l['acp.eventsManagement.form.error.id'] =  'Ungültige ID';
$l['acp.eventsManagement.form.error.command'] =  'Ungültiger Befehl';
$l['acp.eventsManagement.form.delete.success'] =  'Das Ereignis wurde erfolgreich gelöscht';
$l['acp.eventsManagement.form.delete.error.1102'] =  'Das Ereignis konnte wegen fehlender schreibrechte nicht gelöscht werden';
$l['acp.eventsManagement.form.delete.error'] =  'Das Ereignis konnte nicht gelöscht werden';
$l['acp.eventsManagement.form.addElement.success'] =  'Das Element wurde erfolgreich gespeichert';
$l['acp.eventsManagement.form.addElement.error.1102'] =  'Das Element konnte wegen fehlender schreibrechte nicht gespeichert werden';
$l['acp.eventsManagement.form.addElement.error'] =  'Das Element konnte nicht gespeichert werden';
$l['acp.eventsManagement.form.addCondition.success'] =  'Die Bedingung wurde erfolgreich gespeichert';
$l['acp.eventsManagement.form.addCondition.error.1102'] =  'Die Bedingung konnte wegen fehlender schreibrechte nicht gespeichert werden';
$l['acp.eventsManagement.form.addCondition.error'] =  'Die Bedingung konnte nicht gespeichert werden';
$l['acp.eventsManagement.form.addElement.removeSuccesss'] =  'Das Element wurde erfolgreich gespeichert';
$l['acp.eventsManagement.form.addElement.removeError.1102'] =  'Das Element konnte wegen fehlender schreibrechte nicht gespeichert werden';
$l['acp.eventsManagement.form.addElement.removeError'] =  'Das Element konnte nicht gespeichert werden';
$l['acp.eventsManagement.form.addCondition.removeSuccesss'] =  'Die Bedingung wurde erfolgreich gespeichert';
$l['acp.eventsManagement.form.addCondition.removeError.1102'] =  'Die Bedingung konnte wegen fehlender schreibrechte nicht gespeichert werden';
$l['acp.eventsManagement.form.addCondition.removeError'] =  'Die Bedingung konnte nicht gespeichert werden';
