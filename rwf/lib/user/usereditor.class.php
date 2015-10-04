<?php

namespace RWF\User;

//Imports
use RWF\Core\RWF;
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
     * liste mit allen bekannten Rechnetn
     *
     * @var array
     */
    protected $permissions = array();

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
     * @var \RWF\User\UserEditor
     */
    protected static $instance = null;

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $usersTableName = 'users';

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $groupsTableName = 'groups';

    /**
     * laedt die Benutzer und Gruppen
     */
    public function loadData() {

        $db = RWF::getDatabase();

        //Benutzergruppen laden
        $groups = $db->hGetAllArray(self::$groupsTableName);
        foreach($groups as $group) {

            //Gruppendaten Laden
            $permissions = array();
            foreach ($this->permissions as $name => $value) {

                $permissions[$name] = (isset($group['permissions'][$name]) ? $group['permissions'][$name] : $value);
            }

            $this->userGroups[$group['id']] = new UserGroup(
                $group['id'], $group['name'], $group['description'], $permissions, ($group['isSystemGroup'] == 1 ? true : false)
            );
        }

        //Benutzerladen
        $users = $db->hGetAllArray(self::$usersTableName);
        foreach($users as $user) {

            //Gruppen vorbereiten
            $userGroups = array();
            foreach ($user['userGroups'] as $groupId) {

                $userGroups[$groupId] = $this->getUserGroupById($groupId);
            }

            //Benutzer laden
            $this->users[$user['id']] = new User(
                $user['id'],
                $user['authCode'],
                $user['name'],
                $user['password'],
                ($user['isOriginator'] == 1 ? true : false),
                $this->getUserGroupById($user['mainUserGroup']),
                $userGroups,
                ($user['language'] != '' ? $user['language'] : null),
                ($user['webStyle'] != '' ? $user['webStyle'] : null),
                ($user['mobileStyle'] != '' ? $user['mobileStyle'] : null),
                \DateTime::createFromFormat('Y-m-d', (string) $user['register'])
            );
        }

        //Benutzer und Gruppen initialisieren
        if(count($this->users) == 0) {

            $this->addSystemUsersAndGroups();
            $this->loadData();
        }
    }

    /**
     * erstellt die Systembenutzer und Systemgruppen
     */
    protected function addSystemUsersAndGroups() {

        //Passwort Libary einbinden fuer PHP Versionen < PHP 5.5
        require_once(PATH_RWF_CLASSES . 'external/password/password.php');

        $db = RWF::getDatabase();
        $date = new \DateTime('now');

        //Benutzer
        $db->autoIncrement(self::$usersTableName);
        $newUser = array(
            'id' => 1,
            'name' => 'admin',
            'password' => password_hash('admin', PASSWORD_DEFAULT),
            'authCode' => String::randomStr(64),
            'language' => '',
            'webStyle' => '',
            'mobileStyle' => '',
            'register' => $date->format('Y-m-d'),
            'mainUserGroup' => 1,
            'userGroups' => array(),
            'isOriginator' => 1
        );

        if($db->hSetNxArray(self::$usersTableName, 1, $newUser) == 0) {

            return false;
        }

        //Gruppen
        $db->autoIncrement(self::$groupsTableName);
        $newGroup = array(
            'id' => 1,
            'name' => 'Administratoren',
            'description' => 'Die Benutzer dieser Gruppen können die Anwendung verwalten',
            'isSystemGroup' => 1,
            'permissions' => array()
        );

        if($db->hSetNxArray(self::$groupsTableName, 1, $newGroup) == 0) {

            return false;
        }

        $db->autoIncrement(self::$groupsTableName);
        $newGroup = array(
            'id' => 2,
            'name' => 'Benutzer',
            'description' => 'angemeldete Benutzer',
            'isSystemGroup' => 1,
            'permissions' => array()
        );

        if($db->hSetNxArray(self::$groupsTableName, 2, $newGroup) == 0) {

            return false;
        }

        $db->autoIncrement(self::$groupsTableName);
        $newGroup = array(
            'id' => 3,
            'name' => 'Gäste',
            'description' => 'Besucher der Seite (nicht angemeldet)',
            'isSystemGroup' => 1,
            'permissions' => array()
        );

        if($db->hSetNxArray(self::$groupsTableName, 3, $newGroup) == 0) {

            return false;
        }
        return true;
    }

    /**
     * erstellt ein neues Recht
     *
     * @param  string $name         Name der Berechtigung
     * @param  bool  $defaultValue Standardwert
     * @return bool
     */
    public function addPermission($name, $defaultValue) {

        if(!isset($this->permissions[$name])) {

            $this->permissions[$name] = (bool) $defaultValue;
            return true;
        }
        return false;
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
     */
    public function addUser($name, $password, $mainGroupId, array $userGroups = array(), $language = null, $webStyle = null, $mobileStyle = null) {

        //Passwort Libary einbinden fuer PHP Versionen < PHP 5.5
        require_once(PATH_RWF_CLASSES . 'external/password/password.php');

        //Ausnahme wenn Benutzername schon belegt
        if (!$this->isUserNameAvailable($name)) {

            throw new \Exception('Der Benutzername ist schon vergeben', 1110);
        }

        $db = RWF::getDatabase();

        //Datum
        $date = new \DateTime('now');

        //ID
        $id = $db->autoIncrement(self::$usersTableName);

        //Benutzergruppen vorbereiten
        $groups = array();
        foreach($userGroups as $group) {

            /** @var $group \RWF\User\UserGroup */
            if($group !== null) {

                $groups[] = (int) $group;
            }
        }

        //User Objekt
        $newUser = array(
            'id' => $id,
            'name' => $name,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'authCode' => String::randomStr(64),
            'language' => ($language !== null ? $language : ''),
            'webStyle' => ($webStyle !== null ? $webStyle : ''),
            'mobileStyle' => ($mobileStyle !== null ? $mobileStyle : ''),
            'register' => $date->format('Y-m-d'),
            'mainUserGroup' => $mainGroupId,
            'userGroups' => $groups,
            'isOriginator' => 0
        );

        if($db->hSetNxArray(self::$usersTableName, $id, $newUser) == 0) {

            return false;
        }
        return true;
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
     */
    public function editUser($id, $name = null, $password = null, $mainGroupId = null, array $userGroups = null, $language = null, $webStyle = null, $mobileStyle = null) {

        $db = RWF::getDatabase();

        //pruefen ob Datensatz existiert
        if($db->hExists(self::$usersTableName, $id)) {

            $user = $db->hGetArray(self::$usersTableName, $id);

            //Benutzername
            if ($name !== null) {

                //puefen ob neuer Benutzername schon belegt
                if ((string) $user['name'] != $name && !$this->isUserNameAvailable($name)) {

                    throw new \Exception('Der Benutzername ist schon vergeben', 1110);
                }
                $user['name'] = $name;
            }

            //Passwort
            if ($password !== null) {

                //Passwort Libary einbinden fuer PHP Versionen < PHP 5.5
                require_once(PATH_RWF_CLASSES . 'external/password/password.php');

                $user['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            //Hauptgruppe
            if ($mainGroupId !== null) {

                $user['mainUserGroup'] = $mainGroupId;
            }

            //Benutzergruppen
            if (count($userGroups)) {

                //Benutzergruppen vorbereiten
                $groups = array();
                foreach($userGroups as $group) {

                    /** @var $group \RWF\User\UserGroup */
                    if($group !== null) {

                        $groups[] = (int) $group;
                    }
                }
                $user['userGroups'] = $groups;
            }

            //Sprache
            if ($language !== null) {

                $user['language'] = $language;
            }

            //Web Style
            if ($webStyle !== null) {

                $user['webStyle'] = $webStyle;
            }

            //Mobile Style
            if ($mobileStyle !== null) {

                $user['mobileStyle'] = $mobileStyle;
            }

            if($db->hSetArray(self::$usersTableName, $id, $user) == 0) {

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
     */
    public function removeUser($id) {

        $db = RWF::getDatabase();

        //pruefen ob Datensatz existiert
        if($db->hExists(self::$usersTableName, $id)) {

            if($db->hDel(self::$usersTableName, $id)) {

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
     * @param  Array   $permissions Berechtigung (Name => Wert)
     * @return Integer
     */
    public function addUserGroup($name, $description, array $permissions = array()) {

        //Ausnahme wenn Gruppenname schon belegt
        if (!$this->isUserGroupNameAvailable($name)) {

            throw new \Exception('Der Gruppenname ist schon vergeben', 1112);
        }

        $db = RWF::getDatabase();
        $id = $db->autoIncrement(self::$groupsTableName);
        $newGroup = array(
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'isSystemGroup' => 0,
            'permissions' => $permissions
        );

        if($db->hSetNxArray(self::$groupsTableName, $id, $newGroup) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet eine Benutzergruppe
     * 
     * @param  Integer $id          Gruppen ID
     * @param  String  $name        Gruppen Name
     * @param  String  $description Beschreibung der Gruppe
     * @param  Array   $permissions Berechtigung (Name => Wert)
     * @return Integer
     */
    public function editUserGroup($id, $name = null, $description = null, array $permissions = array()) {

        $db = RWF::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$groupsTableName, $id)) {

            $group = $db->hGetArray(self::$groupsTableName, $id);

            //Grupenname
            if ($name !== null) {

                //puefen ob neuer Gruppenname schon belegt
                if ($name != (string) $group['name'] && !$this->isUserGroupNameAvailable($name)) {

                    throw new \Exception('Der Gruppenname ist schon vergeben', 1112);
                }
                $group['name'] = $name;
            }

            //Beschreibung
            if ($description !== null) {

                $group['description'] = $description;
            }

            //Berechtigungen
            if(count($permissions) > 0) {

                foreach($this->permissions as $name => $value) {

                    $group['permissions'][$name] = (isset($permissions[$name]) ? $permissions[$name] : $value);
                }
            }

            if($db->hSetArray(self::$groupsTableName, $id, $group) == 0) {

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
     */
    public function removeUserGroup($id) {

        $db = RWF::getDatabase();

        //pruefen ob Datensatz existiert
        if($db->hExists(self::$groupsTableName, $id)) {

            if($db->hDel(self::$groupsTableName, $id)) {

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
