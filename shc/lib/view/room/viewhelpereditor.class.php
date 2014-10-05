<?php

namespace SHC\View\Room;

//Imports
use SHC\Core\SHC;
use RWF\XML\XmlFileManager;
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

    protected function __construct() {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM_VIEW);

        //Daten EInlesen
        foreach ($xml->box as $box) {

            $boxObject = new ViewHelperBox();
            $boxObject->setBoxId((int) $box->id);
            $boxObject->setName((string) $box->name);
            $boxObject->setBoxOrderId((int) $box->orderId);
            $boxObject->setRoomId((int) $box->roomId);
            //Elemente hinzufuegen
            foreach ($box->elements->element as $element) {

                $attr = $element->attributes();
                if ((int) $attr->type == self::TYPE_READABLE) {

                    $boxObject->addReadable(SwitchableEditor::getInstance()->getElementById((int) $attr->id));
                } elseif ((int) $attr->type == self::TYPE_SWITCHABLE) {

                    $boxObject->addSwitchable(SwitchableEditor::getInstance()->getElementById((int) $attr->id));
                } elseif ((int) $attr->type == self::TYPE_SENSOR) {

                    $boxObject->addSensor(SensorPointEditor::getInstance()->getSensorById((string) $attr->id));
                }
            }
            $this->boxes[(int) $box->id] = $boxObject;
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
            if ($element->getRoom()->getId() == $roomId) {

                if ($element instanceof Readable) {

                    $viewHelper->addReadable($element);
                } elseif ($element instanceof Switchable) {

                    $viewHelper->addSwitchable($element);
                }
            }
        }

        //Sensoren hinzufuegen
        $sensors = SensorPointEditor::getInstance()->listSensors(SensorPointEditor::SORT_NOTHING);
        foreach ($sensors as $sensor) {

            /* @var $sensor \SHC\Sensor\Sensor */
            if ($sensor->getRoom()->getId() == $roomId) {

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

                        $viewHelper->removeReadable($element);
                    } elseif ($element instanceof Switchable) {

                        $viewHelper->removeSwitchable($element);
                    } elseif ($element instanceof \SHC\Sensor\Sensor) {

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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM_VIEW, true);

        //Raeume durchlaufen und deren Sortierungs ID anpassen
        foreach ($xml->box as $box) {

            if (isset($order[(int) $box->id])) {

                $box->orderId = $order[(int) $box->id];
            }
        }

        //Daten Speichern
        $xml->save();
        return true;
    }

    /**
     * gibt die naechste freie Sortierungs ID zurueck
     * 
     * @return Integer
     */
    public function getNextOrderId() {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM_VIEW, true);

        $nextOrderId = (int) $xml->nextAutoIncrementOrderId;
        $xml->nextAutoIncrementOrderId = $nextOrderId + 1;

        //Daten Speichern
        $xml->save();
        return $nextOrderId;
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

        //Ausnahme wenn Boxname schon belegt
        if (!$this->isBoxNameAvailable($name)) {

            throw new \Exception('Der Name ist schon vergeben', 1507);
        }

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM_VIEW, true);

        //Autoincrement
        $nextId = (int) $xml->nextAutoIncrementId;
        $xml->nextAutoIncrementId = $nextId + 1;

        //Datensatz erstellen
        /* @var $box \SimpleXmlElement */
        $box = $xml->addChild('box');
        $box->addChild('id', $nextId);
        $box->addChild('name', $name);
        $box->addChild('roomId', $roomId);
        $box->addChild('orderId', $orderId);
        $box->addChild('elements');

        //Daten Speichern
        $xml->save();
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM_VIEW, true);

        //Server Suchen
        foreach ($xml->box as $box) {

            /* @var $box \SimpleXmlElement */
            if ((int) $box->id == $id) {

                //Name
                if ($name !== null) {

                    //Ausnahme wenn Name der Box schon belegt
                    if (!$this->isBoxNameAvailable($name)) {

                        throw new \Exception('Der Name ist schon vergeben', 1507);
                    }

                    $box->name = $name;
                }

                //Raum
                if ($roomId !== null) {

                    $box->roomId = $roomId;
                }

                //Raum
                if ($orderId !== null) {

                    $box->orderId = $orderId;
                }

                //Daten Speichern
                $xml->save();
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM_VIEW, true);

        //Server Suchen
        foreach ($xml->box as $box) {

            /* @var $box \SimpleXmlElement */
            if ((int) $box->id == $id) {

                $element = $box->elements->addChild('element');
                $element->addAttribute('type', $type);
                $element->addAttribute('is', $elementId);

                //Daten Speichern
                $xml->save();
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM_VIEW, true);

        //Server Suchen
        for ($i = 0; $i < count($xml->box); $i++) {

            /* @var $box \SimpleXmlElement */
            if ((int) $xml->box[$i]->id == $id) {

                for ($j = 0; $j < count($box->elements->element); $j++) {

                    $attr = $box->elements->element[$j]->attributes();
                    if ($elementId == (int) $attr->id && $type == (int) $attr->type) {

                        unset($box->elements->element[$j]);

                        //Daten Speichern
                        $xml->save();
                        return true;
                    }
                }
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM_VIEW, true);

        //Server Suchen
        for ($i = 0; $i < count($xml->box); $i++) {

            /* @var $box \SimpleXmlElement */
            if ((int) $xml->box[$i]->id == $id) {

                unset($xml->box[$i]->id);

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
