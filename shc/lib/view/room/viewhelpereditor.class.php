<?php

namespace SHC\View\Room;

//Imports
use RWF\Util\String;
use SHC\Core\SHC;
use SHC\Sensor\Sensor;
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Sensor\SensorPointEditor;

/**
 * Verwaltung der Raum UI Helper
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ViewHelperEditor {

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
     * nach Sortierungs ID sortieren
     * 
     * @var String
     */
    const SORT_BY_ORDER_ID = 'orderId';

    /**
     * nicht sortieren
     * 
     * @var String
     */
    const SORT_NOTHING = 'unsorted';

    /**
     * lesbares Element
     * 
     * @var Integer
     */
    const TYPE_READABLE = 1;

    /**
     * schaltbares Element
     * 
     * @var Integer
     */
    const TYPE_SWITCHABLE = 2;

    /**
     * Sensor
     * 
     * @var Integer
     */
    const TYPE_SENSOR = 4;

    /**
     * ViewHelper Boxen
     * 
     * @var Array 
     */
    protected $boxes = array();

    /**
     * Singleton Instanz
     * 
     * @var \SHC\View\Room\ViewHelperEditor
     */
    protected static $instance = null;

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'shc:roomView';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * laedt die Daten aus den XML Dateien
     */
    public function loadData() {

        $bexes = SHC::getDatabase()->hGetAllArray(self::$tableName);
        foreach ($bexes as $box) {

            $boxRoomId = (int) $box['roomId'];
            $boxObject = new ViewHelperBox();
            $boxObject->setBoxId((int) $box['id']);
            $boxObject->setName((string) $box['name']);
            $boxObject->setBoxOrderId((int) $box['orderId']);
            $boxObject->setRoomId($boxRoomId);
            foreach ($box['elements'] as $element) {

                if ((int) $element['type'] == self::TYPE_READABLE) {

                    $element = SwitchableEditor::getInstance()->getElementById((int) $element['id']);
                    if($element instanceof Readable && $element->isInRoom($boxRoomId)) {

                        $boxObject->addReadable($element);
                    }
                } elseif ((int) $element['type'] == self::TYPE_SWITCHABLE) {

                    $element = SwitchableEditor::getInstance()->getElementById((int) $element['id']);
                    if($element instanceof Switchable && $element->isInRoom($boxRoomId)) {

                        $boxObject->addSwitchable($element);
                    }
                } elseif ((int) $element['type'] == self::TYPE_SENSOR) {

                    $element = SensorPointEditor::getInstance()->getSensorById((string) $element['id']);
                    if($element instanceof Sensor && $element->isInRoom($boxRoomId)) {

                        $boxObject->addSensor($element);
                    }
                }
            }
            $this->boxes[(int) $box['id']] = $boxObject;
        }
    }

    /**
     * gibt eine Box mit der ID zurueck
     * 
     * @param  Integer $id Box ID
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function getBoxById($id) {

        if (isset($this->boxes[$id])) {

            return $this->boxes[$id];
        }
        return null;
    }

    /**
     * gibt den View Helper fuer den Raum zurueck
     * 
     * @param  Integer $roomId Raum ID
     * @return \SHC\View\Room\ViewHelper
     */
    public function getViewHelperForRoom($roomId) {

        $viewHelper = new ViewHelper();
        $viewHelper->setRoomId($roomId);

        //lesbare/schaltbare Elemente hinzufuegen
        $elements = SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_NOTHING);
        foreach ($elements as $element) {

            /* @var $element \SHC\Switchable\Element */
            if ($element->isInRoom($roomId)) {

                if ($element instanceof Readable) {

                    /* @var $element \SHC\Switchable\Readable */
                    $viewHelper->addReadable($element);
                } elseif ($element instanceof Switchable) {

                    /* @var $element \SHC\Switchable\Switchable */
                    $viewHelper->addSwitchable($element);
                }
            }
        }

        //Sensoren hinzufuegen
        $sensors = SensorPointEditor::getInstance()->listSensors(SensorPointEditor::SORT_NOTHING);
        foreach ($sensors as $sensor) {

            /* @var $sensor \SHC\Sensor\Sensor */
            if ($sensor->isInRoom($roomId)) {

                $viewHelper->addSensor($sensor);
            }
        }

        //Boxen Hinzufuegen
        foreach ($this->boxes as $box) {

            if ($box->getRoomId() == $roomId) {

                $viewHelper->addBox($box);

                //Sensoren die den Boxen angehoeren aus dem normalen Bereich wieder entfernen
                foreach ($box->listElementsOrdered() as $element) {

                    if ($element instanceof Readable) {

                        /* @var $element \SHC\Switchable\Readable */
                        $viewHelper->removeReadable($element);
                    } elseif ($element instanceof Switchable) {

                        /* @var $element \SHC\Switchable\Switchable */
                        $viewHelper->removeSwitchable($element);
                    } elseif ($element instanceof Sensor) {

                        /* @var $element \SHC\Sensor\Sensor */
                        $viewHelper->removeSensor($element);
                    }
                }
            }
        }

        return $viewHelper;
    }

    /**
     * gibt eine Liste mir allen Boxen zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren, 
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listBoxes($orderBy = 'orderId') {

        if ($orderBy == 'id') {

            //Raeume nach ID sortieren
            $boxes = $this->boxes;
            ksort($boxes, SORT_NUMERIC);
            return $boxes;
        } elseif ($orderBy == 'orderId') {

            //Raeume nach Sortierungs ID sortieren
            $boxes = array();
            foreach ($this->boxes as $box) {

                /* @var $box \SHC\View\Room\ViewHelperBox */
                $boxes[$box->getBoxOrderId()] = $box;
            }

            ksort($boxes, SORT_NUMERIC);
            return $boxes;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $boxes = $this->boxes;

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
            usort($boxes, $orderFunction);
            return $boxes;
        }
        return $this->boxes;
    }

    /**
     * Prueft ob der Name der Box schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isBoxNameAvailable($name) {

        foreach ($this->boxes as $box) {

            /* @var $box \SHC\View\Room\ViewHelperBox */
            if (String::toLower($box->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }

    /**
     * bearbeitet die Sortierung der Boxen
     * 
     * @param  Array   $order Array mit Box ID als Index und Sortierungs ID als Wert
     * @return Boolean
     */
    public function editBoxOrder(array $order) {

        $db = SHC::getDatabase();
        foreach($order as $boxId => $orderId) {

            if(isset($this->boxes[$boxId])) {

                $boxData = $db->hGetArray(self::$tableName, $boxId);
                $boxData['orderId'] = $orderId;

                if($db->hSetArray(self::$tableName, $boxId, $boxData) != 0) {

                    return false;
                }
            }
        }
        return true;
    }

    /**
     * gibt die naechste freie Sortierungs ID zurueck
     * 
     * @return Integer
     */
    public function getNextOrderId() {

        return SHC::getDatabase()->autoIncrement(self::$tableName .'_order');
    }

    /**
     * erstellt eine neue Box
     * 
     * @param  String  $name    Name
     * @param  Integer $roomId  Raum ID
     * @param  Integer $orderId Sortierungs ID
     * @return Boolean
     * @throws \Exception
     */
    public function addBox($name, $roomId, $orderId) {

        $db = SHC::getDatabase();
        $index = $db->autoIncrement(self::$tableName);

        $newBox = array(
            'id' => $index,
            'name' => $name,
            'roomId' => $roomId,
            'orderId' => $orderId,
            'elements' => array()
        );
        if($db->hSetNxArray(self::$tableName, $index, $newBox) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet eine Box
     * 
     * @param  Integer $id      Box ID
     * @param  String  $name    Name
     * @param  Integer $roomId  Raum ID
     * @param  Integer $orderId Sortierungs ID
     * @return Boolean
     * @throws \Exception
     */
    public function editBox($id, $name = null, $roomId = null, $orderId = null) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $box = $db->hGetArray(self::$tableName, $id);

            //Name
            if ($name !== null) {

                $box['name'] = $name;
            }

            //Sortierung
            if ($orderId !== null) {

                $box['orderId'] = $orderId;
            }

            //Raum
            if ($roomId !== null) {

                if($roomId != (int) $box['roomId'] && $orderId === null) {

                    $box['roomId'] = ViewHelperEditor::getInstance()->getNextOrderId();
                }
                $box['roomId'] = $roomId;
            }

            if($db->hSetArray(self::$tableName, $id, $box) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * entfernt eine Element aus einer Box
     * 
     * @param  Integer $boxId     Box ID
     * @param  Integer $type      Typ
     * @param  Integer $elementId Element ID
     * @return Boolean
     */
    public function addToBox($boxId, $type, $elementId) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $boxId)) {

            $box = $db->hGetArray(self::$tableName, $boxId);
            foreach($box['elements'] as $index => $element) {

                if($element['id'] == $elementId && $element['type'] == $type) {

                    return true;
                }
            }

            $box['elements'][] = array(
                'type' => $type,
                'id' => $elementId
            );

            if($db->hSetArray(self::$tableName, $boxId, $box) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * entfernt eine Element aus einer Box
     * 
     * @param  Integer $boxId     Box ID
     * @param  Integer $type      Typ
     * @param  Integer $elementId Element ID
     * @return Boolean
     */
    public function removeElementFromBox($boxId, $type, $elementId) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $boxId)) {

            $box = $db->hGetArray(self::$tableName, $boxId);
            foreach($box['elements'] as $index => $element) {

                if($element['id'] == $elementId && $element['type'] == $type) {

                    unset($element[$index]);
                }
            }

            if($db->hSetArray(self::$tableName, $boxId, $box) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * loescht alle Elemente einer Box
     *
     * @param  Integer $boxId ID der Box
     * @return Boolean
     * @throws \RWF\XML\Exception\XmlException
     */
    public function removeAllElementsFromBox($boxId) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $boxId)) {

            $box = $db->hGetArray(self::$tableName, $boxId);
            $box['elements'] = array();

            if($db->hSetArray(self::$tableName, $boxId, $box) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * loescht eine Box
     * 
     * @param  Integer $id Box ID
     * @return Boolean
     */
    public function removeBox($id) {

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
     * gibt den Raum Editor zurueck
     * 
     * @return \SHC\View\Room\ViewHelperEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new ViewHelperEditor();
        }
        return self::$instance;
    }

}
