<?php

/**
 * Raeumeverwaltung Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Allgemein
$l['acp.conditionManagement.title'] = 'Bedingungen verwalten';

//Bedingungen liste
$l['acp.conditionManagement.conditionList.table.head.name'] = 'Name';
$l['acp.conditionManagement.conditionList.table.head.type'] = 'Typ';

//Buttons
$l['acp.conditionManagement.button.addCondition'] = 'neue Bedingung';
$l['acp.conditionManagement.button.next'] = 'weiter';

//Prompts
$l['acp.conditionManagement.prompt.deleteCondition.title'] = 'Bedingung löschen';
$l['acp.conditionManagement.prompt.deleteCondition'] = 'Willst du die Bedingung wirklich löschen?';

//Bedingungen
$l['acp.conditionManagement.condition.HumidityGreaterThanCondition'] = 'Luftfeuchte größer als';
$l['acp.conditionManagement.condition.HumidityLowerThanCondition'] = 'Luftfeuchte kleiner als';
$l['acp.conditionManagement.condition.LightIntensityGreaterThanCondition'] = 'Lichtstärke größer als';
$l['acp.conditionManagement.condition.LightIntensityLowerThanCondition'] = 'Lichtstärke kleiner als';
$l['acp.conditionManagement.condition.MoistureGreaterThanCondition'] = 'Feuchtigkeit größer als';
$l['acp.conditionManagement.condition.MoistureLowerThanCondition'] = 'Feuchtigkeit kleiner als';
$l['acp.conditionManagement.condition.TemperatureGreaterThanCondition'] = 'Temperatur größer als';
$l['acp.conditionManagement.condition.TemperatureLowerThanCondition'] = 'Temperatur kleiner als';
$l['acp.conditionManagement.condition.NobodyAtHomeCondition'] = 'Niemand zu Hause';
$l['acp.conditionManagement.condition.UserAtHomeCondition'] = 'Benutzer zu Hause';
$l['acp.conditionManagement.condition.DateCondition'] = 'Datumsbereich';
$l['acp.conditionManagement.condition.DayOfWeekCondition'] = 'Wochentage';
$l['acp.conditionManagement.condition.TimeOfDayCondition'] = 'Zeitbereich';
$l['acp.conditionManagement.condition.SunriseSunsetCondition'] = 'Tag';
$l['acp.conditionManagement.condition.SunsetSunriseCondition'] = 'Nacht';

//Formulare
$l['acp.conditionManagement.form.condition.name'] = 'Name';
$l['acp.conditionManagement.form.condition.name.description'] = 'Name der Bedingung';
$l['acp.conditionManagement.form.condition.sensors'] = 'Sensoren';
$l['acp.conditionManagement.form.condition.sensors.description'] = 'wähle die Sensoren die mit der Bedingung abgefragt werden';
$l['acp.conditionManagement.form.condition.active'] = 'Aktiviert';
$l['acp.conditionManagement.form.condition.active.description'] = 'Aktiviert/Deaktiviert die Bedingung';
$l['acp.conditionManagement.form.condition.humidity'] = 'Luftfeuchte';
$l['acp.conditionManagement.form.condition.humidity.description'] = 'wähle die Luftfeuchte (in Prozent) über/unter der die Bedingung zutreffen soll';
$l['acp.conditionManagement.form.condition.lightIntensity'] = 'Lichtstärke';
$l['acp.conditionManagement.form.condition.lightIntensity.description'] = 'wähle die Lichtstärke (0 - 1023 Digits) über/unter der die Bedingung zutreffen soll';
$l['acp.conditionManagement.form.condition.moisture'] = 'Feuchtigkeit';
$l['acp.conditionManagement.form.condition.moisture.description'] = 'wähle die Feuchtigkeit (0 - 1023 Digits) über/unter der die Bedingung zutreffen soll';
$l['acp.conditionManagement.form.condition.temperature'] = 'Temperatur';
$l['acp.conditionManagement.form.condition.temperature.description'] = 'wähle die Temperatur (-30 - 120°C) über/unter der die Bedingung zutreffen soll';
$l['acp.conditionManagement.form.condition.userAtHome'] = 'Benutzer';
$l['acp.conditionManagement.form.condition.userAtHome.description'] = 'wähle die Benutzer von denen mindestens einer anwesend sein muss damit die Bedingung zutrifft';
$l['acp.conditionManagement.form.condition.startDate'] = 'Start Datum';
$l['acp.conditionManagement.form.condition.startDate.description'] = 'Anfangsdatum des Datumsbereiches';
$l['acp.conditionManagement.form.condition.endDate'] = 'End Datum';
$l['acp.conditionManagement.form.condition.endDate.description'] = 'Enddatum des Datumsbereiches';
$l['acp.conditionManagement.form.condition.startDay'] = 'Start Tag';
$l['acp.conditionManagement.form.condition.startDay.description'] = 'Anfangstag des Datumsbereiches';
$l['acp.conditionManagement.form.condition.endDay'] = 'End Tag';
$l['acp.conditionManagement.form.condition.endDay.description'] = 'Endtag des Datumsbereiches';;
$l['acp.conditionManagement.form.condition.startHour'] = 'Start Stunde';
$l['acp.conditionManagement.form.condition.startHour.description'] = 'Startstunde des Zeitbereiches';
$l['acp.conditionManagement.form.condition.startMinute'] = 'Start Minute';
$l['acp.conditionManagement.form.condition.startMinute.description'] = 'Startminute des Zeitbereiches';
$l['acp.conditionManagement.form.condition.endHour'] = 'End Stunde';
$l['acp.conditionManagement.form.condition.endHour.description'] = 'Endstunde des Zeitbereiches';;
$l['acp.conditionManagement.form.condition.endMinute'] = 'End Minute';
$l['acp.conditionManagement.form.condition.endMinute.description'] = 'Endminute des Zeitbereiches';

//Meldungen
$l['acp.conditionManagement.form.condition.success'] = 'Die Bedingung wurde erfolgreich gespeichert';

$l['acp.conditionManagement.form.condition.formError'] = 'Falscher Formulartyp';
$l['acp.conditionManagement.form.condition.error.1502'] = 'Der Name ist schon vergeben';
$l['acp.conditionManagement.form.condition.error.1102'] = 'Die Bedingung konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.conditionManagement.form.condition.error.invalidDate'] = 'Ungültige Daten angegeben';
$l['acp.conditionManagement.form.condition.error'] = 'Die Bedingung konnte nicht gespeichert werden';
$l['acp.conditionManagement.form.condition.error.id'] = 'Ungültige ID';
$l['acp.conditionManagement.form.delete.success'] = 'Die Bedingung wurde erfolgreich gelöscht';
$l['acp.conditionManagement.form.delete.error.1102'] = 'Die Bedingung konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.conditionManagement.form.delete.error'] = 'Die Bedingung konnte nicht gelöscht werden';