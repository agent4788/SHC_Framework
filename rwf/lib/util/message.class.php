<?php

namespace RWF\Util;

/**
 * Meldungsobjekt
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Message {
    
    /**
     * Fehler
     * 
     * @var int
     */
    const ERROR = 1;

    /**
     * Warnung
     * 
     * @var int
     */
    const WARNING = 2;

    /**
     * Meldung
     * 
     * @var int
     */
    const MESSAGE = 4;

    /**
     * Erfolgreich
     * 
     * @var int
     */
    const SUCCESSFULLY = 8;

    /**
     * Typ der Meldung
     * 
     * @var int
     */
    protected $type = 0;

    /**
     * Meldung
     * 
     * @var string
     */
    protected $message = '';

    /**
     * Informationen zur Meldung
     * 
     * @var array
     */
    protected $subMessages = array();

    /**
     * Meldungen
     * 
     * @param int    $type
     * @param string $message
     * @param array  $submassages
     */
    public function __construct($type = 0, $message = '', $submassages = array()) {

        $this->type = $type;
        $this->message = $message;
        $this->subMessages = $submassages;
    }

    /**
     * gibt den Typ der Meldung zurueck
     * 
     * @return int
     */
    public function getType() {

        return $this->type;
    }

    /**
     * setzt den Typ der Meldung
     * 
     * @param int $type
     */
    public function setType($type) {

        $this->type = $type;
    }

    /**
     * gibt den Meldetext zurueck
     * 
     * @return string
     */
    public function getMessage() {

        return $this->message;
    }

    /**
     * setzt den Meldetext
     * 
     * @param string $message
     */
    public function setMessage($message) {

        $this->message = $message;
    }

    /**
     * gibt die Zusaetzlichen Meldungen zurueck
     * 
     * @return array
     */
    public function getSubMessages() {

        return $this->subMessages;
    }

    /**
     * fuegt eine neue Zusaetzliche Meldung hinzu
     * 
     * @param string $message
     */
    public function addSubMessage($message) {

        $this->subMessages[] = $message;
    }

    /**
     * fuegt eine Liste neue Zusaetzlicher Meldung hinzu
     * 
     * @param Array $message
     */
    public function addSubMessages(array $messages) {

        $this->subMessages = array_merge($this->subMessages, $messages);
    }

    /**
     * loescht alle Zusaetzlichen Meldungen
     */
    public function removeSubMessages() {

        $this->subMessages = array();
    }

    /**
     * gibt das Meldungsobjekt als HTML Fragment zurueck
     * 
     * @return String
     */
    public function fetchHtml() {

        $type = 'rwf-ui-message-info';
        switch ($this->getType()) {

            case self::ERROR;

                $type = 'rwf-ui-message-error';
                break;
            case self::MESSAGE;

                $type = 'rwf-ui-message-info';
                break;
            case self::SUCCESSFULLY;

                $type = 'rwf-ui-message-successfully';
                break;
            case self::WARNING;

                $type = 'rwf-ui-message-warning';
                break;
        }

        if ($this->getMessage() != '') {
            
            $html = '<div class="rwf-ui-message">' . "\n";
            $html .= '<div class="' . $type . '">' . "\n";
            $html .= '<div class="rwf-ui-message-icon"></div>' . "\n";
            $html .= '<div class="rwf-ui-message">' . $this->getMessage() . '</div>' . "\n";
            if(count($this->getSubMessages())) {
                
                $html .= '<ul class="rwf-ui-message-sub">' . "\n";
                
                foreach($this->getSubMessages() as $subMessage) {
                    
                    $html .= '<li class="rwf-ui-message-sub-element">'. $subMessage . "</li>\n";
                }
                $html .= '</ul>' . "\n";
            }
            $html .= '</div>' . "\n";
            $html .= '</div>' . "\n";

            return $html;
        }
        
        return '';
    }
}
