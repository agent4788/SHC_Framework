<?php

namespace RWF\Template;

/**
 * Template Funktion (wird zur Laufzeit aufgerufen um die aktuellen Daten zu bearbeiten)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface TemplateFunction {

    /**
     * Template Funktion
     * 
     * @param  Array                  $value Werte
     * @param  \RWF\Template\Template $tpl   Template Objekt
     * @return String
     */
    public static function execute(array $value, Template $tpl);
}
