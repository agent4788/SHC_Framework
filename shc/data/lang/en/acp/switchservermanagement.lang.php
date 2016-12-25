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

//$l['acp.switchserverManagement.title'] = 'Schaltserver verwalten';
$l['acp.switchserverManagement.title'] = 'Manage switching server';

//Serveruebersicht
//Server overview

//$l['acp.switchserverManagement.serverList.table.head.name'] = 'Name';
$l['acp.switchserverManagement.serverList.table.head.name'] = 'Name';
//$l['acp.switchserverManagement.serverList.table.head.ip'] = 'IP Adresse';
$l['acp.switchserverManagement.serverList.table.head.ip'] = 'IP Adress';
//$l['acp.switchserverManagement.serverList.table.head.radioSockets'] = 'Funksteckdosen';
$l['acp.switchserverManagement.serverList.table.head.radioSockets'] = 'Radio controlled sockets';
//$l['acp.switchserverManagement.serverList.table.head.readGPIOs'] = 'GPIOs lesen';
$l['acp.switchserverManagement.serverList.table.head.readGPIOs'] = 'Read GPIOs';
//$l['acp.switchserverManagement.serverList.table.head.writeGPIOs'] = 'GPIOs schreiben';
$l['acp.switchserverManagement.serverList.table.head.writeGPIOs'] = 'Write GPIOs';

//Knopfe
//Buttons

//$l['acp.switchserverManagement.button.addSwitchServer'] = 'neuer Schaltserver';
$l['acp.switchserverManagement.button.addSwitchServer'] = 'Add switching server';

//Prompts
//Prompts

//$l['acp.switchserverManagement.prompt.deletSwitchServer.title'] = 'Schaltserver löschen';
$l['acp.switchserverManagement.prompt.deletSwitchServer.title'] = 'Delete switching server';
//$l['acp.switchserverManagement.prompt.deletSwitchServer'] = 'Willst du den Schaltserver wirklich löschen?';
$l['acp.switchserverManagement.prompt.deletSwitchServer'] = 'Are you sure to delete the switching server?';

//Formular
//Form

//$l['acp.switchserverManagement.form.switchServer.name'] = 'Name';
$l['acp.switchserverManagement.form.switchServer.name'] = 'Name';
//$l['acp.switchserverManagement.form.switchServer.name.description'] = 'Name des Schaltservers';
$l['acp.switchserverManagement.form.switchServer.name.description'] = 'Name of switching server';
//$l['acp.switchserverManagement.form.switchServer.ip'] = 'IP Adresse';
$l['acp.switchserverManagement.form.switchServer.ip'] = 'IP Adress';
//$l['acp.switchserverManagement.form.switchServer.ip.description'] = 'IP Adresse des Schaltservers';
$l['acp.switchserverManagement.form.switchServer.ip.description'] = 'Switching server IP address';
//$l['acp.switchserverManagement.form.switchServer.port'] = 'Port';
$l['acp.switchserverManagement.form.switchServer.port'] = 'Port';
//$l['acp.switchserverManagement.form.switchServer.port.description'] = 'Port auf dem der Schaltserver auf Anfragen wartet';
$l['acp.switchserverManagement.form.switchServer.port.description'] = 'Port on the switching server to wait for requests';
//$l['acp.switchserverManagement.form.switchServer.timeout'] = 'Timeout';
$l['acp.switchserverManagement.form.switchServer.timeout'] = 'Timeout';
//$l['acp.switchserverManagement.form.switchServer.timeout.description'] = 'Wartezeit (in Sekunden) bis die Verbindung abgebrochen wird wenn keine Antwort vom Server erfolgt';
$l['acp.switchserverManagement.form.switchServer.timeout.description'] = 'Waiting time (in seconds) until the connection  will be canceled if no response from the server occurs';
//$l['acp.switchserverManagement.form.switchServer.model'] = 'Model';
$l['acp.switchserverManagement.form.switchServer.model'] = 'Model';
//$l['acp.switchserverManagement.form.switchServer.model.description'] = 'Raspberry Pi Model';
$l['acp.switchserverManagement.form.switchServer.model.description'] = 'Raspberry Pi Model';
//$l['acp.switchserverManagement.form.switchServer.radioSockets'] = 'Funksteckdose';
$l['acp.switchserverManagement.form.switchServer.radioSockets'] = 'Radio controlled socket';
//$l['acp.switchserverManagement.form.switchServer.radioSockets.description'] = 'der Schaltserver kann Funksteckdosen schalten';
$l['acp.switchserverManagement.form.switchServer.radioSockets.description'] = 'der Schaltserver kann Funksteckdosen schalten';
//$l['acp.switchserverManagement.form.switchServer.readGPIO'] = 'GPIOs lesen';
$l['acp.switchserverManagement.form.switchServer.readGPIO'] = 'read GPIOs';
//$l['acp.switchserverManagement.form.switchServer.readGPIO.description'] = 'die GPIOs des Schaltservers können als Eingang abgefragt werden';
$l['acp.switchserverManagement.form.switchServer.readGPIO.description'] = 'the GPIOs of the switching server can be used as input';
//$l['acp.switchserverManagement.form.switchServer.writeGPIO'] = 'GPIOs schreiben';
$l['acp.switchserverManagement.form.switchServer.writeGPIO'] = 'write GPIOs';
//$l['acp.switchserverManagement.form.switchServer.writeGPIO.description'] = 'die GPIOs des Schaltservers können als Ausgang verwendet werden';
$l['acp.switchserverManagement.form.switchServer.writeGPIO.description'] = 'the GPIOs of the switching server can be used as output';
//$l['acp.switchserverManagement.form.switchServer.active'] = 'Aktiviert';
$l['acp.switchserverManagement.form.switchServer.active'] = 'Enabled';
//$l['acp.switchserverManagement.form.switchServer.active.description'] = 'Aktiviert/Deaktiviert den Schaltserver';
$l['acp.switchserverManagement.form.switchServer.active.description'] = 'Enables / Disables the switching server';

//Meldungen
//Notifications

//$l['acp.switchserverManagement.form.success.addSwitchServer'] =  'Der Schaltserver wurde erfolgreich erstellt';
$l['acp.switchserverManagement.form.success.addSwitchServer'] =  'The switching server was successfully created';
//$l['acp.switchserverManagement.form.success.editSwitchServer'] =  'Der Schaltserver wurde erfolgreich bearbeitet';
$l['acp.switchserverManagement.form.success.editSwitchServer'] =  'The switching server was successfully edited';
//$l['acp.switchserverManagement.form.success.deleteSwitchServer'] =  'Der Schaltserver wurde erfolgreich gelöscht';
$l['acp.switchserverManagement.form.success.deleteSwitchServer'] =  'The switching server was successfully deleted';
//$l['acp.switchserverManagement.form.error.1501'] =  'Der Name des Schaltservers ist schon vergeben';
$l['acp.switchserverManagement.form.error.1501'] =  'The name of the switching server is already taken';
//$l['acp.switchserverManagement.form.error.1102'] =  'Der Schaltserver konnte wegen fehlender Schreibrechte nicht gespeichert werden';
$l['acp.switchserverManagement.form.error.1102'] =  'The switching server could not be saved due to lack of write access';
//$l['acp.switchserverManagement.form.error.1102.del'] =  'Der Schaltserver konnte wegen fehlender Schreibrechte nicht gelöscht werden';
$l['acp.switchserverManagement.form.error.1102.del'] =  'The switching server could not be deleted due to lack of write access';
//$l['acp.switchserverManagement.form.error'] =  'Der Schaltserver konnte nicht gespeichert werden';
$l['acp.switchserverManagement.form.error'] =  'The switching server could not be saved';
//$l['acp.switchserverManagement.form.error.del'] =  'Der Schaltserver konnte nicht gelöscht werden';
$l['acp.switchserverManagement.form.error.del'] =  'The switching server could not be deleted';
//$l['acp.switchserverManagement.form.error.id'] =  'Ungültige ID';
$l['acp.switchserverManagement.form.error.id'] =  'Invalid ID';
