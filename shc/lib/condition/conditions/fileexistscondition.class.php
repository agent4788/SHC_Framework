<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use RWF\Util\FileUtil;

/**
 * Bedingung Datei vorhanden
 *
 * @author     Arthur Rupp
 * @copyright  Copyright (c) 2014, arteck
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.2-0
 * @version    2.0.2-0
 */
class FileExistsCondition extends AbstractCondition {

    /**
     * gibt an ob die Bedingung erfuellt ist
     *
     * @return Boolean
     */
    public function isSatisfies() {

        //wenn deaktiviert immer True
        if (!$this->isEnabled()) {
            return true;
        }

        //noetige Parameter pruefen
        if (!isset($this->data['path'])) {

            throw new \Exception('Dateiname wurde nicht angegeben', 1580);
        }

        //Daten vorbereiten
        $path = FileUtil::addLeadingSlash(\trim($this->data['path']));
        $wait = (isset($this->data['wait']) && $this->data['wait'] > 0 ? intval($this->data['wait']) : 0);
        $delete = (isset($this->data['delete']) && $this->data['delete'] == 1 ? true : false);
        $invert = (isset($this->data['invert']) && $this->data['invert'] == 1 ? true : false);

        //pruefen ob invertiert (wenn invertiert wird geprüft ob Datei nicht vorhanden
        if($invert === false) {

            //nach vorhandener Datei sichen
            if (@file_exists($path)) {

                //wartezeit
                if($wait > 0) {

                    sleep($wait);
                }

                //Datei loeschen
                if($delete === true) {

                    FileUtil::deleteFile($path);
                }
                return true;
            }
        } else {

            //Nach nicht vorhandener Datei suchen
            if (!@file_exists($path)) {

                return true;
            }
        }
        return false;
    }

}