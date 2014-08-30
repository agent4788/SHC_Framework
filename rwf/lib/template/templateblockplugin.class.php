<?php

namespace RWF\Template;

/**
 * Daten im Block koennen vom Plugin zur Laufzeit bearbeitet werden
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface TemplateBlockPlugin {

    /**
     * fuehrt die Blockfunktion aus
     * 
     * @param  Array                  $args         Argumente
     * @param  String                 $blockContent Content
     * @param  \RWF\Template\Template $tpl          Template Objekt
     * @return String
     */
    public static function execute($args, $blockContent, Template $tpl);

    /**
     * Initialisiert den Block
     * 
     * @param  Array                  $args Argumente
     * @param  \RWF\Template\Template $tpl  Template Objekt
     */
    public static function init($args, Template $tpl);
}
