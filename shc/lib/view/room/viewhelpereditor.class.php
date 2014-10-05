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
        foreach($xml->box as $box) {
            
            $boxObject = new ViewHelperBox();
            $boxObject->setBoxId((int) $box->id);
            $boxObject->setName((string) $box->name);
            $boxObject->setBoxOrderId((int) $box->orderId);
            $boxObject->setRoomId((int) $box->roomId);
            //Elemente hinzufuegen
            foreach($box->elements->element as $element) {
                
                $attr = $element->attributes();
                if((int) $attr->type == self::TYPE_READABLE) {
                    
                    $boxObject->addReadable(SwitchableEditor::getInstance()->getElementById((int) $attr->id));
                } elseif((int) $attr->type == self::TYPE_SWITCHABLE) {
                    
                    $boxObject->addSwitchable(SwitchableEditor::getInstance()->getElementById((int) $attr->id));
                } elseif((int) $attr->type == self::TYPE_SENSOR) {
                    
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
        foreach($elements as $element) {
            
            /* @var $element \SHC\Switchable\Element */
            if($element->getRoom()->getId() == $roomId) {
                
                if($element instanceof Readable) {
                    
                    $viewHelper->addReadable($element);
                } elseif($element instanceof Switchable) {
                    
                    $viewHelper->addSwitchable($element);
                }
            }
        }
        
        //Sensoren hinzufuegen
        $sensors = SensorPointEditor::getInstance()->listSensors(SensorPointEditor::SORT_NOTHING);
        foreach($sensors as $sensor) {
            
            /* @var $sensor \SHC\Sensor\Sensor */
            if($sensor->getRoom()->getId() == $roomId) {
                
                $viewHelper->addSensor($sensor);
            }
        }
        
        //Boxen Hinzufuegen
        foreach($this->boxes as $box) {
            
            if($box->getRoomId() == $roomId) {
                
                $viewHelper->addBox($box);
                
                //Sensoren die den Boxen angehoeren aus dem normalen Bereich wieder entfernen
                foreach($box->listElementsOrdered() as $element) {
                    
                    if($element instanceof Readable) {
                        
                        $viewHelper->removeReadable($element);
                    } elseif($element instanceof Switchable) {
                        
                        $viewHelper->removeSwitchable($element);
                    } elseif($element instanceof \SHC\Sensor\Sensor) {
                        
                        $viewHelper->removeSensor($element);
                    }
                }
            }
        }
        
        return $viewHelper;
    }
    
    public function listBoxes($orderBy = 'orderId') {
        
        
    }
    
    public function isBoxNameAvailable($name) {
        
    }
    
    public function editBoxOrder(array $order) {
        
    }
    
    public function getNextOrderId() {
        
    }
    
    public function addBox($name, $roomId, $orderId) {
        
    }
    
    public function editBox($id, $name, $roomId, $orderId) {
        
    }
    
    public function removeBox($id) {
        
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
