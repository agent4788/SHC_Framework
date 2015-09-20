<?php

namespace SHC\Util;

//Imports


/**
 * Hilfsfunktionen für Funksteckdosen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class RadioSocketsUtil {

    /**
     * pruef ob der Systemcode in binaerform angegeben wurde
     *
     * @param  String  $systemCode Systemcode
     * @return Boolean
     */
    public static function isBinary($systemCode) {

        if(preg_match('#^[01]{5}$#', $systemCode)) {

            return true;
        }
        return false;
    }

    /**
     * errechnet aus einem Binaeren String den Zahlencode
     *
     * @param  String  $systemCode Systemcode
     * @return Integer
     */
    public static function convertBinaryToDec($systemCode) {

        $return = 0;
        for($j = 0; $j < strlen($systemCode); $j++) {

            $bin_tmp = substr($systemCode, $j, 1);
            $return .= $bin_tmp * (pow(2, $j));
        }
        return $return;
    }

    /**
     * errechnet aus einem Zahlencode einen Binaeren String
     *
     * @param  String  $systemCode Systemcode
     * @return Integer
     */
    public static function convertDecToBinary($systemCode) {

        $return = '';
        foreach(array(1, 2, 4, 8, 16) as $i) {

            if($systemCode & $i) {

                $return .= '1';
            } else {

                $return .= '0';
            }
        }

        return $return;

    }
}