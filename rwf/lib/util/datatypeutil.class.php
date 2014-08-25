<?php

namespace RWF\Util;

/**
 * Datentypen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DataTypeUtil {

    /**
     * Ganzzahl
     * 
     * @var Integer
     */
    const INTEGER = 1;

    /**
     * Gleitpunkzahl
     * 
     * @var Integer
     */
    const FLOAT = 2;

    /**
     * Zeichenkette
     * 
     * @var Integer
     */
    const STRING = 4;

    /**
     * Wahrheitswert
     * 
     * @var Integer
     */
    const BOOLEAN = 8;

    /**
     * Wahrheitswert
     * 
     * @var Integer
     */
    const MD5 = 16;

    /**
     * Array
     * 
     * @var Integer
     */
    const ARRAY_STR = 32;

    /**
     * keine Datentyp bestimmung
     * 
     * @var Integer
     */
    const PLAIN = 64;

    /**
     * konvertiert den Uebergebene Wert in das gewuenschte Format und prueft dabei auf Plausibilitaet
     * 
     * @param Mixed   $value    Wert
     * @param Integer $dataType Datentyp Konstante
     */
    public static function checkAndConvert($value, $dataType) {

        switch ($dataType) {

            case self::BOOL:
                if ((int) $value === 1 || (string) $value === 'true') {
                    return true;
                } elseif ((int) $value === 0 || (string) $value === 'false') {
                    return false;
                }
                throw new Exception('Hacking versuch entdeckt', 1011);
                break;
            case self::INT:
                if ((string) $value == (int) $value) {
                    return $value;
                }
                throw new Exception('Hacking versuch entdeckt', 1011);
                break;
            case self::FLOAT:
                if ((string) $value == (float) $value) {
                    return $value;
                }
                throw new Exception('Hacking versuch entdeckt', 1011);
                break;
            case self::MD5:
                if (preg_match('#[0-9a-f]{32}#i', $value)) {
                    return $value;
                }
                throw new Exception('Hacking versuch entdeckt', 1011);
                break;
            case self::STRING:
            case self::PLAIN;
            default:
                return $value;
        }
    }

    /**
     * konvertiert den Uebergebene Wert in das gewuenschte Format
     * 
     * @param Mixed   $value    Wert
     * @param Integer $dataType Datentyp Konstante
     */
    public static function convert($value, $dataType) {

        switch ($dataType) {

            case self::INTEGER:
                //Ganzzahl
                return intval($value);
                break;
            case self::FLOAT:
                //Gleitpunktzahl
                return floatval($value);
                break;
            case self::MD5:
            case self::STRING:
                //Zeichenkette
                return $value;
                break;
            case self::BOOLEAN:
                //Wahrheitswert
                if (intval($value) === 1) {
                    return true;
                }
                return false;
                break;
            case self::ARRAY_STR:
                //Array
                return unserialize($value);
                break;
        }
    }

}
