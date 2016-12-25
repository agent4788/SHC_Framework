<?php

namespace RWF\Util;

/**
 * Array Hilfsfunktionen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class ArrayUtil {

    /**
     * vergleicht die Eintraege normal
     * 
     * @var Integer
     */
    const SORT_REGULAR = 0;

    /**
     * vergleicht die Eintraege numerisch
     * 
     * @var Integer
     */
    const SORT_NUMERIC = 1;

    /**
     * vergleicht die Eintraege als String
     * 
     * @var Integer
     */
    const SORT_STRING = 2;

    /**
     * vergleicht die Eintraege als String, nach der aktuellen Locale-Einstellung
     * 
     * @var Integer
     */
    const SORT_LOCALE_STRING = 5;

    /**
     * sortiert ein Array mit Strings
     * 
     * @param  Array   $array Array
     * @param  Integer $flag  Flag
     * @return Boolean
     */
    public static function sort(array &$array, $flag = self::SORT_REGULAR) {

        return asort($array, $flag);
    }

    /**
     * entfernt in allen Array Elementen Leerzeichen am Anfang und Ende der Zeichenketten
     * 
     * @param  Array   $array       Array
     * @param  Boolean $removeEmpty Leere Elemente entfernen
     * @param  Boolean $recursive   Rekursive abarbeitung
     * @return Array                Array
     */
    public static function trim($array, $removeEmpty = true, $recursive = true) {

        if (is_array($array)) {

            foreach ($array as $key => $var) {

                if (is_array($var) && $recursive == false) {

                    return $var;
                }

                $val = self::trim($var, $removeEmpty, $recursive);

                if (($removeEmpty == true && StringUtils::length($val) > 0) || ($removeEmpty == false)) {

                    $array[$key] = $val;
                } else {

                    unset($array[$key]);
                }
            }
        } else {

            $array = StringUtils::trim($array);
        }

        return $array;
    }

    /**
     * ersetzt in allen Arrayelementen HTML Sonderzeichen
     * 
     * @param  Array $array Array
     * @return Array        Array
     */
    public static function encodeHTML($array) {

        if (is_array($array)) {

            foreach ($array as $key => $var) {

                $array[$key] = self::encodeHTML($var);
            }
        }

        return StringUtils::encodeHTML($array);
    }

    /**
     * schuetzt in allen Arrayelementen Sonderzeichen mit einem Escapezeichen
     * 
     * @param  Array $array Array
     * @return Array        Array
     */
    public static function stripSlashes($array) {

        if (is_array($array)) {

            foreach ($array as $key => $var) {

                $array[$key] = self::stripSlashes($var);
            }
        } else {

            $array = StringUtils::stripSlashes($array);
        }

        return $array;
    }

    /**
     * entfernt in allen Arrayelementen vor Sonderzeichen die Escapezeichen
     * 
     * @param  Array $array Array
     * @return Array        Array
     */
    public static function addSlashes($array) {

        if (is_array($array)) {

            foreach ($array as $key => $var) {

                $array[$key] = self::addSlashes($var);
            }
        } else {

            $array = StringUtils::addSlashes($array);
        }

        return $array;
    }

    /**
     * konvertiert in allen Arrayelementen Zeilenumbrueche in das Unix Format
     * 
     * @param  Array $array Array
     * @return Array        Array
     */
    public static function convertToUnixLines($array) {

        if (is_array($array)) {

            foreach ($array as $key => $var) {

                $array[$key] = self::convertToUnixLines($var);
            }
        } else {

            $array = StringUtils::convertToUnixLines($array);
        }

        return $array;
    }

    /**
     * 
     * Enter description here ...
     * @param  Array   $array Array
     * @param  Boolean $html  HTML formatieren
     * @return Array          Array
     */
    public static function printArray($array, $html = false) {

        $string = print_r($array, true);
        $string = StringUtils::convertEncoding('ISO-8859-1', 'UTF-8', $string);

        if ($html === true) {
            $string = StringUtils::encodeHTML($string);
            $string = StringUtils::replace(' ', '&nbsp;', $string);
            $string = nl2br($string);
        }
        return $string;
    }

    /**
     * prueft ob ein Wert im Array vorhanden ist
     * 
     * @param  mixed   $needle    Gesuchtes Element
     * @param  Array   $haystack  Array in dem gesucht wird
     * @param  Boolean $strict    Typensicherheit
     * @param  Boolean $recursive Rekursiv
     * @return Boolean            True bei Erfolg
     */
    public static function inArray($needle, $haystack, $strict = false, $recursive = false) {

        if (is_array($haystack) && $recursive === true) {
            foreach ($haystack as $var) {
                if (self::inArray($needle, $var, $strict, $recursive)) {
                    return true;
                }
            }
        } elseif (is_array($haystack) && $recursive === false) {
            return in_array($needle, $haystack, $strict);
        } else {
            if ($strict === true) {
                if ($needle === $haystack) {
                    return true;
                }
            } else {
                if ($needle == $haystack) {
                    return true;
                }
            }
        }
        return false;
    }

}
