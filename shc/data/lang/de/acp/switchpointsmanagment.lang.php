<?php

/**
 * Schaltpunkteverwaltung Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Allgemein
$l['acp.switchpointsManagment.title'] = 'Schaltpunkte verwalten';

//Schaltpunkte Tabelle
$l['acp.switchpointsManagment.switchPointList.table.head.name'] = 'Name';
$l['acp.switchpointsManagment.switchPointList.table.head.lastExecute'] = 'letzte Ausführung';
$l['acp.switchpointsManagment.switchPointList.table.head.lastExecute.never'] = 'nie Ausgeführt';

//Tooltip
$l['acp.switchpointsManagment.tooltip.command'] = 'Befehl';
$l['acp.switchpointsManagment.tooltip.condition'] = 'Bedingungen';
$l['acp.switchpointsManagment.tooltip.condition.none'] = 'keine';
$l['acp.switchpointsManagment.tooltip.year'] = 'Jahr';
$l['acp.switchpointsManagment.tooltip.year.every'] = 'jedes Jahr';
$l['acp.switchpointsManagment.tooltip.month'] = 'Monat';
$l['acp.switchpointsManagment.tooltip.month.every'] = 'jeden Monat';
$l['acp.switchpointsManagment.tooltip.day'] = 'Tag';
$l['acp.switchpointsManagment.tooltip.day.every'] = 'jeden Tag';
$l['acp.switchpointsManagment.tooltip.hour'] = 'Stunde';
$l['acp.switchpointsManagment.tooltip.hour.every'] = 'jede Stunde';
$l['acp.switchpointsManagment.tooltip.minute'] = 'Minute';
$l['acp.switchpointsManagment.tooltip.minute.every'] = 'jede Minute';

//Buttons
$l['acp.switchpointsManagment.button.addSwitchPoint'] = 'neuer Schaltpunkt';
$l['acp.switchpointsManagment.button.addSwitchPoint.extendetForm'] = 'erweitert';

//Prompts
$l['acp.switchpointsManagment.prompt.deleteSwitchPoint.title'] = 'Schaltpunkt löschen';
$l['acp.switchpointsManagment.prompt.deleteSwitchPoint'] = 'Willst du den Schaltpunkt wirklich löschen?';

//Formulate
$l['acp.switchpointsManagment.form.switchPoint.name'] = 'Name';
$l['acp.switchpointsManagment.form.switchPoint.name.decription'] = 'Name des Schaltpunkes';
$l['acp.switchpointsManagment.form.switchPoint.command'] = 'Befehl';
$l['acp.switchpointsManagment.form.switchPoint.command.decription'] = 'Befehl der ausgeführt werden soll wenn der Zeitpunkt des Schaltpunktes zurifft';
$l['acp.switchpointsManagment.form.switchPoint.dayOfWeek'] = 'Tage';
$l['acp.switchpointsManagment.form.switchPoint.dayOfWeek.decription'] = 'an denen der Befehl der ausgeführt werden soll';
$l['acp.switchpointsManagment.form.switchPoint.dayOfWeek.val1'] = 'jeden Tag';
$l['acp.switchpointsManagment.form.switchPoint.dayOfWeek.val2'] = 'jeden Wochentag';
$l['acp.switchpointsManagment.form.switchPoint.dayOfWeek.val3'] = 'Wochenende';
$l['acp.switchpointsManagment.form.switchPoint.condition'] = 'Bedingungen';
$l['acp.switchpointsManagment.form.switchPoint.condition.decription'] = 'Bedingungen die zutreffen müssen damit der Schaltpunkt ausgeführt werden soll';
$l['acp.switchpointsManagment.form.switchPoint.year.every'] = 'jedes Jahr';
$l['acp.switchpointsManagment.form.switchPoint.year'] = 'Jahr';
$l['acp.switchpointsManagment.form.switchPoint.year.decription'] = 'Jahr in dem der Schaltpunkt ausgeführt werden soll';
$l['acp.switchpointsManagment.form.switchPoint.month.every'] = 'jeden Monat';
$l['acp.switchpointsManagment.form.switchPoint.month'] = 'Monat';
$l['acp.switchpointsManagment.form.switchPoint.month.decription'] = 'Monat in dem der Schaltpunkt ausgeführt werden soll';
$l['acp.switchpointsManagment.form.switchPoint.day.every'] = 'jeden Tag';
$l['acp.switchpointsManagment.form.switchPoint.day'] = 'Tag';
$l['acp.switchpointsManagment.form.switchPoint.day.decription'] = 'Tag an dem der Schaltpunkt ausgeführt werden soll';
$l['acp.switchpointsManagment.form.switchPoint.hour.every'] = 'jede Stunde';
$l['acp.switchpointsManagment.form.switchPoint.hour'] = 'Stunde';
$l['acp.switchpointsManagment.form.switchPoint.hour.decription'] = 'Stunde in der der Schaltpunkt ausgeführt werden soll';
$l['acp.switchpointsManagment.form.switchPoint.minute.every'] = 'jede Minute';
$l['acp.switchpointsManagment.form.switchPoint.minute'] = 'Minute';
$l['acp.switchpointsManagment.form.switchPoint.minute.decription'] = 'Minute zu der der Schaltpunkt ausgeführt werden soll';
$l['acp.switchpointsManagment.form.switchPoint.active'] = 'Aktiviert';
$l['acp.switchpointsManagment.form.switchPoint.active.decription'] = 'Aktiviert/deaktiviert den Schaltpunkt';

//Meldungen
$l['acp.switchpointsManagment.form.success.switchPoint'] =  'Der Schaltpunkt wurde erfolgreich erstellt';
$l['acp.switchpointsManagment.form.success.delete'] =  'Der Schaltpunkt wurde erfolgreich gelöscht';

$l['acp.switchpointsManagment.form.error.id'] =  'Ungültige ID';

$l['acp.switchpointsManagment.form.switchPoint.error.1503'] =  'Der Name des Schaltpunktes ist schon vergeben';
$l['acp.switchpointsManagment.form.switchPoint.error.1102'] =  'Der Schaltpunkt konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchpointsManagment.form.switchPoint.error'] =  'Der Schaltpunkt konnte nicht gespeichert werden';
$l['acp.switchpointsManagment.form.error.1102.del'] =  'Der Schaltpunkt konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.switchpointsManagment.form.error.del'] =  'Der Schaltpunkt konnte nicht gelöscht werden';