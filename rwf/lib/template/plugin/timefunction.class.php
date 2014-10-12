<?php

namespace RWF\Template\Plugin;

//Imports
use RWF\Date\DateTime;
use RWF\Date\LanguageDateTime;
use RWF\Template\TemplateFunction;
use RWF\Template\Template;


/**
 * gibt eine Formatierte Zeit zurueck
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TimeFunction implements TemplateFunction {

    /**
     * Template Funktion
     *
     * @param  Array                  $value Werte
     * @param  \RWF\Template\Template $tpl   Template Objekt
     * @return String
     */
    public static function execute(array $value, Template $tpl) {

        //Zeitobjekt vorbereitenz
        if($value[0] instanceof DateTime) {

            $time = LanguageDateTime::createFromDatabaseDateTime($value[0]);
        } else {

            $time = LanguageDateTime::now();
        }

        if (isset($value[1])) {

            return $time->showTime($value[1]);
        }

        return $time->showTime();
    }

}