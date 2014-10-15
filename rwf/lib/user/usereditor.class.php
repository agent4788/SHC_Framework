<?php

namespace RWF\User;

//Imports
use RWF\XML\XmlFileManager;
use RWF\Util\String;

/**
 * Benutzerverwaltung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserEditor {

    /**
     * nach ID sortieren
     * 
     * @var String
     */
    const SORT_BY_ID = 'id';
    
    /**
     * nach Namen sortieren
     * 
     * @var String
     */
    const SORT_BY_NAME = 'name';
    
    /**
     * nicht sortieren
     * 
     * @var String
     */
    const SORT_NOTHING = 'unsorted';
    
    /**
     * Liste mit allen Benutzergruppen
     * 
     * @var Array 
     */
    protected $userGroups = array();

    /**
     * Benutzer
     * 
     * @var Array 
     */
    protected $users = array();

    /**
     * Gast Objekt
     * 
     * @var \RWF\User\Guest 
     */
    protected $guest = null;

    /**
     * Singleton Instanz
     * 
     * @var RWF\User\UserEditor 
     */
    protected static $instance = null;

    protected function __construct() {

        //Daten einlesen
        $xml = XmlFileManager::getInstance()->getXmlObject(XmlFileManager::XML_USERS);

        //Benutzergruppen
        foreach ($xml->groups->group as $group) {

            //Berechtigungen
            $premissions = array();
            foreach ($group->premissions->premission as $premission) {

                $attr = $premission->attributes();
                $premissions[(string) $attr->name] = ((int) $attr->value == 1 ? true : false);
            }

            $this->userGroups[(int) $group->id] = new UserGroup(
                    (int) $group->id, (string) $group->name, (string) $group->description, $premissions, ((int) $group->isSystemGroup == 1 ? true : false)
            );
        }

        //Benutzer
        foreach ($xml->users->user as $user) {

            //Benutzergruppen des Benutzers
            $userGroups = array();
            foreach (explode(',', (string) $user->userGroups) as $groupId) {

                $userGroups[(int) $groupId] = $this->getUserGroupById((int) $groupId);
            }

            $this->users[(int) $user->id] = new User(
                    (int) $user->id, (string) $user->authCode, (string) $user->name, (string) $user->password, (bool) $user->isOriginator, $this->getUserGroupById((int) $user->mainUserGroup), $userGroups, ((string) $user->language != '' ? (string) $user->language : null), ((string) $user->webStyle != '' ? (string) $user->webStyle : null), ((string) $user->mobileStyle != '' ? (string) $user->mobileStyle : null), \DateTime::createFromFormat('Y-m-d', (string) $user->register)
            );
        }
    }

    /**
     * gibt ein Gast Objekt zurueck
     * 
     * @return \RWF\User\Guest 
     */
    public function getGuest() {

        if ($this->guest === null) {

            //Gast erstellen
            $this->guest = new Guest($this->getUserGroupById(RWF_GUEST_USER_GROUP));
        }
        return $this->guest;
    }

    /**
     * gibt eine Userobjekt anhand der ID zurueck
     * 
     * @param  Integer $id Benutzer ID
     * @return \RWF\User\User
     */
    public function getUserById($id) {

        if (isset($this->users[$id])) {

            return $this->users[$id];
        }
        return null;
    }

    /**
     * gibt ein Userobjekt anhand des Benutzernamens zurueck
     * 
     * @param  String $name Benutzername
     * @return \RWF\User\User
     */
    public function getUserByName($name) {

        foreach ($this->users as $user) {

            /* @var $user \RWF\User\User */
            if (String::toLower($user->getName()) == String::toLower($name)) {

                return $user;
            }
        }
        return null;
    }

    /**
     * gibt ein Userobjekt anhand des Autorisierungs Codes zurueck
     * 
     * @param  String $authCode Autorisierungs Code
     * @return \RWF\User\User
     */
    public function getUserByAuthCode($authCode) {

        foreach ($this->users as $user) {

            /* @var $user \RWF\User\User */
            if ($user->getAuthCode() == $authCode) {

                return $user;
            }
        }
        return null;
    }

    /**
     * prueft ob der Benutzername den Namensregeln entspricht
     * 
     * @param String $name Benutzername
     */
    public function checkUserName($name) {

        if (preg_match('#^[a-z0-9\#\_\!\-\.\,\;\+\*\?]{3,25}$#i', $name)) {

            return true;
        }
        return false;
    }

    /**
     * prueft ob der Benutzername schon vergeben ist
     * 
     * @param String $name Benutzername
     */
    public function isUserNameAvailable($name) {

        $user = $this->getUserByName($name);
        if ($user instanceof User) {

            return false;
        }
        return true;
    }

    /**
     * prueft ob ein Benutzer existiert
     * 
     * @param  Integer $id Benutzer ID
     * @return Boolean
     */
    public function userExists($id) {

        $user = $this->getUserById($id);
        if ($user instanceof User) {

            return true;
        }
        return false;
    }

    /**
     * gibt eine Liste mit allen Benutzern zurueck
     * 
     * @param  String $orderBy Art der Sortierung (id => nach ID sorieren, name => nach Namen sortieren, unsorted => unsortiert)
     * @return Array
     */
    public function listUsers($orderBy = 'id') {

        if ($orderBy == 'id') {

            //Benutzer nach ID sortieren
            $users = $this->users;
            ksort($users, SORT_NUMERIC);
            return $users;
        } elseif ($orderBy == 'name') {

            //Benuternamen als Index verwenden
            $users = $this->users;
            
            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($users, $orderFunction);
            return $users;
        }
        return $this->users;
    }

    /**
     * erstellt einen neuen Benutzer und gibt die Benutzer ID zurueck
     * 
     * @param  String  $name        Benutzername
     * @param  String  $password    Passwort
     * @param  Integer $mainGroupId ID der Hauptgruppe
     * @param  Array   $userGroups  IDs der Benutzergruppen
     * @param  Integer $language    Sprache
     * @param  String  $webStyle    Name des Styles fuer die Webaoberflaeche 
     * @param  String  $mobileStyle Name des Styles fuer die Mobiloberflaeche
     * @return Integer
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addUser($name, $password, $mainGroupId, array $userGroups = array(), $language = null, $webStyle = null, $mobileStyle = null) {

        //Passwort Libary einbinden fuer PHP Versionen < PHP 5.5
        require_once(PATH_RWF_CLASSES . 'external/password/password.php');

        //Ausnahme wenn Benutzername schon belegt
        if (!$this->isUserNameAvailable($name)) {

            throw new \Exception('Der Benutzername ist schon vergeben', 1110);
        }

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(XmlFileManager::XML_USERS, true);

        //Autoincrement
        $nextId = (int) $xml->users->nextAutoIncrementId;
        $xml->users->nextAutoIncrementId = $nextId + 1;

        //Datum
        $date = new \DateTime('now');

        //TAGs erstellen
        $user = $xml->users->addChild('user');
        $user->addChild('id', $nextId);
        $user->addChild('name', $name);
        $user->addChild('password', password_hash($password, PASSWORD_DEFAULT));
        $user->addChild('authCode', String::randomStr(64));
        $user->addChild('language', ($language !== null ? $language : ''));
        $user->addChild('webStyle', ($webStyle !== null ? $webStyle : ''));
        $user->addChild('mobileStyle', ($mobileStyle !== null ? $mobileStyle : ''));
        $user->addChild('register', $date->format('Y-m-d'));
        $user->addChild('mainUserGroup', $mainGroupId);
        $user->addChild('userGroups', implode(',', $userGroups));
        $user->addChild('isOriginator', 0);

        //Daten Speichern
        $xml->save();
        return $nextId;
    }

    /**
     * bearbeitet einen Benutzer
     * 
     * @param  Integer $id          Benutzer ID
     * @param  String  $name        Benutzername
     * @param  String  $password    Passwort
     * @param  Integer $mainGroupId ID der Hauptgruppe
     * @param  Array   $userGroups  IDs der Benutzergruppen
     * @param  Integer $language    Sprache
     * @param  String  $webStyle    Name des Styles fuer die Webaoberflaeche 
     * @param  String  $mobileStyle Name des Styles fuer die Mobiloberflaeche
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editUser($id, $name = null, $password = null, $mainGroupId = null, array $userGroups = null, $language = null, $webStyle = null, $mobileStyle = null) {

        //Passwort Libary einbinden fuer PHP Versionen < PHP 5.5
        require_once(PATH_RWF_CLASSES . 'external/password/password.php');

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(XmlFileManager::XML_USERS, true);

        foreach ($xml->users->user as $user) {

            //Benutzer suchen
            if ((int) $user->id == $id) {

                //Benutzername
                if ($name !== null) {

                    //puefen ob neuer Benutzername schon belegt
                    if ((string) $user->name != $name && !$this->isUserNameAvailable($name)) {

                        throw new \Exception('Der Benutzername ist schon vergeben', 1110);
                    }
                    $user->name = $name;
                }

                //Passwort
                if ($password !== null) {

                    $user->password = password_hash($password, PASSWORD_DEFAULT);
                }

                //Hauptgruppe
                if ($mainGroupId !== null) {

                    $user->mainUserGroup = $mainGroupId;
                }

                //Benutzergruppen
                if ($userGroups !== null) {

                    $user->userGroups = implode(',', $userGroups);
                }

                //Sprache
                if ($language !== null) {

                    $user->language = $language;
                }

                //Web Style
                if ($webStyle !== null) {

                    $user->webStyle = $webStyle;
                }

                //Mobile Style
                if ($mobileStyle !== null) {

                    $user->mobileStyle = $mobileStyle;
                }

                //Daten Speichern
                $xml->save();
                return true;
            }
        }
        return false;
    }

    /**
     * loescht einen Benutzer
     * 
     * @param  Integer $id Benutzer ID
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function removeUser($id) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(XmlFileManager::XML_USERS, true);

        for ($i = 0; $i < count($xml->users->user); $i++) {

            //Benutzer suchen
            if ((int) $xml->users->user[$i]->id == $id) {

                //Pruefen ob es nicht der Gruender ist
                if ((string) $xml->users->user[$i]->isOriginator == true) {

                    //Ausnahme der Gruender kann nicht geloescht werden
                    throw new \Exception('Der Gründer kann nicht gelöscht werden', 1111);
                }

                //Benutzer loeschen
                unset($xml->users->user[$i]);

                //Daten Speichern
                $xml->save();
                return true;
            }
        }
        return false;
    }

    /**
     * gibt eine UserGroupobjekt anhand der ID zurueck
     * 
     * @param  Integer $id Gruppen ID
     * @return \RWF\User\UserGroup
     */
    public function getUserGroupById($id) {

        if (isset($this->userGroups[$id])) {

            return $this->userGroups[$id];
        }
        return null;
    }

    /**
     * gibt eine Liste mit allen benutzergruppen zurueck
     * 
     * @param  String $orderBy Art der Sortierung (id => nach ID sorieren, name => nach Namen sortieren, unsorted => unsortiert)
     * @return Array
     */
    public function listUserGruops($orderBy = 'id') {

        if ($orderBy == 'id') {

            //Gruppen nach ID sortieren
            $userGroups = $this->userGroups;
            ksort($userGroups, SORT_NUMERIC);
            return $userGroups;
        } elseif ($orderBy == 'name') {

            //Gruppenname als Index verwenden
            $userGroups = array();
            foreach ($this->userGroups as $userGroup) {

                /* @var $userGroup \RWF\User\UserGroup */
                $userGroups[$userGroup->getName()] = $userGroup;
            }

            ksort($userGroups, SORT_STRING);
            return $userGroups;
        }
        return $this->userGroups;
    }

    /**
     * prueft ob der Gruppenname den Namensregeln entspricht
     * 
     * @param String $name Benutzername
     */
    public function checkUserGroupName($name) {

        if (preg_match('#^[a-z0-9\#\_\!\-\.\,\;\+\*\?]{3,50}$#i', $name)) {

            return true;
        }
        return false;
    }

    /**
     * prueft ob der Gruppen Name bereits vergeben ist
     * 
     * @param  String $name Gruppen Name
     * @return Boolean
     */
    public function isUserGroupNameAvailable($name) {

        foreach ($this->userGroups as $userGroup) {

            /* @var $userGroup \RWF\User\UserGroup */
            if (strtolower($userGroup->getName()) == strtolower($name)) {

                return false;
            }
        }
        return true;
    }

    /**
     * prueft ob eine Benutzergruppe existiert
     * 
     * @param  Integer $id Gruppen ID
     * @return Boolean
     */
    public function userGroupExists($id) {

        $userGroup = $this->getUserGroupById($id);
        if ($userGroup instanceof UserGroup) {

            return true;
        }
        return false;
    }

    /**
     * erstellt eine neue Benutzergruppe und gibt die Benutzer ID zurueck
     * 
     * @param  String  $name        Gruppen Name
     * @param  String  $description Beschreibung der Gruppe
     * @param  Array   $premissions Berechtigung (Name => Wert)
     * @return Integer
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addUserGroup($name, $description, array $premissions = array()) {

        //Ausnahme wenn Gruppenname schon belegt
        if (!$this->isUserGroupNameAvailable($name)) {

            throw new \Exception('Der Gruppenname ist schon vergeben', 1112);
        }

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(XmlFileManager::XML_USERS, true);

        //Autoincrement
        $nextId = (int) $xml->groups->nextAutoIncrementId;
        $xml->groups->nextAutoIncrementId = $nextId + 1;

        //TAGs erstellen
        $group = $xml->groups->addChild('group');
        $group->addChild('id', $nextId);
        $group->addChild('name', $name);
        $group->addChild('description', $description);
        $group->addChild('isSystemGroup', 0);
        $premissionsTag = $group->addChild('premissions');

        //Berechtigungen hinzufuegen
        foreach ($premissions as $name => $value) {

            $premission = $premissionsTag->addChild('premission');
            $premission->addAttribute('name', $name);
            $premission->addAttribute('value', ($value == true ? 1 : 0));
        }

        //Daten Speichern
        $xml->save();
        return $nextId;
    }

    /**
     * bearbeitet eine Benutzergruppe
     * 
     * @param  Integer $id          Gruppen ID
     * @param  String  $name        Gruppen Name
     * @param  String  $description Beschreibung der Gruppe
     * @param  Array   $premissions Berechtigung (Name => Wert)
     * @return Integer
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editUserGroup($id, $name = null, $description = null, array $premissions = array()) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(XmlFileManager::XML_USERS, true);

        foreach ($xml->groups->group as $group) {

            //Gruppe suchen
            if ((int) $group->id == $id) {

                //Grupenname
                if ($name !== null) {

                    //puefen ob neuer Gruppenname schon belegt
                    if ($name != (string) $group->name && !$this->isUserGroupNameAvailable($name)) {

                        throw new \Exception('Der Gruppenname ist schon vergeben', 1112);
                    }
                    $group->name = $name;
                }

                //Passwort
                if ($description !== null) {

                    $group->description = $description;
                }

                //Berechtigungen
                foreach ($premissions as $name => $value) {

                    //XML Tag suchen
                    $found = false;
                    foreach ($group->premissions->premission as $premission) {

                        $attr = $premission->attributes();
                        if ((string) $attr->name == $name) {

                            $attr->value = ($value == true ? 1 : 0);
                            $found = true;
                            break;
                        }
                    }

                    //TAG nicht gefunden, neues erstellen
                    $premission = $group->premissions->addChild('premission');
                    $premission->addAttribute('name', $name);
                    $premission->addAttribute('value', ((bool) $value == true ? 1 : 0));
                }

                //Daten Speichern
                $xml->save();
                return true;
            }
        }
        return false;
    }

    /**
     * loescht eine Benutzergruppe
     * 
     * @param  Integer $id Gruppen ID
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function removeUserGroup($id) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(XmlFileManager::XML_USERS, true);

        for ($i = 0; $i < count($xml->groups->group); $i++) {

            //Benutzer suchen
            if ((int) $xml->groups->group[$i]->id == $id) {

                //Pruefen ob es es sich um eine Systemgruppe handelt
                if ((int) $xml->groups->group[$i]->isSystemGroup == true) {

                    //Ausnahme der Gruender kann nicht geloescht werden
                    throw new \Exception('Eine System Gruppe kann nicht gelöscht werden', 1113);
                }

                //Gruppe loeschen
                unset($xml->groups->group[$i]);

                //Daten Speichern
                $xml->save();
                return true;
            }
        }
        return false;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt den Benutzer Editor zurueck
     * 
     * @return \RWF\User\UserEditor 
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new UserEditor();
        }
        return self::$instance;
    }

}
