<?php

namespace SHC\View\Room;

//Imports
use RWF\Core\RWF;
use RWF\Util\String;
use SHC\Sensor\Sensor;
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
     * erstellt das HTML Fragment zur Anzeige eines Sensors
     * 
     * @param  \SHC\Sensor\Sensor  $sensor     Sensor
     * @param  Booelan             $ignoreShow Sensoren Anzeigen trotz abgewahlt
     * @return String
     */
    public static function showSensor(Sensor $sensor, $ignoreShow = false) {

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
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.temp') .' : ';
                $html .= '<span id="shc-view-sensor-ds18x20-' . $sensor->getId() . '-temp">' . String::formatFloat($sensor->getTemperature(), 1) . '</span>&deg;C';
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon shc-icon-ds18x20"></span>';
                $html .= '<div class="shc-contentbox-body-row-content">';
                $html .= '<span id="shc-view-sensor-ds18x20-' . $sensor->getId() . '-temp">' . String::formatFloat($sensor->getTemperature(), 1) . '</span>&deg;C';
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
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.temp') .' : ';
                    $html .= '<span id="shc-view-sensor-dht-' . $sensor->getId() . '-temp">' . String::formatFloat($sensor->getTemperature(), 1) . '</span>&deg;C ';
                    $firstRow = false;
                }
                if ($sensor->isHumidityVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.hum') .' : ';
                    $html .= '<span id="shc-view-sensor-dht-' . $sensor->getId() . '-hum">' . String::formatFloat($sensor->getHumidity(), 1) . '</span>% ';
                }
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon shc-icon-dht"></span>';
                $html .= '<div class="shc-contentbox-body-row-content %%%%">';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '<span id="shc-view-sensor-dht-' . $sensor->getId() . '-temp">' . String::formatFloat($sensor->getTemperature(), 1) . '</span>&deg;C';
                    $firstRow = false;
                    $i++;
                }
                if ($sensor->isHumidityVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '<span id="shc-view-sensor-dht-' . $sensor->getId() . '-hum">' . String::formatFloat($sensor->getHumidity(), 1) . '</span>%';
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
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && ($sensor->isTemperatureVisible() || $sensor->isPressureVisible() || $sensor->isAltitudeVisible()))) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.temp') .' : ';
                    $html .= '<span id="shc-view-sensor-bmp-' . $sensor->getId() . '-temp">' . String::formatFloat($sensor->getTemperature(), 1) . '</span>&deg;C ';
                    $firstRow = false;
                }
                if ($sensor->isPressureVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.press') .' : ';
                    $html .= '<span id="shc-view-sensor-bmp-' . $sensor->getId() . '-press">' . String::formatFloat($sensor->getPressure(), 1) . '</span>hPa ';
                    $firstRow = false;
                }
                if ($sensor->isAltitudeVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.alti') .' : ';
                    $html .= '<span id="shc-view-sensor-bmp-' . $sensor->getId() . '-alti">' . String::formatFloat($sensor->getAltitude(), 1) . '</span>m';
                }
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon shc-icon-bmp"></span>';
                $html .= '<div class="shc-contentbox-body-row-content %%%%">';
                if ($sensor->isTemperatureVisible() || $ignoreShow == true) {

                    $html .= '<span id="shc-view-sensor-bmp-' . $sensor->getId() . '-temp">' . String::formatFloat($sensor->getTemperature(), 1) . '</span>&deg;C';
                    $firstRow = false;
                    $i++;
                }
                if ($sensor->isPressureVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '<span id="shc-view-sensor-bmp-' . $sensor->getId() . '-press">' . String::formatFloat($sensor->getPressure(), 1) . '</span>hPa';
                    $firstRow = false;
                    $i++;
                }
                if ($sensor->isAltitudeVisible() || $ignoreShow == true) {

                    ($firstRow === false ? $html .= '<br/>' : null);
                    $html .= '<span id="shc-view-sensor-bmp-' . $sensor->getId() . '-alti">' . String::formatFloat($sensor->getAltitude(), 1) . '</span>m';
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
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && $sensor->isValueVisible())) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.moisture') .' : ';
                $html .= '<span id="shc-view-sensor-analog-' . $sensor->getId() . '-value">' . String::formatInteger($sensor->getValue() * 100 / 1023) . '</span>%';
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon shc-icon-rain"></span>';
                $html .= '<div class="shc-contentbox-body-row-content">';
                $html .= '<span id="shc-view-sensor-analog-' . $sensor->getId() . '-value">' . String::formatInteger($sensor->getValue() * 100 / 1023) . '</span>%';
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
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && $sensor->isValueVisible())) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.moisture') .' : ';
                $html .= '<span id="shc-view-sensor-analog-' . $sensor->getId() . '-value">' . String::formatInteger($sensor->getValue() * 100 / 1023) . '</span>%';
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon shc-icon-hygrometer"></span>';
                $html .= '<div class="shc-contentbox-body-row-content">';
                $html .= '<span id="shc-view-sensor-analog-' . $sensor->getId() . '-value">' . String::formatInteger($sensor->getValue() * 100 / 1023) . '</span>%';
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
        if ($ignoreShow == true || ($sensor->isVisible() == Sensor::SHOW && $sensor->isValueVisible())) {

            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobile Ansicht
                $html .= '<li>';
                $html .= '<span style="font-weight: bold;">'. String::encodeHtml($sensor->getName()) .' : </span></br>';
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;'. RWF::getLanguage()->get('index.room.sensorValue.lightIntensity') .' : ';
                $html .= '<span id="shc-view-sensor-analog-' . $sensor->getId() . '-value">' . String::formatInteger($sensor->getValue() * 100 / 1023) . '</span>%';
                $html .= '</li>';
            } else {

                //Web Ansicht
                $html .= '<div class="shc-contentbox-body-row shc-view-sensor">';
                $html .= '<span class="shc-contentbox-body-row-title">' . String::encodeHTML($sensor->getName()) . '</span>';
                $html .= '<span class="shc-icon shc-icon-ldr"></span>';
                $html .= '<div class="shc-contentbox-body-row-content">';
                $html .= '<span id="shc-view-sensor-analog-' . $sensor->getId() . '-value">' . String::formatInteger($sensor->getValue() * 100 / 1023) . '</span>%';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

}
