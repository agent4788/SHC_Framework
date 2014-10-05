<?php

namespace SHC\View\Room;

//Imports
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Sensor\Sensor;
use RWF\Util\String;

/**
 * Raum Anzeige Box
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ViewHelperBox {
    
    /**
     * Box ID
     * 
     * @var Integer 
     */
    protected $boxId = 0;

    /**
     * Name der Box
     * 
     * @var String 
     */
    protected $name = '';
    
    /**
     * Raum ID
     * 
     * @var Integer 
     */
    protected $roomId = 0;
    
    /**
     * Sortierungs ID der Box
     * 
     * @var Integer 
     */
    protected $boxOrderId = 0;

    /**
     * Liste mit allen enthaltenen Elementen
     * 
     * @var Array 
     */
    protected $elements = array();
    
    function __construct() {
        
    }
    
    /**
     * setzt die ID der Box
     * 
     * @param  Integer $boxId ID
     * @return \SHC\View\RoomViewHelperBox
     */
    public function setBoxId($boxId) {
        
        $this->boxId = $boxId;
        return $this;
    }
    
    /**
     * gibt die ID der Box zurueck
     * 
     * @return Integer
     */
    public function getBoxId() {
        
        return $this->boxId;
    }
    
    /**
     * setzt den Namen der Box
     * 
     * @param  String $name Name
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function setName($name) {
        
        $this->name = $name;
        return $this;
    }
    
    /**
     * gibt den Namen der Box zurueck
     * 
     * @return String
     */
    public function getName() {
        
        return $this->name;
    }

    /**
     * setzt die Raum ID
     * 
     * @param  Integer $roomId ID
     * @return \SHC\View\RoomViewHelperBox
     */
    public function setRoomId($roomId) {
        
        $this->roomId = $roomId;
        return $this;
    }
    
    /**
     * gibt die Raum ID zurueck
     * 
     * @return Integer
     */
    public function getRoomId() {
        
        return $this->roomId;
    }

    /**
     * setzt die Sortierungs ID der Box
     * 
     * @param  Integer $boxOrderId Sortierungs ID
     * @return \SHC\View\RoomViewHelperBox
     */
    public function setBoxOrderId($boxOrderId) {
        
        $this->boxOrderId = $boxOrderId;
        return $this;
    }
    
    /**
     * gibt die Sortierungs ID der Box zurueck
     * 
     * @return Integer
     */
    public function getBoxOrderId() {
        
        return $this->boxOrderId;
    }
    
    /**
     * fuegt ein Lesbares Element hinzu
     * 
     * @param  \SHC\Switchable\Readable $readable
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function addReadable(Readable $readable) {
        
        $this->elements[$readable->getOrderId()] = $readable;
        return $this;
    }
    
    /**
     * entfernt ein lesbares Element
     * 
     * @param  \SHC\Switchable\Readable $readable
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function removeReadable(Readable $readable) {
        
        $this->elements = array_diff($$this->elements, array($readable));
        return $this;
    }
    
    /**
     * fuegt ein schaltbares Element hinzu
     * 
     * @param  \SHC\Switchable\Switchable $switchable
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function addSwitchable(Switchable $switchable) {
        
        $this->elements[$switchable->getOrderId()] = $switchable;
        return $this;
    }
    
    /**
     * entfernt ein lesbares Element
     * 
     * @param  \SHC\Switchable\Switchable $switchable
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function removeSwitchable(Switchable $switchable) {
        
        $this->elements = array_diff($$this->elements, array($switchable));
        return $this;
    }

    /**
     * fuegt einen Sensor hinzu
     * 
     * @param  \SHC\Sensor\Sensor $sensor
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function addSensor(Sensor $sensor) {
        
        $this->elements[$sensor->getOrderId()] = $sensor;
        return $this;
    }
    
    /**
     * entfernt einen Sensor
     * 
     * @param  \SHC\Sensor\Sensor $sensor
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function removeSensor(Sensor $sensor) {
        
        $this->elements = array_diff($$this->elements, array($sensor));
        return $this;
    }
    
    public function removeAll() {
        
        $this->elements = array();
        return $this;
    }
    
    /**
     * gibt eine Sortierte Liste mit den Elementen zurueck
     */
    public function listElementsOrdered() {
        
        $elements = $this->elements;
        ksort($elements, SORT_NUMERIC);
        return $elements;
        
    }
    
    /**
     * gibt die Box als HTML Fragment zurueck
     * 
     * @return String
     */
    public function showAll() {
        
        $html = $this->fetchStartTag();
        foreach($this->listElementsOrdered() as $element) {
            
            if($element instanceof Readable) {
                
                $html .= ReadableViewHelper::showReadable($element);
            } elseif($element instanceof Switchable) {
                
                $html .= SwitchableViewHelper::showSwitchable($element);
            } elseif($element instanceof Sensor) {
                
                $html .= SensorViewHelper::showSensor($element);
            }
        }
        $html .= $this->fetchEndTag();
        return $html;
    }
    
    /**
     * gibt ein HTML Fragment mit dem Start Element der Box zurueck
     * 
     * @return String
     */
    protected function fetchStartTag() {
        
        $html = '<div class="shc-contentbox shc-contentbox-room ui-tabs ui-widget ui-widget-content ui-corner-all">';
        $html .= '<div class="shc-contentbox-header ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">';
        $html .= String::encodeHtml($this->getName());
        $html .= '</div>';
        $html .= '<div class="shc-contentbox-body">';
        return $html;
    }
    
    /**
     * gibt ein HTML Fragment mit dem End Element der Box zurueck
     * 
     * @return String
     */
    protected function fetchEndTag() {
        
        return '</div></div>';
    }
    
}