<?php

namespace SHC\View\Room;

//Imports
use RWF\Core\RWF;
use RWF\Util\String;
use SHC\Sensor\Sensor;
use SHC\Sensor\Sensors\AvmMeasuringSocket;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;

/**
 * erstellt aus Sensorobjekten HTML Fragmente
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class SensorViewHelper {

    /**
     * Raum ID
     *
     * @var Integer
     */
    protected static $roomId = 0;

    /**
     * erstellt das HTML Fragment zur Anzeige eines Sensors
     *
     * @param  Integer             $roomId     Raum ID
     * @param  \SHC\Sensor\Sensor  $sensor     Sensor
     * @param  bool                $ignoreShow Sensoren Anzeigen trotz abgewahlt
     * @return String
     */
    public static function showSensor($roomId, Sensor $sensor, $ignoreShow = false) {

        self::$roomId = $roomId;
        if ($sensor instanceof DS18x20) {

            return self::showDS18x20($sensor, $ignoreShow);
        } elseif ($sensor instanceof DHT) {

            return self::showDHT($sensor, $ignoreShow);
        } elseif ($sensor instanceof BMP) {

            return self::showBMP($sensor, $ignoreShow);
        } elseif ($sensor instanceof RainSensor) {

            return self::showRainsensor($sensor, $ignoreShow);
        } elseif ($sensor instanceof Hygrometer) {

            return self::showHygrometer($sensor, $ignoreShow);
        } elseif ($sensor instanceof LDR) {

            return self::showLDR($sensor, $ignoreShow);
        } elseif ($sensor instanceof AvmMeasuringSocket) {

            return self::showLAvmMeasuringSocket($sensor, $ignoreShow);
        }
        return '<span>Unbekannter Sensor</span>';
    }

    /**
     * bereitet die Daten eines DS18x20 zur Anzeige vor
     * 
     * @param  \SHC\Sensor\Sensors\DS18x20 $sensor     Sensor Objekt
     * @param  Boolean                     $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    public static function showDS18x20(DS18x20 $sensor, $ignoreShow = false) {

        $html = '';
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && $sensor->isTemperatureVisible())) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                if(SHC_DETECTED_DEVICE != 'smartphone') {

                    $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                }
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.temp') .' : ';
                $html .= '<span id="shc-view-sensor-ds18x20-' . self::$roomId . '-' . $sensor->getId() . '-temp">' . String::encodeHTML($sensor->getDisplayTemperature()) . '</span>';
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                $html .= '<div class="shc-contentbox-body-row-content" style="padding-left: 10px;">';
                $html .= '<span id="shc-view-sensor-ds18x20-' . self::$roomId . '-' . $sensor->getId() . '-temp">' . String::encodeHTML($sensor->getDisplayTemperature()) . '</span>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten eines DHT zur Anzeige vor
     * 
     * @param  \SHC\Sensor\Sensors\DHT $sensor     Sensor Objekt
     * @param  Boolean                 $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    public static function showDHT(DHT $sensor, $ignoreShow = false) {

        $html = '';
        $firstRow = true;
        $i = 0;
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && ($sensor->isTemperatureVisible() || $sensor->isHumidityVisible()))) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                if(SHC_DETECTED_DEVICE != 'smartphone') {

                    $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                }
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.temp') .' : ';
                    $html .= '<span id="shc-view-sensor-dht-' . self::$roomId . '-' . $sensor->getId() . '-temp">' . String::encodeHTML($sensor->getDisplayTemperature()) . '</span>';
                    $firstRow = false;
                }
                if ($sensor->isHumidityVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.hum') .' : ';
                    $html .= '<span id="shc-view-sensor-dht-' . self::$roomId . '-' . $sensor->getId() . '-hum">' . String::encodeHTML($sensor->getDisplayHumidity()) . '</span> ';
                }
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                $html .= '<div class="shc-contentbox-body-row-content %%%%" style="padding-left: 10px;">';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '<span id="shc-view-sensor-dht-' . self::$roomId . '-' . $sensor->getId() . '-temp">' . String::encodeHTML($sensor->getDisplayTemperature()) . '</span>';
                    $firstRow = false;
                    $i++;
                }
                if ($sensor->isHumidityVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '<span id="shc-view-sensor-dht-' . self::$roomId . '-' . $sensor->getId() . '-hum">' . String::encodeHTML($sensor->getDisplayHumidity()) . '</span>';
                    $i++;
                }
                $html .= '</div>';
                $html .= '</div>';

                //CSS Ausrichtung
                if ($i == 2) {

                    $html = preg_replace('#%%%%#', 'shc-view-middle', $html);
                }
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten eines BMP zur Anzeige vor
     * 
     * @param  \SHC\Sensor\Sensors\BMP $sensor     Sensor Objekt
     * @param  Boolean                 $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    public static function showBMP(BMP $sensor, $ignoreShow = false) {

        $html = '';
        $firstRow = true;
        $i = 0;
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && ($sensor->isTemperatureVisible() || $sensor->isAirPressureVisible() || $sensor->isAltitudeVisible()))) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                if(SHC_DETECTED_DEVICE != 'smartphone') {

                    $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                }
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.temp') .' : ';
                    $html .= '<span id="shc-view-sensor-bmp-' . self::$roomId . '-' . $sensor->getId() . '-temp">' . String::encodeHTML($sensor->getDisplayTemperature()) . '</span>';
                    $firstRow = false;
                }
                if ($sensor->isAirPressureVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.press') .' : ';
                    $html .= '<span id="shc-view-sensor-bmp-' . self::$roomId . '-' . $sensor->getId() . '-press">' . String::encodeHTML($sensor->getDisplayAirPressure()) . '</span>';
                    $firstRow = false;
                }
                if ($sensor->isAltitudeVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.alti') .' : ';
                    $html .= '<span id="shc-view-sensor-bmp-' . self::$roomId . '-' . $sensor->getId() . '-alti">' . String::encodeHTML($sensor->getDisplayAltitude()) . '</span>';
                }
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                $html .= '<div class="shc-contentbox-body-row-content %%%%" style="padding-left: 10px;">';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '<span id="shc-view-sensor-bmp-' . self::$roomId . '-' . $sensor->getId() . '-temp">' . String::encodeHTML($sensor->getDisplayTemperature()) . '</span>';
                    $firstRow = false;
                    $i++;
                }
                if ($sensor->isAirPressureVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '<span id="shc-view-sensor-bmp-' . self::$roomId . '-' . $sensor->getId() . '-press">' . String::encodeHTML($sensor->getDisplayAirPressure()) . '</span>';
                    $firstRow = false;
                    $i++;
                }
                if ($sensor->isAltitudeVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '<span id="shc-view-sensor-bmp-' . self::$roomId . '-' . $sensor->getId() . '-alti">' . String::encodeHTML($sensor->getDisplayAltitude()) . '</span>';
                    $i++;
                }
                $html .= '</div>';
                $html .= '</div>';

                //CSS Ausrichtung
                if ($i == 3) {

                    $html = preg_replace('#%%%%#', 'shc-view-low', $html);
                } elseif ($i == 2) {

                    $html = preg_replace('#%%%%#', 'shc-view-middle', $html);
                }
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten eines Regensensors zur Anzeige vor
     * 
     * @param  \SHC\Sensor\Sensors\RainSensor $sensor     Sensor Objekt
     * @param  Boolean                        $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    public static function showRainSensor(RainSensor $sensor, $ignoreShow = false) {

        $html = '';
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && $sensor->isMoistureVisible())) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                if(SHC_DETECTED_DEVICE != 'smartphone') {

                    $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                }
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.moisture') .' : ';
                $html .= '<span id="shc-view-sensor-analog-' . self::$roomId . '-' . $sensor->getId() . '-value">' . String::encodeHTML($sensor->getDisplayMoisture()) . '</span>';
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                $html .= '<div class="shc-contentbox-body-row-content" style="padding-left: 10px;">';
                $html .= '<span id="shc-view-sensor-analog-' . self::$roomId . '-' . $sensor->getId() . '-value">' . String::encodeHTML($sensor->getDisplayMoisture()) . '</span>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten eines Hygrometer zur Anzeige vor
     * 
     * @param  \SHC\Sensor\Sensors\Hygrometer $sensor     Sensor Objekt
     * @param  Boolean                        $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    public static function showHygrometer(Hygrometer $sensor, $ignoreShow = false) {

        $html = '';
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && $sensor->isMoistureVisible())) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                if(SHC_DETECTED_DEVICE != 'smartphone') {

                    $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                }
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.moisture') .' : ';
                $html .= '<span id="shc-view-sensor-analog-' . self::$roomId . '-' . $sensor->getId() . '-value">' . String::encodeHTML($sensor->getDisplayMoisture()) . '</span>';
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                $html .= '<div class="shc-contentbox-body-row-content" style="padding-left: 10px;">';
                $html .= '<span id="shc-view-sensor-analog-' . self::$roomId . '-' . $sensor->getId() . '-value">' . String::encodeHTML($sensor->getDisplayMoisture()) . '</span>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten eines Lichtsensors zur Anzeige vor
     * 
     * @param  \SHC\Sensor\Sensors\LDR $sensor     Sensor Objekt
     * @param  Boolean                 $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    public static function showLDR(LDR $sensor, $ignoreShow = false) {

        $html = '';
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && $sensor->isLightIntensityVisible())) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                if(SHC_DETECTED_DEVICE != 'smartphone') {

                    $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                }
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.lightIntensity') .' : ';
                $html .= '<span id="shc-view-sensor-analog-' . self::$roomId . '-' . $sensor->getId() . '-value">' . String::encodeHTML($sensor->getDisplayLightIntensity()) . '</span>';
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                $html .= '<div class="shc-contentbox-body-row-content" style="padding-left: 10px;">';
                $html .= '<span id="shc-view-sensor-analog-' . self::$roomId . '-' . $sensor->getId() . '-value">' . String::encodeHTML($sensor->getDisplayLightIntensity()) . '</span>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten eines AVM Power Sensors zur Anzeige vor
     *
     * @param  \SHC\Sensor\Sensors\AvmMeasuringSocket $sensor     Sensor Objekt
     * @param  Boolean                                $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    public static function showLAvmMeasuringSocket(AvmMeasuringSocket $sensor, $ignoreShow = false) {

        $html = '';
        $firstRow = true;
        $i = 0;
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && ($sensor->isTemperatureVisible() || $sensor->isPowerVisible() || $sensor->isEnergyVisible()))) {

            $sensorId = str_replace(' ', '-', $sensor->getId());
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                if(SHC_DETECTED_DEVICE != 'smartphone') {

                    $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                }
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.temp') .' : ';
                    $html .= '<span id="shc-view-sensor-avmPowerSensor-' . self::$roomId . '-' . $sensorId . '-temp">' . String::encodeHTML($sensor->getDisplayTemperature()) . '</span>';
                    $firstRow = false;
                }
                if ($sensor->isPowerVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.power') .' : ';
                    $html .= '<span id="shc-view-sensor-avmPowerSensor-' . self::$roomId . '-' . $sensorId . '-power">' . String::encodeHTML($sensor->getDisplayPower()) . '</span>';
                    $firstRow = false;
                }
                if ($sensor->isEnergyVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.energy') .' : ';
                    $html .= '<span id="shc-view-sensor-avmPowerSensor-' . self::$roomId . '-' . $sensorId . '-energy">' . String::encodeHTML($sensor->getDisplayEnergy()) . '</span>';
                }
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon '. $sensor->getIcon() .'"></span>';
                $html .= '<div class="shc-contentbox-body-row-content %%%%" style="padding-left: 10px;">';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '<span id="shc-view-sensor-avmPowerSensor-' . self::$roomId . '-' . $sensorId . '-temp">' . String::encodeHTML($sensor->getDisplayTemperature()) . '</span>';
                    $firstRow = false;
                    $i++;
                }
                if ($sensor->isPowerVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '<span id="shc-view-sensor-avmPowerSensor-' . self::$roomId . '-' . $sensorId . '-power">' . String::encodeHTML($sensor->getDisplayPower()) . '</span>';
                    $firstRow = false;
                    $i++;
                }
                if ($sensor->isEnergyVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '<span id="shc-view-sensor-avmPowerSensor-' . self::$roomId . '-' . $sensorId . '-energy">' . String::encodeHTML($sensor->getDisplayEnergy()) . '</span>';
                    $i++;
                }
                $html .= '</div>';
                $html .= '</div>';

                //CSS Ausrichtung
                if ($i == 3) {

                    $html = preg_replace('#%%%%#', 'shc-view-low', $html);
                } elseif ($i == 2) {

                    $html = preg_replace('#%%%%#', 'shc-view-middle', $html);
                }
            }
        }
        return $html;
    }
}
