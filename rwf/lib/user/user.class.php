<?php

namespace RWF\User;

/**
 * Benutzer (angemeldet)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class User implements Visitor {

    /**
     * Benutzer ID
     * 
     * @var Integer
     */
    protected $id = 0;

    /**
     * Autorisierungs Code
     * 
     * @var String 
     */
    protected $authCode = '';

    /**
     * Benutzername
     * 
     * @var string
     */
    protected $name = '';

    /**
     * Passwort Hash
     * 
     * @var String
     */
    protected $passwordHash = '';

    /**
     * gibt an ob der Benutzer der Gruender der Seite ist
     * 
     * @var Boolean 
     */
    protected $isOriginator = false;

    /**
     * Sprach Code fuer die Anzeigesprache
     * 
     * @var String 
     */
    protected $language = null;
    
    /**
     * Name des Styles fuer die Webaoberflaeche
     * 
     * @var String 
     */
    protected $webStyle = null;
    
    /**
     * Name des Styles fuer die Mobiloberflaeche
     * 
     * @var String 
     */
    protected $mobileStyle = null;

    /**
     * Registrierungsdatum
     * 
     * @var \DateTime 
     */
    protected $registerDate = null;

    /**
     * Benutzer Hauptgruppe
     * 
     * @var \RWF\User\UserGroup
     */
    protected $mainGroup = null;

    /**
     * Benutzergruppen des Benutzers
     * 
     * @var Array 
     */
    protected $userGroups = array();

    /**
     * @param Integer             $id           Benutzer ID
     * @param String              $authCode     Autorisierungs Code
     * @param String              $name         Benutzername
     * @param String              $passwordHash Passwort Hash
     * @param Boolean             $isOriginator ist Gruender?
     * @param \RWF\User\UserGroup $mainGroup    Hauptgruppe
     * @param Array               $userGroups   Benutzergruppen
     * @param String              $language     Sprache
     * @param String              $webStyle     Name des Styles fuer die Webaoberflaeche 
     * @param String              $mobileStyle  Name des Styles fuer die Mobiloberflaeche
     * @param \DateTime           $registerDate Registrierungsdatum
     */
    public function __construct($id, $authCode, $name, $passwordHash, $isOriginator, UserGroup $mainGroup, array $userGroups, $language = null, $webStyle = null, $mobileStyle = null, \DateTime $registerDate = null) {

        $this->id = $id;
        $this->authCode = $authCode;
        $this->name = $name;
        $this->passwordHash = $passwordHash;
        $this->isOriginator = $isOriginator;
        $this->mainGroup = $mainGroup;
        $this->userGroups = $userGroups;
        $this->language = $language;
        $this->webStyle = $webStyle;
        $this->mobileStyle = $mobileStyle;
        $this->registerDate = $registerDate;
    }

    /**
     * gibt on ob es sich um einen Gast oder Benutzer handelt
     * 
     * @return Boolean
     */
    public function isUser() {
        
        return true;
    }
    
    /**
     * gibt die Benutzer ID zurueck
     * 
     * @return Integer
     */
    public function getId() {

        return $this->id;
    }

    /**
     * gibt den Autorisierungs Code zurueck
     * 
     * @return String
     */
    public function getAuthCode() {

        return $this->authCode;
    }

    /**
     * gibt den Benutzernamen zurueck
     * 
     * @return String
     */
    public function getName() {

        return $this->name;
    }

    /**
     * gibt an ob der Benutzer der Gruender der Seite ist
     * 
     * @return Boolean
     */
    public function isOriginator() {

        return $this->isOriginator;
    }

    /**
     * gibt den Sprach Code zurueckk
     * 
     * @return String
     */
    public function getLanguage() {

        return $this->language;
    }
    
    /**
     * gibt den Name des Styles fuer die Weboberflaeche zurueckk
     * 
     * @return String
     */
    public function getWebStyle() {
        
        return $this->webStyle;
    }
    
    /**
     * gibt den Name des Styles fuer die Mobiloberflaeche zurueckk
     * 
     * @return String
     */
    public function getMobileStyle() {
        
        return $this->mobileStyle;
    }

    /**
     * gibt ein Datumsobjekt mit dem Registrierungsdatum zurueck
     * 
     * @return \DateTime
     */
    public function getRegisterDate() {

        return $this->registerDate;
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

        return $this->userGroups;
    }

    /**
     * prueft ob das Passwort mit dem gespeicherten uebereinstimmt und gibt bei erfol den authCode zurueck
     * 
     * @param  String $password Passwort
     * @return String
     */
    public function checkPasswordHash($password) {

        //Passwort Libary einbinden fuer PHP Versionen < PHP 5.5
        require_once(PATH_RWF_CLASSES . 'external/password/password.php');
        if (password_verify($password, $this->passwordHash)) {

            return $this->authCode;
        }
        return null;
    }

    /**
     * prueft die Berechtigung des Benutzers (die rechte ergeben sich aus den Benutzergruppen)
     * 
     * @param  String  $permissions Recht
     * @return Boolean
     */
    public function checkPermission($permissions) {

        //Gruender?
        if ($this->isOriginator() === true) {

            return true;
        }

        //Hauptgruppe
        if ($this->mainGroup instanceof UserGroup && $this->mainGroup->checkPermission($permissions) === true) {

            return true;
        }

        //alle anderen Gruppen
        foreach ($this->userGroups as $group) {

            if ($group instanceof UserGroup && $group->checkPermission($permissions) === true) {

                return true;
            }
        }
        return false;
    }
    
    /**
     * wandelt das Objekt in einen String um
     */
    public function __toString() {
        
        return $this->getName();
    }

}
