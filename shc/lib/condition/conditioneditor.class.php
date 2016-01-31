<?php

namespace SHC\Condition;

//Imports
use RWF\Util\String;
use SHC\Core\SHC;

/**
 * Beingungen verwalten
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.2-0
 */
class ConditionEditor {

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
     * Bedingungen
     * 
     * @var Array 
     */
    protected $conditions = array();

    /**
     * Singleton Instanz
     * 
     * @var \SHC\Condition\ConditionEditor
     */
    protected static $instance = null;

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'shc:conditions';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * laedt die Bedingungen aus den XML Daten und erzeugt die Objekte
     */
    public function loadData() {

        //alte Daten loeschen
        $this->conditions = array();

        $conditions = SHC::getDatabase()->hGetAllArray(self::$tableName);
        foreach($conditions as $condition) {

            $class = (string) $condition['class'];

            $data = array();
            foreach ($condition as $index => $value) {

                if (!in_array($index, array('id', 'name', 'class', 'enabled'))) {

                    $data[$index] = $value;
                }
            }

            $this->conditions[(int) $condition['id']] = new $class(
                (int) $condition['id'], (string) $condition['name'], $data, ((int) $condition['enabled'] == 1 ? true : false)
            );
        }
    }

    /**
     * gibt die Bedingung mit der ID zurueck
     * 
     * @param  Integer $id ID
     * @return \SHC\Condition\Condition
     */
    public function getConditionByID($id) {

        if (isset($this->conditions[$id])) {

            return $this->conditions[$id];
        }
        return null;
    }

    /**
     * prueft ob der Name der Bedingung schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isConditionNameAvailable($name) {

        foreach ($this->conditions as $condition) {

            /* @var $condition \SHC\Condition\Condition */
            if (String::toLower($condition->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }

    /**
     * gibt eine Liste mit allen Bedingungen zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listConditions($orderBy = 'id') {

        if ($orderBy == 'id') {

            //nach ID sortieren
            $conditions = $this->conditions;
            ksort($conditions, SORT_NUMERIC);
            return $conditions;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $conditions = $this->conditions;

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
            usort($conditions, $orderFunction);
            return $conditions;
        }
        return $this->conditions;
    }

    /**
     * erstellt eine neue Bedingung
     * 
     * @param  String  $class   Klassenname
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @param  Array   $data    Zusatzdaten
     * @return Boolean
     * @throws \Exception
     */
    protected function addCondition($class, $name, $enabled, array $data = array()) {

        //Ausnahme wenn Name der Bedingung schon belegt
        if (!$this->isConditionNameAvailable($name)) {

            throw new \Exception('Der Name der Bedingung ist schon vergeben', 1502);
        }

        $db = SHC::getDatabase();
        $index = $db->autoIncrement(self::$tableName);
        $newCondition = array(
            'id' => $index,
            'class' => $class,
            'name' => $name,
            'enabled' => ($enabled == true ? true : false)
        );

        foreach ($data as $tag => $value) {

            if (!in_array($tag, array('id', 'name', 'class', 'enabled'))) {

                $newCondition[$tag] = $value;
            }
        }

        if($db->hSetNxArray(self::$tableName, $index, $newCondition) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet eine Bedingung
     * 
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @param  Array   $data    Zusatzdaten
     * @return Boolean
     * @throws \Exception
     */
    public function editCondition($id, $name, $enabled, array $data = array()) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $condition = $db->hGetArray(self::$tableName, $id);

            //Name
            if ($name !== null) {

                //Ausnahme wenn Name der Bedingung schon belegt
                if ((string) $condition['name'] != $name && !$this->isConditionNameAvailable($name)) {

                    throw new \Exception('Der Name der Bedingung ist schon vergeben', 1502);
                }

                $condition['name'] = $name;
            }

            //Aktiv
            if ($enabled !== null) {

                $condition['enabled'] = ($enabled == true ? 1 : 0);
            }

            //Zusatzdaten
            foreach($data as $tag => $value) {

                if (!in_array($tag, array('id', 'name', 'class', 'enabled'))) {

                    if($value !== null) {

                        $condition[$tag] = $value;
                    }
                }
            }

            //Daten Speichern
            if($db->hSetArray(self::$tableName, $id, $condition) == 0) {

                return true;
            }

        }
        return false;
    }

    /**
     * erstellt eine neue Datumsbedingung
     * 
     * @param  String  $name    Name
     * @param  String  $start   Startdatum
     * @param  String  $end     Enddatum
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addDateCondition($name, $start, $end, $enabled) {

        //Daten vorbereiten
        $data = array(
            'start' => $start,
            'end' => $end
        );

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\DateCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Datumsbedingung
     * 
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  String  $start   Startdatum
     * @param  String  $end     Enddatum
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editDateCondition($id, $name = null, $start = null, $end = null, $enabled = null) {
        
        //Daten vorbereiten
        $data = array(
            'start' => $start,
            'end' => $end
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Wochentagsbedingung
     * 
     * @param  String  $name    Name
     * @param  String  $start   Starttag
     * @param  String  $end     Endtag
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addDayOfWeekCondition($name, $start, $end, $enabled) {

        //Daten vorbereiten
        $data = array(
            'start' => $start,
            'end' => $end
        );

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\DayOfWeekCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Wochentagsbedingung
     * 
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  String  $start   Starttag
     * @param  String  $end     Endtag
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editDayOfWeekCondition($id, $name = null, $start = null, $end = null, $enabled = null) {
        
        //Daten vorbereiten
        $data = array(
            'start' => $start,
            'end' => $end
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Zeitbedingung
     * 
     * @param  String  $name    Name
     * @param  String  $start   Startzeit
     * @param  String  $end     Endzeit
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addTimeOfDayCondition($name, $start, $end, $enabled) {

        //Daten vorbereiten
        $data = array(
            'start' => $start,
            'end' => $end
        );

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\TimeOfDayCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Zeitbedingung
     * 
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  String  $start   Startzeit
     * @param  String  $end     Endzeit
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editTimeOfDayCondition($id, $name = null, $start = null, $end = null, $enabled = null) {
        
        //Daten vorbereiten
        $data = array(
            'start' => $start,
            'end' => $end
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Bedingung fuer den Zeitraum zwischen Sonnenauf- und Sonnenuntergang
     * 
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addSunriseSunsetCondition($name, $enabled) {

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\SunriseSunsetCondition', $name, $enabled);
    }

    /**
     * bearbeitet eine Bedingung fuer den Zeitraum zwischen Sonnenauf- und Sonnenuntergang
     * 
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editSunriseSunsetCondition($id, $name = null, $enabled = null) {

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled);
    }

    /**
     * erstellt eine neue Bedingung fuer den Zeitraum zwischen Sonnenunter- und Sonnenaufgang
     * 
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addSunsetSunriseCondition($name, $enabled) {

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\SunsetSunriseCondition', $name, $enabled);
    }

    /**
     * bearbeitet eine Bedingung fuer den Zeitraum zwischen Sonnenunter- und Sonnenaufgang
     * 
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editSunsetSunriseCondition($id, $name = null, $enabled = null) {
        
        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled);
    }

    /**
     * erstellt eine neue Bedingung die prueft das niemand zu Hause ist
     *
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addNobodyAtHomeCondition($name, $enabled) {

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\NobodyAtHomeCondition', $name, $enabled);
    }

    /**
     * bearbeitet eine Bedingung die prueft das niemand zu Hause ist
     *
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editNobodyAtHomeCondition($id, $name = null, $enabled = null) {

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled);
    }

    /**
     * erstellt eine neue Bedingung fuer die Ueberwachung ob ein Benutzer zu Hause ist
     *
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addUserAtHomeCondition($name, array $users, $enabled) {

        //Daten vorbereiten
        $data = array(
            'users' => implode(',', $users)
        );

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\UserAtHomeCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Bedingung fuer die Ueberwachung ob ein Benutzer zu Hause ist
     *
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editUserAtHomeCondition($id, $name = null, array $users = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'users' => ($users !== null ? implode(',', $users) : null)
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Bedingung fuer die Ueberwachung ob ein Benutzer nicht zu Hause ist
     *
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addUserNotAtHomeCondition($name, array $users, $enabled) {

        //Daten vorbereiten
        $data = array(
            'users' => implode(',', $users)
        );

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\UserNotAtHomeCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Bedingung fuer die Ueberwachung ob ein Benutzer nicht zu Hause ist
     *
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editUserNotAtHomeCondition($id, $name = null, array $users = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'users' => ($users !== null ? implode(',', $users) : null)
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Vergleichsbedingung
     *
     * @param  String  $name      Name
     * @param  Array   $sensorIds Liste mit Sensoren
     * @param  Integer $humidity  Luftfeuchte als Grenzwert
     * @param  Boolean $enabled   Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addHumidityGreaterThanCondition($name, array $sensorIds, $humidity, $enabled) {

        //Daten vorbereiten
        $data = array(
            'sensors' => implode(',', $sensorIds),
            'humidity'=> $humidity
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\HumidityGreaterThanCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Vergleichsbedingung
     *
     * @param  Integer $id        ID
     * @param  String  $name      Name
     * @param  Array   $sensorIds Liste mit Sensoren
     * @param  Integer $humidity  Luftfeuchte als Grenzwert
     * @param  Boolean $enabled   Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editHumidityGreaterThanCondition($id, $name = null, array $sensorIds = null, $humidity = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'sensors' => ($sensorIds !== null ? implode(',', $sensorIds) : null),
            'humidity'=> $humidity
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Vergleichsbedingung
     *
     * @param  String  $name      Name
     * @param  Array   $sensorIds Liste mit Sensoren
     * @param  Integer $humidity  Luftfeuchte als Grenzwert
     * @param  Boolean $enabled   Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addHumidityLowerThanCondition($name, array $sensorIds, $humidity, $enabled) {

        //Daten vorbereiten
        $data = array(
            'sensors' => implode(',', $sensorIds),
            'humidity'=> $humidity
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\HumidityLowerThanCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Vergleichsbedingung
     *
     * @param  Integer $id        ID
     * @param  String  $name      Name
     * @param  Array   $sensorIds Liste mit Sensoren
     * @param  Integer $humidity  Luftfeuchte als Grenzwert
     * @param  Boolean $enabled   Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editHumidityLowerThanCondition($id, $name = null, array $sensorIds = null, $humidity = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'sensors' => ($sensorIds !== null ? implode(',', $sensorIds) : null),
            'humidity'=> $humidity
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Vergleichsbedingung
     *
     * @param  String  $name            Name
     * @param  Array   $sensorIds       Liste mit Sensoren
     * @param  Integer $lightIntensity  Lichtstaerke als Grenzwert
     * @param  Boolean $enabled         Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addLightIntensityGreaterThanCondition($name, array $sensorIds, $lightIntensity, $enabled) {

        //Daten vorbereiten
        $data = array(
            'sensors' => implode(',', $sensorIds),
            'lightIntensity'=> $lightIntensity
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\LightIntensityGreaterThanCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Vergleichsbedingung
     *
     * @param  Integer $id              ID
     * @param  String  $name            Name
     * @param  Array   $sensorIds       Liste mit Sensoren
     * @param  Integer $lightIntensity  Lichtstaerke als Grenzwert
     * @param  Boolean $enabled         Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editLightIntensityGreaterThanCondition($id, $name = null, array $sensorIds = null, $lightIntensity = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'sensors' => ($sensorIds !== null ? implode(',', $sensorIds) : null),
            'lightIntensity'=> $lightIntensity
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Vergleichsbedingung
     *
     * @param  String  $name            Name
     * @param  Array   $sensorIds       Liste mit Sensoren
     * @param  Integer $lightIntensity  Lichtstaerke als Grenzwert
     * @param  Boolean $enabled         Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addLightIntensityLowerThanCondition($name, array $sensorIds, $lightIntensity, $enabled) {

        //Daten vorbereiten
        $data = array(
            'sensors' => implode(',', $sensorIds),
            'lightIntensity'=> $lightIntensity
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\LightIntensityLowerThanCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Vergleichsbedingung
     *
     * @param  Integer $id              ID
     * @param  String  $name            Name
     * @param  Array   $sensorIds       Liste mit Sensoren
     * @param  Integer $lightIntensity  Lichtstaerke als Grenzwert
     * @param  Boolean $enabled         Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editLightIntensityLowerThanCondition($id, $name = null, array $sensorIds = null, $lightIntensity = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'sensors' => ($sensorIds !== null ? implode(',', $sensorIds) : null),
            'lightIntensity'=> $lightIntensity
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Vergleichsbedingung
     *
     * @param  String  $name      Name
     * @param  Array   $sensorIds Liste mit Sensoren
     * @param  Integer $moisture  Feuchtigkeit als Grenzwert
     * @param  Boolean $enabled   Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addMoistureGreaterThanCondition($name, array $sensorIds, $moisture, $enabled) {

        //Daten vorbereiten
        $data = array(
            'sensors' => implode(',', $sensorIds),
            'moisture'=> $moisture
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\MoistureGreaterThanCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Vergleichsbedingung
     *
     * @param  Integer $id        ID
     * @param  String  $name      Name
     * @param  Array   $sensorIds Liste mit Sensoren
     * @param  Integer $moisture  Feuchtigkeit als Grenzwert
     * @param  Boolean $enabled   Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editMoistureGreaterThanCondition($id, $name = null, array $sensorIds = null, $moisture = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'sensors' => ($sensorIds !== null ? implode(',', $sensorIds) : null),
            'moisture'=> $moisture
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Vergleichsbedingung
     *
     * @param  String  $name      Name
     * @param  Array   $sensorIds Liste mit Sensoren
     * @param  Integer $moisture  Feuchtigkeit als Grenzwert
     * @param  Boolean $enabled   Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addLMoistureLowerThanCondition($name, array $sensorIds, $moisture, $enabled) {

        //Daten vorbereiten
        $data = array(
            'sensors' => implode(',', $sensorIds),
            'moisture'=> $moisture
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\MoistureLowerThanCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Vergleichsbedingung
     *
     * @param  Integer $id        ID
     * @param  String  $name      Name
     * @param  Array   $sensorIds Liste mit Sensoren
     * @param  Integer $moisture  Feuchtigkeit als Grenzwert
     * @param  Boolean $enabled   Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editMoistureLowerThanCondition($id, $name = null, array $sensorIds = null, $moisture = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'sensors' => ($sensorIds !== null ? implode(',', $sensorIds) : null),
            'moisture'=> $moisture
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Vergleichsbedingung
     *
     * @param  String  $name         Name
     * @param  Array   $sensorIds    Liste mit Sensoren
     * @param  Float   $temperature  Temperatur als Grenzwert
     * @param  Boolean $enabled      Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addTemperatureGreaterThanCondition($name, array $sensorIds, $temperature, $enabled) {

        //Daten vorbereiten
        $data = array(
            'sensors' => implode(',', $sensorIds),
            'temperature'=> $temperature
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\TemperatureGreaterThanCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Vergleichsbedingung
     *
     * @param  Integer $id           ID
     * @param  String  $name         Name
     * @param  Array   $sensorIds    Liste mit Sensoren
     * @param  Float   $temperature  Temperatur als Grenzwert
     * @param  Boolean $enabled      Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editTemperatureGreaterThanCondition($id, $name = null, array $sensorIds = null, $temperature = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'sensors' => ($sensorIds !== null ? implode(',', $sensorIds) : null),
            'temperature'=> $temperature
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Vergleichsbedingung
     *
     * @param  String  $name         Name
     * @param  Array   $sensorIds    Liste mit Sensoren
     * @param  Float   $temperature  Temperatur als Grenzwert
     * @param  Boolean $enabled      Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addTemperatureLowerThanCondition($name, array $sensorIds, $temperature, $enabled) {

        //Daten vorbereiten
        $data = array(
            'sensors' => implode(',', $sensorIds),
            'temperature'=> $temperature
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\TemperatureLowerThanCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Vergleichsbedingung
     *
     * @param  Integer $id           ID
     * @param  String  $name         Name
     * @param  Array   $sensorIds    Liste mit Sensoren
     * @param  Float   $temperature  Temperatur als Grenzwert
     * @param  Boolean $enabled      Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editTemperatureLowerThanCondition($id, $name = null, array $sensorIds = null, $temperature = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'sensors' => ($sensorIds !== null ? implode(',', $sensorIds) : null),
            'temperature'=> $temperature
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine Dateibedingung
     *
     * @param  String  $name         Name
     * @param  String  $path         Pfad zur Datei
     * @param  Boolean $invert       Invertiert
     * @param  Integer $wait         Wartezeit
     * @param  Boolean $delete       loeschen
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function addFileExistsCondition($name, $path, $invert, $wait, $delete, $enabled) {

        //Daten vorbereiten
        $data = array(
            'path' => $path,
            'invert'=> ($invert == true ? 1 : 0),
            'wait'=> $wait,
            'delete'=> ($delete == true ? 1 : 0)
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\FileExistsCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Dateibedingung
     *
     * @param  Integer $id           ID
     * @param  String  $name         Name
     * @param  String  $path         Pfad zur Datei
     * @param  Boolean $invert       Invertiert
     * @param  Integer $wait         Wartezeit
     * @param  Boolean $delete       loeschen
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function editFileExistsCondition($id, $name = null, $path = null, $invert = null, $wait = null, $delete = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'path' => $path,
            'invert'=> ($invert === null ? null : ($invert == true ? 1 : 0)),
            'wait'=> $wait,
            'delete'=> ($delete === null ? null : ($delete == true ? 1 : 0))
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine Eingangsbedingung
     *
     * @param  String  $name         Name
     * @param  Array   $inputs       Eingaenge
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function addInputHighCondition($name,array $inputs, $enabled) {

        //Daten vorbereiten
        $data = array(
            'inputs' => $inputs
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\InputHighCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Eingangsbedingung
     *
     * @param  Integer $id           ID
     * @param  Array   $inputs       Eingaenge
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function editInputHighCondition($id, $name = null, $inputs = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'inputs' => $inputs
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine Eingangsbedingung
     *
     * @param  String  $name         Name
     * @param  Array   $inputs       Eingaenge
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function addInputLowCondition($name,array $inputs, $enabled) {

        //Daten vorbereiten
        $data = array(
            'inputs' => $inputs
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\InputLowCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Eingangsbedingung
     *
     * @param  Integer $id           ID
     * @param  Array   $inputs       Eingaenge
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function editInputLowCondition($id, $name = null, $inputs = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'inputs' => $inputs
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine Eingangsbedingung
     *
     * @param  String  $name         Name
     * @param  Integer $holidays     Feiertage
     * @param  Boolean $enabled      Aktiv
     * @param  Boolean $invert       Invertiert
     * @return bool
     * @throws \Exception
     */
    public function addHolidaysCondition($name, $holidays, $enabled, $invert) {

        //Daten vorbereiten
        $data = array(
            'holidays' => $holidays,
            'invert' => $invert
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\HolidaysCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet eine Eingangsbedingung
     *
     * @param  Integer $id           ID
     * @param  Integer $holidays     Feiertage
     * @param  Boolean $enabled      Aktiv
     * @param  Boolean $invert       Invertiert
     * @return bool
     * @throws \Exception
     */
    public function editHolidaysCondition($id, $name = null, $holidays = null, $enabled = null, $invert = null) {

        //Daten vorbereiten
        $data = array(
            'holidays' => $holidays,
            'invert' => $invert
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine neue Bedingung die nur im ersten Lauf des Shedulers zutrifft
     *
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addFirstLoopCondition($name, $enabled) {

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\FirstLoopCondition', $name, $enabled);
    }

    /**
     * bearbeitet eine Bedingung die nur im ersten Lauf des Shedulers zutrifft
     *
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editFirstLoopCondition($id, $name = null, $enabled = null) {

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled);
    }

    /**
     * erstellt eine neue Bedingung die nur zu geraden Kalenderwochen zutrifft
     *
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addJustCalendarWeekCondition($name, $enabled) {

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\JustCalendarWeekCondition', $name, $enabled);
    }

    /**
     * bearbeitet eine Bedingung die nur zu geraden Kalenderwochen zutrifft
     *
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editJustCalendarWeekCondition($id, $name = null, $enabled = null) {

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled);
    }

    /**
     * erstellt eine neue Bedingung die nur zu ungeraden Kalenderwochen zutrifft
     *
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function addOddCalendarWeekCondition($name, $enabled) {

        //Datensatz erstellen
        return $this->addCondition('\SHC\Condition\Conditions\OddCalendarWeekCondition', $name, $enabled);
    }

    /**
     * bearbeitet eine Bedingung die nur zu ungeraden Kalenderwochen zutrifft
     *
     * @param  Integer $id      ID
     * @param  String  $name    Name
     * @param  Boolean $enabled Aktiv
     * @return Boolean
     * @throws \Exception
     */
    public function editOddCalendarWeekCondition($id, $name = null, $enabled = null) {

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled);
    }

    /**
     * erstellt eine Bedingung die abhängig vom Status von schaltbaren Elementen zutrifft
     *
     * @param  String  $name         Name
     * @param  array   $switchables  Liste mit den Schaltbaren Elementen
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function addSwitchableStateHighCondition($name, array $switchables, $enabled) {

        //Daten vorbereiten
        $data = array(
            'switchables' => implode(',', $switchables)
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\SwitchableStateHighCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet Bedingung die abhängig vom Status von schaltbaren Elementen zutrifft
     *
     * @param  Integer $id           ID
     * @param  array   $switchables  Liste mit den Schaltbaren Elementen
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function editSwitchableStateHighCondition($id, $name = null, array $switchables = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'switchables' => implode(',', $switchables)
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * erstellt eine Bedingung die abhängig vom Status von schaltbaren Elementen zutrifft
     *
     * @param  String  $name         Name
     * @param  array   $switchables  Liste mit den Schaltbaren Elementen
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function addSwitchableStateLowCondition($name, array $switchables, $enabled) {

        //Daten vorbereiten
        $data = array(
            'switchables' => implode(',', $switchables)
        );

        //Datensatz bearbeiten
        return $this->addCondition('\SHC\Condition\Conditions\SwitchableStateLowCondition', $name, $enabled, $data);
    }

    /**
     * bearbeitet Bedingung die abhängig vom Status von schaltbaren Elementen zutrifft
     *
     * @param  Integer $id           ID
     * @param  array   $switchables  Liste mit den Schaltbaren Elementen
     * @param  Boolean $enabled      Aktiv
     * @return bool
     * @throws \Exception
     */
    public function editSwitchableStateLowCondition($id, $name = null, array $switchables = null, $enabled = null) {

        //Daten vorbereiten
        $data = array(
            'switchables' => implode(',', $switchables)
        );

        //Datensatz bearbeiten
        return $this->editCondition($id, $name, $enabled, $data);
    }

    /**
     * loascht eine Bedingung
     * 
     * @param  Integer $id ID
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function removeCondition($id) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            if($db->hDel(self::$tableName, $id)) {

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
     * gibt den Bedingungs Editor zurueck
     * 
     * @return \SHC\Condition\ConditionEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new ConditionEditor();
        }
        return self::$instance;
    }

}
