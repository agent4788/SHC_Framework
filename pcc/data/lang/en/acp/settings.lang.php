<?php

/**
 * Einstellungen Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Allgemein
$l['acp.settings.title'] = 'Einstellungen';

//Meldungen
$l['acp.settings.form.success'] = 'Die Einstellungen wurden erfolgreich gespeichert';
$l['acp.settings.form.error.1102'] = 'Die Einstellungen konnten wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.settings.form.error'] = 'Die Einstellungen konnten nicht gespeichert werden';

//Formulare
$l['acp.settings.tabs.global'] = 'Allgemein';
$l['acp.settings.tabs.global.description'] = 'Grundeinstellungen des SHC';
$l['acp.settings.tabs.dateTime'] = 'Datum und Zeit';
$l['acp.settings.tabs.dateTime.description'] = 'Zeinstellungen zu Datum und Zeit';
$l['acp.settings.tabs.ui'] = 'Benutzeroberfläche';
$l['acp.settings.tabs.ui.description'] = 'Einstellungen zur Benutzeroberfläche';
$l['acp.settings.tabs.fritzBox'] = 'Fritz!Box';
$l['acp.settings.tabs.fritzBox.description'] = 'Zugangsdaten zur Fritz Box';

$l['acp.settings.tabs.redirect.pc'] = 'Weboberfläche';
$l['acp.settings.tabs.redirect.tablet'] = 'Tabletoberfläche';
$l['acp.settings.tabs.redirect.smartphone'] = 'Smartphoneoberfläche';

$l['acp.settings.form.title'] = 'Titel';
$l['acp.settings.form.title.decription'] = 'Titel der in der Kopfzeile angezeit wird';
$l['acp.settings.form.allowLongTimeLogin'] = 'Langzeit Login';
$l['acp.settings.form.allowLongTimeLogin.decription'] = 'Benutzer können sich dauerhaft einlogge';
$l['acp.settings.form.defaultStyle'] = 'Webstyle';
$l['acp.settings.form.defaultStyle.decription'] = 'der hier eingestellte Webstyle gilt für alle Gäste und Benutzer die nicht selbst einen Style gewählt haben';
$l['acp.settings.form.defaultLanguage'] = 'Sprache';
$l['acp.settings.form.defaultLanguage.decription'] = 'die hier eingestellte Sprache gilt für alle Gäste und Benutzer die nicht selbst ein Sprache gewählt haben';
$l['acp.settings.form.Timezone'] = 'Zeitzone';
$l['acp.settings.form.Timezone.decription'] = 'Zeitzone';
$l['acp.settings.form.defaultDateFormat'] = 'Datumsformat';
$l['acp.settings.form.defaultDateFormat.decription'] = 'Standard Datumsformat, die Bedutung der Formatierung ist in der PHP Funktionsbeschreibung der "date" Funktion zu finden';
$l['acp.settings.form.defaultTimeFormat'] = 'Zeitformat';
$l['acp.settings.form.defaultTimeFormat.decription'] = 'Standard Zeitformat, die Bedutung der Formatierung ist in der PHP Funktionsbeschreibung der "date" Funktion zu finden';
$l['acp.settings.form.sunriseOffset'] = 'Offset Sonnenaufgang';
$l['acp.settings.form.sunriseOffset.decription'] = 'mit dieser Einstellung kann der errechnete Sonnenaufgang verschoben werden (heutiger Sonnenaufgang mit Offset: {1:s} Uhr)';
$l['acp.settings.form.sunsetOffset'] = 'Offset Sonnenuntergang';
$l['acp.settings.form.sunsetOffset.decription'] = 'mit dieser Einstellung kann der errechnete Sonnenuntergang verschoben werden (heutiger Sonnenuntergang mit Offset: {1:s} Uhr)';
$l['acp.settings.form.Latitude'] = 'Breitengrad';
$l['acp.settings.form.Latitude.decription'] = 'Geodaten für die Berechnung von Sonnenauf- und untergang';
$l['acp.settings.form.Longitude'] = 'Längengrad';
$l['acp.settings.form.Longitude.decription'] = 'Geodaten für die Berechnung von Sonnenauf- und untergang';
$l['acp.settings.form.fbAddress'] = 'Adresse';
$l['acp.settings.form.fbAddress.decription'] = 'Host oder IP Adresse der Fritz!Box';
$l['acp.settings.form.5GHzWlan'] = '5GHz Wlan';
$l['acp.settings.form.5GHzWlan.decription'] = 'Hat die Fritz!Box ein 5GHz Wlan?';
$l['acp.settings.form.fbUser'] = 'Benutzer';
$l['acp.settings.form.fbUser.decription'] = 'Benutzername des Fritz!Box Benutzers';
$l['acp.settings.form.fbPassword'] = 'Passwort';
$l['acp.settings.form.fbPassword.decription'] = 'Passwort des Fritz!Box Benutzers';
$l['acp.settings.form.fbShowState'] = 'Status anzeigen';
$l['acp.settings.form.fbShowState.decription'] = 'Soll die Fritz!Box Statusübersicht angezeigt werden';
$l['acp.settings.form.fbDsl'] = 'DSL';
$l['acp.settings.form.fbDsl.decription'] = 'Ist die Fritz!Box per DSL mit dem Internet verbunden?';
$l['acp.settings.form.fbSmartHomeDevices'] = 'SmartHome Geräte';
$l['acp.settings.form.fbSmartHomeDevices.decription'] = 'Soll die Fritz!Box SmartHome Geräteüberschit angezeigt werden';
$l['acp.settings.form.fbCallList'] = 'Anrufliste';
$l['acp.settings.form.fbCallList.decription'] = 'Soll die Fritz!Box Anrufliste angezeigt werden';
$l['acp.settings.form.fbCallListMax'] = 'Einträge';
$l['acp.settings.form.fbCallListMax.decription'] = 'Wievile Eintraäe der Anrufliste sollen Maximal angezeigt werden';
$l['acp.settings.form.fbCallListDays'] = 'Tage';
$l['acp.settings.form.fbCallListDays.decription'] = 'gibt an wie alt die Einträge maxmal sein dürfen (1 = heute, 7 = die letzte Woche)';
$l['acp.settings.form.redirectActive'] = 'Umleitung Aktiv';
$l['acp.settings.form.redirectActive.decription'] = 'ist die Umleitung anktiv werden PCs, Tablets und Smartphones auf die EIngestellten Oberflächen umgeleitet';
$l['acp.settings.form.redirectPcTo'] = 'PC Umleitung';
$l['acp.settings.form.redirectPcTo.decription'] = 'legt die Oberfläche fest auf die ein PC umgeleitet wird';
$l['acp.settings.form.redirectTabletTo'] = 'Tablet Umleitung';
$l['acp.settings.form.redirectTabletTo.decription'] = 'legt die Oberfläche fest auf die ein Tablet umgeleitet wird';
$l['acp.settings.form.redirectSmartphoneTo'] = 'Smartphone Umleitung';
$l['acp.settings.form.redirectSmartphoneTo.decription'] = 'legt die Oberfläche fest auf die ein Smartphone umgeleitet wird';