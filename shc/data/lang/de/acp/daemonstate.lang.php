<?php

/**
 * Serverstatus Sprachvariablen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Titel
$l['acp.daemonState.title'] = 'Serverstatus';

//Schaltserver
$l['acp.daemonState.box.switchServer'] = 'Schaltserver';
$l['acp.daemonState.box.daemons'] = 'Dienste';
$l['acp.daemonState.box.name'] = 'Name';
$l['acp.daemonState.box.deamon'] = 'Dienst';
$l['acp.daemonState.box.deamon.task'] = 'Sheduler (-sh oder --sheduler)';
$l['acp.daemonState.box.deamon.sensorDataTransmitter'] = 'Sensor Transmitter (-st oder --sensortransmitter)';
$l['acp.daemonState.box.state'] = 'Status';
$l['acp.daemonState.box.state.run'] = 'läuft';
$l['acp.daemonState.box.state.stop'] = 'läuft nicht';
$l['acp.daemonState.box.state.notActive'] = 'deaktiviert';

//Meldungen
$l['acp.daemonState.noRunningServer'] = 'Kein laufender Schaltserver gefunden. Für die Funktion des SHC muss mindestens ein Schaltserver erreichbar sein!';