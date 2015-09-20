<?php

namespace RWF\Util;

//Imports
use RWF\Core\RWF;

/**
 * Zeichenketten Funktionen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class String {

    /**
     * generiert eine zufaellige Zeichenkette
     *
     * @param  Integer $length Laenge
     * @return String          Zeichenkette
     */
    public static function randomStr($length = 10) {

        $set = array("a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J",
            "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r", "R", "s", "S", "t", "T",
            "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z", "1", "2", "3", "4", "5", "6", "7", "8",
            "9");
        $str = '';

        for ($i = 1; $i <= $length; ++$i) {

            $ch = mt_rand(0, count($set) - 1);
            $str .= $set[$ch];
        }

        return $str;
    }

    /**
     * formatiert eine Zahl zur anzeige
     * 
     * @param  Float   $value    Wert
     * @param  Integer $decimals Dezimalstellen
     * @return String
     */
    public static function formatFloat($value, $decimals = 2) {

        return number_format($value, $decimals, RWF::getLanguage()->getDecimalSeparator(), RWF::getLanguage()->getTousandsSeparator());
    }

    /**
     * formatiert eine Zahl zur anzeige
     *
     * @param  Integer $value Wert
     * @return String
     */
    public static function formatInteger($value) {

        return number_format($value, 0, RWF::getLanguage()->getDecimalSeparator(), RWF::getLanguage()->getTousandsSeparator());
    }

    /**
     * prueft eine MD5 Checksumme auf gueltigkeit
     *
     * @param  String  $str
     * @return boolean
     */
    public static function checkMD5($str) {

        if (preg_match('#^[0-9a-f]{32}$#i', $str)) {
            return true;
        }
        return false;
    }

    /**
     * erzeugt eine eindeutige ID
     *
     * @return String MD5
     */
    public static function randomId() {

        return md5(microtime() . uniqid(mt_rand(), true));
    }

    /**
     * konvertiert Dos zu Unix Zeilenumbruechen
     *
     * @param  String $str
     * @return String
     */
    public static function convertToUnixLines($str) {

        return preg_replace("%(\r\n)|(\r)%", "\n", $str);
    }

    /**
     * entfernt Leerzeichen am Anfang und Ende des Strings
     *
     * @param  String $str
     * @return String
     */
    public static function trim($str) {

        return trim($str);
    }

    /**
     * Codiert HTML Sonderzeichen
     *
     * @param  String $str
     * @return String
     */
    public static function encodeHTML($str) {

        return @htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Decodiert HTML Sonderzeichen
     *
     * @param  String $str
     * @return String
     */
    public static function decodeHTML($str) {

        $str = str_ireplace('&nbsp;', ' ', $str);
        return @html_entity_decode($str, '"', 'UTF-8');
    }

    /**
     * Formatiert eine Zahl zur ausgabe
     *
     * @param  number $number
     * @return String
     */
    public static function numberFormat($number) {

        if (is_Integer($number)) {
            return self::formatInteger($number);
        } elseif (is_float($number)) {
            return self::formatFloat($number);
        }

        if (floatval($number) - (float) intval($number)) {
            return self::formatFloat($number);
        } else {
            return self::formatInteger($number);
        }
    }

    /**
     * Sortiert ein Array mit Strings
     *
     * @param  array $str
     */
    public static function sort(&$str) {

        return asort($str, SORT_LOCALE_STRING);
    }

    /**
     * gibt die anzahl der Zeichen eines Strings zurueck
     *
     * @param  String $str
     * @return Integer
     */
    public static function length($str) {

        if (MULTIBYTE_STRING) {
            return mb_strlen($str);
        }
        return strlen($str);
    }

    /**
     * gibt die Position der ersten Fundstelle zurueck
     *
     * @param String $hayStack SuchString
     * @param String $needle   gesuchter String
     * @param Integer    $offset   start Position
     */
    public static function indexOf($hayStack, $needle, $offset = 0) {

        if (MULTIBYTE_STRING) {
            return mb_strpos($hayStack, $needle, $offset);
        }
        return strpos($hayStack, $needle, $offset);
    }

    /**
     * gibt die Position der ersten Fundstelle zurueck, ohne beachtung von Groß- und Kleinschreibung
     *
     * @param String $hayStack SuchString
     * @param String $needle   gesuchter String
     * @param Integer    $offset   start Position
     */
    public static function indexOfIgnoreCase($hayStack, $needle, $offset = 0) {

        if (MULTIBYTE_STRING) {
            return mb_strpos(self::toLower($hayStack), self::toLower($needle), $offset);
        } else {
            return stripos($hayStack, $needle, $offset);
        }
    }

    /**
     * gibt einen TeilString zurueck
     *
     * @param  String $str
     * @param  Integer    $start
     * @param  Integer    $length
     * @return String
     */
    public static function subString($str, $start, $length = null) {

        if (MULTIBYTE_STRING) {
            if ($length !== null) {
                return mb_substr($str, $start, $length);
            }
            return mb_substr($str, $start);
        }
        if ($length !== null) {
            return substr($str, $start, $length);
        }
        return substr($str, $start);
    }

    /**
     * konvertirt alle Zeichen im String zu Kleinbuchstaben
     *
     * @param  String $str
     * @return String
     */
    public static function toLower($str) {

        if (MULTIBYTE_STRING) {
            return mb_strtolower($str);
        }
        return strtolower($str);
    }

    /**
     * konvertiert alle Zeichen im String zu Großbuchstaben
     *
     * @param  String $str
     * @return String
     */
    public static function toUpper($str) {

        if (MULTIBYTE_STRING) {
            return mb_strtoupper($str);
        }
        return strtoupper($str);
    }

    /**
     * zaehlt wie oft der gesuchte String im SuchString vorkommt
     *
     * @param  String $hayStack
     * @param  String $needle
     * @return Integer
     */
    public static function countSubString($hayStack, $needle) {

        if (MULTIBYTE_STRING) {
            return mb_substr_count($hayStack, $needle);
        }
        return substr_count($hayStack, $needle);
    }

    /**
     * erstes Zeichen in Großbuchstaben umwandeln
     *
     * @param  String $str
     * @return String
     */
    public static function firstCharToUpper($str) {

        if (MULTIBYTE_STRING) {
            return self::toUpper(self::subString($str, 0, 1)) . self::subString($str, 1);
        }
        return ucfirst($str);
    }

    /**
     * erstes Zeichen in Kleinbuchstaben umwandeln
     *
     * @param  String $str
     * @return String
     */
    public static function firstCharToLower($str) {

        if (MULTIBYTE_STRING) {
            return self::toLower(self::subString($str, 0, 1)) . self::subString($str, 1);
        }
        return ucfirst($str);
    }

    /**
     * Woerter in Großbuchstaben convertieren
     *
     * @param  String $str
     * @return String
     */
    public static function wordsToUpper($str) {

        if (MULTIBYTE_STRING) {
            return mb_convert_case($str, MB_CASE_TITLE);
        }
        return ucwords($str);
    }

    /**
     * Suchen und Ersetzen
     *
     * @param  String $search
     * @param  String $replace
     * @param  String $subject
     * @param  Integer $count
     * @return String
     */
    public static function replace($search, $replace, $subject, &$count = 0) {

        return str_replace($search, $replace, $subject, $count);
    }

    /**
     * Suchen und Ersetzen
     *
     * @param  String $search
     * @param  String $replace
     * @param  String $subject
     * @param  Integer $count
     * @return String
     */
    public static function replaceIgnoreCase($search, $replace, $subject, &$count = 0) {

        if (MULTIBYTE_STRING) {
            $startPos = self::indexOf(self::toLower($subject), self::toLower($search));
            if ($startPos === false)
                return $subject;
            else {
                $endPos = $startPos + self::length($search);
                $count++;
                return self::subString($subject, 0, $startPos) . $replace . self::replaceIgnoreCase($search, $replace, self::subString($subject, $endPos), $count);
            }
        }
        return str_ireplace($search, $replace, $subject, $count);
    }

    /**
     * konvertiert einen String in einen bestimmten Zeichensatz
     *
     * @param  String $inCharset
     * @param  String $outCharset
     * @param  String $String
     * @return String
     */
    public static function convertEncoding($inCharset, $outCharset, $str) {

        if ($inCharset == 'ISO-8859-1' && $outCharset == 'UTF-8') {
            return utf8_encode($str);
        }
        if ($inCharset == 'UTF-8' && $outCharset == 'ISO-8859-1') {
            return utf8_decode($str);
        }

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, $outCharset, $inCharset);
        }
        return $str;
    }

    /**
     * HTML Tags aus dem String entfernen
     *
     * @param  String $str
     * @return String
     */
    public static function stripHTML($str) {

        return preg_replace('#</?[a-z]+[1-6]?(?:\s*[a-z]+\s*=\s*(?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|[^\s>]))*\s*/?>#i', '', $str);
    }

    /**
     * Ueberprueft die laenge einer Zeichenkette
     *
     * @param  String $str
     * @param  Integer    $min
     * @param  Integer    $max
     * @return boolean
     */
    public static function checkLength($str, $min = 0, $max = 0) {

        if ($min > 0 && $max == 0) {
            $legth = self::length($str);
            if ($legth >= $min) {
                return true;
            } else {
                return false;
            }
        } elseif ($min == 0 && $max > 0) {
            $legth = self::length($str);
            if ($legth <= $max) {
                return true;
            } else {
                return false;
            }
        } elseif ($min > 0 && $max > 0) {
            $legth = self::length($str);
            if ($legth >= $min && $legth <= $max) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * git eine Variablendefinition als String zurueck
     *
     * @param  mixed   $array
     * @param  boolean $html
     * @return String
     */
    public static function varDump($array, $html = false) {

        ob_start();
        var_dump($array);
        $String = ob_get_contents();
        ob_end_clean();
        $String = String::convertEncoding('ISO-8859-1', 'UTF-8', $String);

        if ($html === true) {
            $String = String::encodeHTML($String);
            $String = String::replace(' ', '&nbsp;', $String);
            $String = nl2br($String);
        }
        return $String;
    }

    /**
     * verschlüsselt eine Zeichenkette
     * 
     * @param  String  $str Zeichenkette
     * @param  Integer $key Schluessel
     * @return String
     */
    public static function encrypt($str, $key) {

        //eigenen Algorythmus verwenden
        mt_srand($key);
        $out = array();
        for ($x = 0, $l = strlen($str); $x < $l; $x++) {
            $out[$x] = (ord($str[$x]) * 3) + mt_rand(350, 16000);
        }

        mt_srand();
        return implode('-', $out);
    }

    /**
     * entschlüsselt eine Zeichenkette
     * 
     * @param  String  $str Zeichenkette
     * @param  Integer $key Schluessel
     * @return String
     */
    public static function decrypt($str, $key) {

        //eigenen Algorythmus verwenden
        mt_srand($key);
        $blocks = explode('-', $str);
        $out = array();
        foreach ($blocks as $block) {
            $ord = (intval($block) - mt_rand(350, 16000)) / 3;
            $out[] = chr($ord);
        }

        mt_srand();
        return implode('', $out);
    }

}
