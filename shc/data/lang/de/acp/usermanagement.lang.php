<?php

/**
 * Benutzerverwaltung Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Allgemein
$l['acp.userManagement.title'] = 'Benutzerverwaltung';

//Benutzer Tabelle
$l['acp.userManagement.userList.table.head.user'] = 'Benutzer';
$l['acp.userManagement.userList.table.head.registrationDate'] = 'Registrierung';
$l['acp.userManagement.userList.table.head.options'] = 'Optionen';

//Buttons
$l['acp.userManagement.button.addUser'] = 'neuer Benutzer';
$l['acp.userManagement.button.viewGroupList'] = 'Benutzergruppen';

//Prompts
$l['acp.userManagement.prompt.deleteUser.title'] = 'Benutzer löschen';
$l['acp.userManagement.prompt.deleteUser'] = 'Willst du den Benutzer wirklich löschen?';

//Forulate
$l['acp.userManagement.form.user.addDescription'] = 'Benutzer erstellen';
$l['acp.userManagement.form.user.editDescription'] = 'Benutzer bearbeiten';
$l['acp.userManagement.form.user.name'] = 'Benutzername';
$l['acp.userManagement.form.user.name.description'] = 'Name des Benutzers';
$l['acp.userManagement.form.user.pass1'] = 'Passwort';
$l['acp.userManagement.form.user.pass1.description'] = 'Passwort des Benutzers';
$l['acp.userManagement.form.user.pass2'] = 'Passwort wiederholung';
$l['acp.userManagement.form.user.pass2.description'] = 'gib das Passwort nochmals ein um schreibfehler zu vermeiden';
$l['acp.userManagement.form.user.mainGroup'] = 'Hauptgruppe';
$l['acp.userManagement.form.user.mainGroup.description'] = 'Hauptgruppe des Benutzers';
$l['acp.userManagement.form.user.userGroups'] = 'Benutzergruppen';
$l['acp.userManagement.form.user.userGroups.description'] = 'Benutzergruppen zu denen der Benutzer zugeordnet wird';
$l['acp.userManagement.form.user.lang'] = 'Sprache';
$l['acp.userManagement.form.user.lang.description'] = 'Sprache in der das SHC dem Benutzer angezeigt wird';

//Meldungen
$l['acp.userManagement.form.error.invalidName'] = 'Der Benutzername darf nur folgende Zeichen enthalten: a-z 0-9 # _ ! - . , ; + * ?';
$l['acp.userManagement.form.error.nameNotAvailable'] =  'Der Benutzername ist bereits vergeben';
$l['acp.userManagement.form.error.passwordError'] =  'Die Passwörter stimmen nicht überein';

$l['acp.userManagement.form.success.addUser'] =  'Der Benutzer wurde erfolgreich erstellt';
$l['acp.userManagement.form.success.editUser'] =  'Der Benutzer wurde erfolgreich bearbeitet';
$l['acp.userManagement.form.success.deleteUser'] =  'Der Benutzer wurde erfolgreich gelöscht';
$l['acp.userManagement.form.error.1110'] =  $l['acp.userManagement.form.error.nameNotAvailable'];
$l['acp.userManagement.form.error.1102'] =  'Der Benutzer konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.userManagement.form.error.1102.del'] =  'Der Benutzer konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.userManagement.form.error.id'] =  'Ungültige Benutzer ID';
$l['acp.userManagement.form.error'] =  'Der Benutzer konnte nicht gespeichert werden';
$l['acp.userManagement.form.error.del'] =  'Der Benutzer konnte nicht gelöscht werden';
