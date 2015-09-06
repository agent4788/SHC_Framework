<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use SHC\Sensor\Model\LightIntensity;
use SHC\Sensor\SensorPointEditor;

/**
 * Bedingung Lichtstaerke kleiner als
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LightIntensityLowerThanCondition extends AbstractCondition {

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
        if (!isset($this->data['sensors']) || !isset($this->data['lightIntensity'])) {

            throw new \Exception('sensors und lightIntensity müssen angegeben werden', 1580);
        }

        $sensors = explode(',', $this->data['sensors']);
        foreach ($sensors as $sensorId) {

            $sensor = SensorPointEditor::getInstance()->getSensorById($sensorId);
            if ($sensor instanceof LightIntensity) {

                $humidity = $sensor->getLightIntensity();
                if ($humidity <= (float) $this->data['lightIntensity']) {

                    return true;
                }
            }
        }

        return false;
    }

}