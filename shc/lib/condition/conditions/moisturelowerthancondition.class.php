<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use SHC\Sensor\Model\Moisture;
use SHC\Sensor\SensorPointEditor;

/**
 * Bedingung Feuchtigkeit kleiner als
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class MoistureLowerThanCondition extends AbstractCondition {

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
        if (!isset($this->data['sensors']) || !isset($this->data['moisture'])) {

            throw new \Exception('sensors und moisture müssen angegeben werden', 1580);
        }

        $sensors = explode(',', $this->data['sensors']);
        foreach ($sensors as $sensorId) {

            $sensor = SensorPointEditor::getInstance()->getSensorById($sensorId);
            if ($sensor instanceof Moisture) {

                $humidity = $sensor->getMoisture();
                if ($humidity <= (float) $this->data['moisture']) {

                    return true;
                }
            }
        }

        return false;
    }

}