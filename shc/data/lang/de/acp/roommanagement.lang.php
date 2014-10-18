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
$l['acp.roomManagement.title'] = 'Raumverwaltung';

//Raumuebersicht
$l['acp.roomManagement.roomList.table.head.name'] = 'Name';

//Buttons
$l['acp.roomManagement.button.addRoom'] = 'neuer Raum';

//Prompts
$l['acp.roomManagement.prompt.deleteRoom.title'] = 'Raum löschen';
$l['acp.roomManagement.prompt.deleteRoom'] = 'Willst du den Raum wirklich löschen?';

//Formulare
$l['acp.roomManagement.form.room.name'] = 'Name';
$l['acp.roomManagement.form.room.name.description'] = 'Name des Raumes';
$l['acp.roomManagement.form.room.active'] = 'Aktiviert';
$l['acp.roomManagement.form.room.active.description'] = 'Aktiviert/Deaktiviert den Raum (der Raum wird wenn Deaktiviert nicht in der Benutzeroberfläche angezeigt)';
$l['acp.roomManagement.form.room.allowedUsers'] = 'erlaubte Benutzer';
$l['acp.roomManagement.form.room.allowedUsers.description'] = 'Die gewählten Benutzer dürfen den Raum verwenden';

//Meldungen
$l['acp.roomManagement.form.success.addRoom'] =  'Der Raum wurde erfolgreich erstellt';
$l['acp.roomManagement.form.success.editRoom'] =  'Der Raum wurde erfolgreich bearbeitet';
$l['acp.roomManagement.form.success.deleteRoom'] =  'Der Raum wurde erfolgreich gelöscht';
$l['acp.roomManagement.form.error.1500'] =  'Der Name des Raumes ist schon vergeben';
$l['acp.roomManagement.form.error.1102'] =  'Der Raum konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.roomManagement.form.error.1102.del'] =  'Der Raum konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.roomManagement.form.error'] =  'Der Raum konnte nicht gespeichert werden';
$l['acp.roomManagement.form.error.del'] =  'Der Raum konnte nicht gelöscht werden';
$l['acp.roomManagement.form.error.id'] =  'Ungültige Raum ID';