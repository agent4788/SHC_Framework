<?php

/**
 * Schaltserver Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Allgemein
$l['acp.switchserverManagement.title'] = 'Schaltserver verwalten';

//Serveruebersicht
$l['acp.switchserverManagement.serverList.table.head.name'] = 'Name';
$l['acp.switchserverManagement.serverList.table.head.ip'] = 'IP Adresse';
$l['acp.switchserverManagement.serverList.table.head.radioSockets'] = 'Funksteckdosen';
$l['acp.switchserverManagement.serverList.table.head.readGPIOs'] = 'GPIOs lesen';
$l['acp.switchserverManagement.serverList.table.head.writeGPIOs'] = 'GPIOs schreiben';

//Buttons
$l['acp.switchserverManagement.button.addSwitchServer'] = 'neuer Schaltserver';

//Prompts
$l['acp.switchserverManagement.prompt.deletSwitchServer.title'] = 'Schaltserver löschen';
$l['acp.switchserverManagement.prompt.deletSwitchServer'] = 'Willst du den Schaltserver wirklich löschen?';

//Formular
$l['acp.switchserverManagement.form.switchServer.name'] = 'Name';
$l['acp.switchserverManagement.form.switchServer.name.description'] = 'Name des Schaltservers';
$l['acp.switchserverManagement.form.switchServer.ip'] = 'IP Adresse';
$l['acp.switchserverManagement.form.switchServer.ip.description'] = 'IP Adresse des Schaltservers';
$l['acp.switchserverManagement.form.switchServer.port'] = 'Port';
$l['acp.switchserverManagement.form.switchServer.port.description'] = 'Port auf dem der Schaltserver auf Anfragen wartet';
$l['acp.switchserverManagement.form.switchServer.timeout'] = 'Timeout';
$l['acp.switchserverManagement.form.switchServer.timeout.description'] = 'Wartezeit (in Sekunden) bis die Verbindung abgebrochen wird wenn keine Antwort vom Server erfolgt';
$l['acp.switchserverManagement.form.switchServer.model'] = 'Model';
$l['acp.switchserverManagement.form.switchServer.model.description'] = 'Raspberry Pi Model';
$l['acp.switchserverManagement.form.switchServer.radioSockets'] = 'Funksteckdose';
$l['acp.switchserverManagement.form.switchServer.radioSockets.description'] = 'der Schaltserver kann Funksteckdosen schalten';
$l['acp.switchserverManagement.form.switchServer.readGPIO'] = 'GPIOs lesen';
$l['acp.switchserverManagement.form.switchServer.readGPIO.description'] = 'die GPIOs des Schaltservers können als Eingang abgefragt werden';
$l['acp.switchserverManagement.form.switchServer.writeGPIO'] = 'GPIOs schreiben';
$l['acp.switchserverManagement.form.switchServer.writeGPIO.description'] = 'die GPIOs des Schaltservers können als Ausgang verwendet werden';
$l['acp.switchserverManagement.form.switchServer.active'] = 'Aktiviert';
$l['acp.switchserverManagement.form.switchServer.active.description'] = 'Aktiviert/Deaktiviert den Schaltserver';

//Meldungen
$l['acp.switchserverManagement.form.success.addSwitchServer'] =  'Der Schaltserver wurde erfolgreich erstellt';
$l['acp.switchserverManagement.form.success.editSwitchServer'] =  'Der Schaltserver wurde erfolgreich bearbeitet';
$l['acp.switchserverManagement.form.success.deleteSwitchServer'] =  'Der Schaltserver wurde erfolgreich gelöscht';
$l['acp.switchserverManagement.form.error.1501'] =  'Der Name des Schaltservers ist schon vergeben';
$l['acp.switchserverManagement.form.error.1102'] =  'Der Schaltserver konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchserverManagement.form.error.1102.del'] =  'Der Schaltserver konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.switchserverManagement.form.error'] =  'Der Schaltserver konnte nicht gespeichert werden';
$l['acp.switchserverManagement.form.error.del'] =  'Der Schaltserver konnte nicht gelöscht werden';
$l['acp.switchserverManagement.form.error.id'] =  'Ungültige ID';
