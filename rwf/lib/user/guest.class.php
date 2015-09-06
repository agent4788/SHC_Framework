<?php

namespace RWF\User;

/**
 * Gast
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Guest implements Visitor {

    /**
     * Benutzer Hauptgruppe
     * 
     * @var \RWF\User\UserGroup
     */
    protected $mainGroup = null;

    /**
     * @param \RWF\User\UserGroup $mainGroup Benutzergruppe
     */
    public function __construct(UserGroup $mainGroup) {

        $this->mainGroup = $mainGroup;
    }
    
    /**
     * gibt on ob es sich um einen Gast oder Benutzer handelt
     * 
     * @return Boolean
     */
    public function isUser() {
        
        return false;
    }

    /**
     * gibt an ob der Benutzer der Gruender der Seite ist
     * 
     * @return Boolean
     */
    public function isOriginator() {

        return false;
    }

    /**
     * gibt den Sprach Code zurueckk
     * 
     * @return String
     */
    public function getLanguage() {

        return null;
    }

    /**
     * gibt das Gruppenobjekt der Hauptgruppe zurueck
     * 
     * @return \RWF\User\UserGroup
     */
    public function getMainGroup() {

        return $this->mainGroup;
    }

    /**
     * gibt ein Array mit allen Gruppenobjekten des Users zurueck
     * 
     * @return Array
     */
    public function listGroups() {

        return array();
    }

    /**
     * prueft die Berechtigung des Benutzers (die rechte ergeben sich aus den Benutzergruppen)
     * 
     * @param  String  $permission Recht
     * @return Boolean
     */
    public function checkPermission($permission) {

        //Hauptgruppe
        if ($this->mainGroup instanceof UserGroup && $this->mainGroup->checkPermission($permission) === true) {

            return true;
        }
        return false;
    }

}
