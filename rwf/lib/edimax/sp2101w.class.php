<?php

namespace RWF\Edimax;

//Imports
use RWF\XML\XmlEditor;


/**
 * Auslesen von Daten und Schalten der Edimax WLan Steckdose SP-2101W
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SP2101W extends SP1101W {

    /**
     * Energiedaten
     *
     * @var array
     */
    protected $energyData = array();

    /**
     * liest die Energiedaten einer WLan Steckdose
     *
     * @return boolean
     * @throws \RWF\XML\Exception\XmlException
     */
    public function readEnergyData() {

        $content = '<?xml version="1.0" encoding="UTF8"?>
                        <SMARTPLUG id="edimax">
                            <CMD id="get">
                                <NOW_POWER></NOW_POWER>
                            </CMD>
                        </SMARTPLUG>
        ';

        $response = $this->sendHttpCommand($content);
        if($response !== false) {

            $this->energyData = array();
            $xml = XmlEditor::createFromString($response);
            if(isset($xml->CMD) && isset($xml->CMD->NOW_POWER)) {

                if(isset($xml->CMD->NOW_POWER->{'Device.System.Power.LastToggleTime'})) {

                    $this->energyData['LastToggleTime'] = (int) $xml->CMD->NOW_POWER->{'Device.System.Power.LastToggleTime'};
                }

                if(isset($xml->CMD->NOW_POWER->{'Device.System.Power.NowCurrent'})) {

                    $this->energyData['NowCurrent'] = (float) $xml->CMD->NOW_POWER->{'Device.System.Power.NowCurrent'};
                }

                if(isset($xml->CMD->NOW_POWER->{'Device.System.Power.NowPower'})) {

                    $this->energyData['NowPower'] = (float) $xml->CMD->NOW_POWER->{'Device.System.Power.NowPower'};
                }

                if(isset($xml->CMD->NOW_POWER->{'Device.System.Power.NowEnergy.Day'})) {

                    $this->energyData['NowEnergy_Day'] = (float) $xml->CMD->NOW_POWER->{'Device.System.Power.NowEnergy.Day'};
                }

                if(isset($xml->CMD->NOW_POWER->{'Device.System.Power.NowEnergy.Week'})) {

                    $this->energyData['NowEnergy_Week'] = (float) $xml->CMD->NOW_POWER->{'Device.System.Power.NowEnergy.Week'};
                }

                if(isset($xml->CMD->NOW_POWER->{'Device.System.Power.NowEnergy.Month'})) {

                    $this->energyData['NowEnergy_Month'] = (float) $xml->CMD->NOW_POWER->{'Device.System.Power.NowEnergy.Month'};
                }
                return true;
            }
        }
        return false;
    }

    /**
     * gibt den Zeitstempel des letzten Schaltvorganges zurück
     *
     * @return int
     */
    public function getLastToggleTime() {

        if(count($this->energyData) == 0) {

            $this->readEnergyData();
        }

        return $this->energyData['LastToggleTime'];
    }

    /**
     * gibt den aktuellen Strom in A zurück
     *
     * @return float
     */
    public function getNowCurrent() {

        if(count($this->energyData) == 0) {

            $this->readEnergyData();
        }

        return $this->energyData['NowCurrent'];
    }

    /**
     * gibt den aktuellen Energiebedarf in W zurück
     *
     * @return float
     */
    public function getNowPower() {

        if(count($this->energyData) == 0) {

            $this->readEnergyData();
        }

        return $this->energyData['NowPower'];
    }

    /**
     * gibt den heutigen Energieverbrauch in Wh zurück
     *
     * @return float
     */
    public function getDayEnergy() {

        if(count($this->energyData) == 0) {

            $this->readEnergyData();
        }

        return $this->energyData['NowEnergy_Day'];
    }

    /**
     * gibt den Energieverbrauch in Wh der Woche zurück
     *
     * @return float
     */
    public function getWeekEnergy() {

        if(count($this->energyData) == 0) {

            $this->readEnergyData();
        }

        return $this->energyData['NowEnergy_Week'];
    }

    /**
     * gibt den Energieverbrauch in Wh des Monats zurück
     *
     * @return float
     */
    public function getMonthEnergy() {

        if(count($this->energyData) == 0) {

            $this->readEnergyData();
        }

        return $this->energyData['NowEnergy_Month'];
    }
}