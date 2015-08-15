<?php

namespace RWF\User;

/**
 * Besucher Schnittstelle
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Visitor {

    /**
     * gibt on ob es sich um einen Gast oder Benutzer handelt
     * 
     * @return Boolean
     */
    public function isUser();
    
    /**
     * gibt an ob der Benutzer der Gruender der Seite ist
     * 
     * @return Boolean
     */
    public function isOriginator();

    /**
     * gibt den Sprach Code zurueckk
     * 
     * @return String
     */
    public function getLanguage();

    /**
     * gibt das Gruppenobjekt der Hauptgruppe zurueck
     * 
     * @return \RWF\User\UserGroup
     */
    public function getMainGroup();

    /**
     * gibt ein Array mit allen Gruppenobjekten des Users zurueck
     * 
     * @return Array
     */
    public function listGroups();

    /**
     * prueft die Berechtigung des Benutzers (die rechte ergeben sich aus den Benutzergruppen)
     * 
     * @param  String  $premission Recht
     * @return Boolean
     */
    public function checkPermission($permission);
}
