<?php

namespace SHC\Sensor;

//Imports

/**
 * Sensor Schnittstelle
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Sensor {
    
    /**
     * Der Sensor soll Angezeigt werden
     * 
     * @var Integer
     */
    const SHOW = 1;
    
    /**
     * Der Sensor soll Versteckt werden
     * 
     * @var Integer
     */
    const HIDE = 0;
    
    /**
     * setzt die Sensor ID
     * 
     * @param  String $id Sensor ID
     * @return \SHC\Sensor\Sensor
     */
    public function setId($id);
    
    /**
     * gibt die Sensor ID zurueck
     * 
     * @return String
     */
    public function getId();
    
    /**
     * setzt den Namen des Sensors
     * 
     * @param  String $name Name des Sensors
     * @return \SHC\Sensor\Sensor
     */
    public function setName($name);
    
    /**
     * gibt den Namen des Sensors zurueck
     * 
     * @return String
     */
    public function getName();

    /**
     * fuegt einen Raum hinzu
     *
     * @param  Integer $roomId Raum ID
     * @return \SHC\Sensor\Sensor
     */
    public function addRoom($roomId);

    /**
     * setzt eine Liste mit Raeumen
     *
     * @param  Array $roomId Raum IDs
     * @return \SHC\Sensor\Sensor
     */
    public function setRooms(array $rooms);

    /**
     * entfernt einen Raum
     *
     * @param  Integer $roomId Raum ID
     * @return \SHC\Sensor\Sensor
     */
    public function removeRoom($roomId);

    /**
     * prueft on das Element dem Raum mit der uebergebenen ID zugeordnet ist
     *
     * @param  Integer $roomId Raum ID
     * @return Boolean
     */
    public function isInRoom($roomId);

    /**
     * gibt eine Liste mit allen Raeumen zurueck
     *
     * @return Array
     */
    public function getRooms();

    /**
     * gibt eine Liste mit den Raumnamen zurueck
     *
     * @return Array
     */
    public function getNamedRoomList($commaSepareted = false);

    /**
     * setzt die Sortierung
     *
     * @param  Array $order Sortierung
     * @return \SHC\Sensor\Sensor
     */
    public function setOrder(array $order);

    /**
     * setzt die Sortierungs ID
     *
     * @param  Integer $roomId  Raum ID
     * @param  Integer $orderId Sortierungs ID
     * @return \SHC\Sensor\Sensor
     */
    public function setOrderId($roomId, $orderId);

    /**
     * gibt die Sortierungs ID zurueck
     *
     * @param  Integer $roomId  Raum ID
     * @return Integer
     */
    public function getOrderId($roomId);
    
    /**
     * setzt die Sichtbarkeit
     * 
     * @param  Boolean $enabled
     * @return \SHC\Sensor\Sensor
     */
    public function visibility($enabled);
    
    /**
     * gibt die Sichtbarkeit des Sensors zurueck
     * 
     * @return Integer
     */
    public function isVisible();
    
    /**
     * aktiviert/deaktiviert das aufzeichnen der Sensordaten
     * 
     * @param  Boolean $enabled aktiviert/deaktiviert
     * @return \SHC\Sensor\Sensor
     */
    public function enableDataRecording($enabled);
    
    /**
     * gibt an ob die Daten des Sensors aufgezeichnet werden sollen
     * 
     * @return Boolean
     */
    public function isDataRecordingEnabled();
    
    /**
     * gibt den Zeitstempel des letzten Sensorwertes zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getTime();
    
    /**
     * gibt die letzten 5 Sensorwerte zurueck
     * 
     * @return Array
     */
    public function getOldValues();
    
    /**
     * gibt an ob die Sensorwerte vereandert wurden
     * 
     * @return Boolean
     */
    public function isModified();

    /**
     * gibt den Typnamen zurueck
     *
     * @return string
     */
    public function getTypeName();

    /**
     * setzt das Icon welches Angezeigt werden soll
     *
     * @param  String $path Dateiname
     * @return \SHC\Sensor\Sensor
     */
    public function setIcon($path);

    /**
     * gibt den Dateinamen des Icons zurueck
     *
     * @return String
     */
    public function getIcon();
}
