<?php

/**
 * Sprachvariablen fuer den Sheduler
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
$l = array();

//Eingaben
$l['shedulerDaemon.input.active'] = 'Sheduler Dienst aktiviert ({1:s}): ';
$l['shedulerDaemon.input.active.invalid'] = 'Ungültige Eingabe';
$l['shedulerDaemon.input.active.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';

$l['shedulerDaemon.input.blinkPin'] = 'Status LED Pin [-1 wenn deaktiviert] ({1:s}): ';
$l['shedulerDaemon.input.blinkPin.invalid'] = 'Ungültige Eingabe';
$l['shedulerDaemon.input.blinkPin.invalid.repeated'] = 'du hast zu oft eine ungültige Angaben eingegeben';

//Meldungen
$l['shedulerDaemon.input.save.success'] = 'Die Einstellungen wurden erfolgreich gespeichert und werden nach dem nächsten neustart des Servers aktiv';
$l['shedulerDaemon.input.save.error'] = 'Die Einstellungen konnten nicht gespeichert werden';