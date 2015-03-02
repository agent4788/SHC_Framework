<?php

namespace SHC\View\Room;

//Imports
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Sensor\Sensor;

/**
 * Raum Anzeige
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ViewHelper {

    /**
     * Raum ID
     * 
     * @var Integer 
     */
    protected $roomId = 0;

    /**
     * Liste mit allen enthaltenen Elementen
     * 
     * @var Array 
     */
    protected $elements = array();
    
    /**
     * setzt die Raum ID
     * 
     * @param  Integer $roomId ID
     * @return \SHC\View\Room\ViewHelper
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
     * fuegt ein Lesbares Element hinzu
     * 
     * @param  \SHC\Switchable\Readable $readable
     * @return \SHC\View\Room\ViewHelper
     */
    public function addReadable(Readable $readable) {

        $this->elements[$readable->getOrderId($this->roomId)] = $readable;
        return $this;
    }
    
    /**
     * entfernt ein lesbares Element
     * 
     * @param  \SHC\Switchable\Readable $readable
     * @return \SHC\View\Room\ViewHelper
     */
    public function removeReadable(Readable $readable) {
        
        if(isset($this->elements[$readable->getOrderId($this->roomId)])) {
            
            unset($this->elements[$readable->getOrderId($this->roomId)]);
        }
        return $this;
    }
    
    /**
     * fuegt ein schaltbares Element hinzu
     * 
     * @param  \SHC\Switchable\Switchable $switchable
     * @return \SHC\View\Room\ViewHelper
     */
    public function addSwitchable(Switchable $switchable) {

        $this->elements[$switchable->getOrderId($this->roomId)] = $switchable;
        return $this;
    }
    
    /**
     * entfernt ein lesbares Element
     * 
     * @param  \SHC\Switchable\Switchable $switchable
     * @return \SHC\View\Room\ViewHelperBox
     */
    public function removeSwitchable(Switchable $switchable) {

        if(isset($this->elements[$switchable->getOrderId($this->roomId)])) {
            
            unset($this->elements[$switchable->getOrderId($this->roomId)]);
        }
        return $this;
    }

    /**
     * fuegt einen Sensor hinzu
     * 
     * @param  \SHC\Sensor\Sensor $sensor
     * @return \SHC\View\Room\ViewHelper
     */
    public function addSensor(Sensor $sensor) {

        $this->elements[$sensor->getOrderId($this->roomId)] = $sensor;
        return $this;
    }
    
    /**
     * entfernt einen Sensor
     * 
     * @param  \SHC\Sensor\Sensor $sensor
     * @return \SHC\View\Room\ViewHelper
     */
    public function removeSensor(Sensor $sensor) {
        
        if(isset($this->elements[$sensor->getOrderId($this->roomId)])) {
            
            unset($this->elements[$sensor->getOrderId($this->roomId)]);
        }
        return $this;
    }
    
    /**
     * fuegt eine Box hinzu
     * 
     * @param  \SHC\View\Room\ViewHelperBox $box
     * @return \SHC\View\Room\ViewHelper
     */
    public function addBox(ViewHelperBox $box) {

        $this->elements[$box->getBoxOrderId()] = $box;
        return $this;
    }
    
    /**
     * entfernt eine Box
     * 
     * @param  \SHC\View\Room\ViewHelperBox $box
     * @return \SHC\View\Room\ViewHelper
     */
    public function renmoveBox(ViewHelperBox $box) {
        
        if(isset($this->elements[$box->getBoxOrderId()])) {
            
            unset($this->elements[$box->getBoxOrderId()]);
        }
        return $this;
    }

    /**
     * loescht alle Elemente
     *
     * @return \SHC\View\Room\ViewHelper
     */
    public function removeAll() {
        
        $this->elements = array();
        return $this;
    }
    
    /**
     * gibt eine Sortierte Liste mit den Elementen zurueck
     *
     * @return Array
     */
    public function listElementsOrdered() {

        $elements = $this->elements;
        ksort($elements, SORT_NUMERIC);
        return $elements;
    }
    
    /**
     * gibt alle Elemente als HTML Fragment zurueck
     * 
     * @return String
     */
    public function showAll() {
        
        $html = '';
        foreach ($this->listElementsOrdered() as $element) {
            
            if($element instanceof ViewHelperBox) {
                
                $html .= $element->showAll();
            } elseif($element instanceof Readable) {
                
                $html .= ReadableViewHelper::showReadable($this->roomId, $element);
            } elseif($element instanceof Switchable) {
                
                $html .= SwitchableViewHelper::showSwitchable($this->roomId, $element);
            } elseif($element instanceof Sensor) {
                
                $html .= SensorViewHelper::showSensor($this->roomId, $element);
            }
        }
        return $html;
    }
    
    /**
     * gibt das Element als HTML Fragment zurueck
     * 
     * @param  Integer $orderId Sortierungs ID
     * @return String
     */
    public function showByOrderId($orderId) {
        
        if(isset($this->elements[$orderId])) {
            
            $element = $this->elements[$orderId];
            if($element instanceof ViewHelperBox) {
                
                return $element->showAll();
            } elseif($element instanceof Readable) {
                
                return ReadableViewHelper::showReadable($this->roomId, $element);
            } elseif($element instanceof Switchable) {
                
                return SwitchableViewHelper::showSwitchable($this->roomId, $element);
            } elseif($element instanceof Sensor) {
                
                return SensorViewHelper::showSensor($this->roomId, $element);
            }
        }
        return '';
    }

}