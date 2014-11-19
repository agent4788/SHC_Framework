<?php

namespace RWF\Template\Plugin;

//Imports
use RWF\Template\TemplateFunction;
use RWF\Template\Template;
use RWF\Core\RWF;

/**
 * gibt eine Sprachvariable aus
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LangFunction implements TemplateFunction {

    /**
     * Template Funktion
     * 
     * @param  Array                  $value Werte
     * @param  \RWF\Template\Template $tpl   Template Objekt
     * @return String
     */
    public static function execute(array $value, Template $tpl) {

        if (isset($value[1])) {

            return RWF::getLanguage()->get($value[0], $value[1]);
        }

        return RWF::getLanguage()->get($value[0]);
    }

}
