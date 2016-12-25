<?php

namespace RWF\Form;

//Imports
use RWF\Core\RWF;
use RWF\Util\StringUtils;
use RWF\Util\Message;

/**
 * Standard HTML Formular
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DefaultHtmlForm extends AbstractForm {

    /**
     * @param String $description Formularbeschreibung
     */
    public function __construct($description = '') {

        RWF::getLanguage()->loadModul('form');
        $this->message = new Message(Message::ERROR, RWF::getLanguage()->getPlain('form.message.typedWrong'));
        $this->setDescription($description);
        $this->addClass('rfw-ui-form-default');
    }

    /**
     * gibt die Formularbeschreibung als HTML Fragment zurueck
     * 
     * @return String
     */
    public function fetchDescription() {

        if($this->getDescription() != '') {

            return '<div class="rwf-ui-form-description-container"><div class="rwf-ui-form-description-text">' . StringUtils::encodeHTML($this->getDescription()) . '</div></div>' . "\n";
        }
        return '';
    }

    /**
     * gibt alle Meldungen als HTML Fragment zurueck
     * 
     * @return String
     */
    public function fetchMessages() {

        if (count($this->invalidElements)) {

            return $this->message->fetchHtml();
        }
        return '';
    }

    /**
     * gibt das Meldungsobjekt zurueck
     * 
     * @return \RWF\Util\Message
     */
    public function getMessage() {

        return $this->message;
    }

    /**
     * gibt das Start Tag als String zurueck
     * 
     * @return String
     */
    public function fetchStartTag() {

        $html = '<form action="' . StringUtils::encodeHTML($this->action) . '" method="post" accept-charset="' . StringUtils::encodeHTML($this->encodeing) . '" ';
        if (count($this->ids) > 0) {

            $html .= 'id="' . StringUtils::encodeHTML(implode(' ', $this->ids)) . '" ';
        }
        if (count($this->classes) > 0) {

            $html .= 'class="' . StringUtils::encodeHTML(implode(' ', $this->classes)) . '" ';
        }
        $html .= '>';

        return $html . "\n";
    }

    /**
     * gibt das End Tag als String zurueck
     * 
     * @return String
     */
    public function fetchEndTag() {

        return "</form>\n";
    }

    /**
     * gibt ein HTML Fragment mit dem ganzen Formular zurueck
     * 
     * @return String
     */
    public function showForm() {

        $html = $this->fetchStartTag();
        $html .= $this->fetchDescription();
        $html .= $this->fetchMessages();
        $html .= $this->fetchAllElements();
        $html .= $this->fetchEndTag();
        return $html;
    }
}
