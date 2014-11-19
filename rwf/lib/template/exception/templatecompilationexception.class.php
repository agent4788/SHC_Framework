<?php

namespace RWF\Template\Exception;

/**
 * Template Compiler Ausnahmen (beim Compilieren)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TemplateCompilationException extends \Exception {

    /**
     * Datei
     * 
     * @var String
     */
    protected $template = '';

    /**
     * Zeile
     * 
     * @var Integer
     */
    protected $templateLine = 0;

    /**
     * @param String  $message Meldung
     * @param String  $file    Datei
     * @param Integer $line    Zeile
     */
    public function __construct($message, $file = '', $line = 0) {

        $this->message = 'Template compilierung Fehlgeschlagen: ' . $message;
        $this->template = $file;
        $this->templateLine = $line;
        $this->code = 1140;

        //Meldunsgzusatz
        if ($this->template != '' && $this->templateLine > 0) {

            $this->message .= ' in Datei "' . $this->template . '" in Zeile ' . $this->templateLine;
        } elseif ($this->template != '' && $this->templateLine == 0) {

            $this->message .= ' in Datei "' . $this->template . '"';
        }
    }

    /**
     * gibt den Namen der Templatedatei zurueck
     * 
     * @return String
     */
    public function getTemplate() {

        return $this->template;
    }

    /**
     * gibt die Zeile im Template zurueck
     * 
     * @return Integer
     */
    public function getTemplateLine() {

        return $this->templateLine;
    }

}
