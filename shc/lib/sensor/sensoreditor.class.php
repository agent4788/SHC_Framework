<?php

namespace SHC\Sensor;

//Imports
use RWF\XML\XmlFileManager;
use SHC\Core\SHC;

/**
 * Verwaltung der Sensoren eines Sensorpunktes
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorEditor {

    /**
     * Sensoren
     *
     * @var Array
     */
    protected $sensors = array('dht' => array(), 'bmp' => array());

    /**
     * Singleton Instanz
     *
     * @var \SHC\Sensor\SensorEditor
     */
    protected static $instance = null;

    protected function __construct() {

        $this->loadData();
    }

    /**
     * Daten laden
     *
     * @throws \Exception
     */
    public function loadData() {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SENSOR_TRANSMITTER, true);

        //Sensoren einlesen
        foreach($xml->dht as $dht) {

            //DHT Sensoren einlesen
            $this->sensors['dht'][(int) $dht->id] = array(
                'id' => (int) $dht->id,
                'type' => (int) $dht->type,
                'pin' => (int) $dht->pin,
                'name' => (string) $dht->name,
            );
        }
        if(isset($xml->bmp)) {

            //BMP Sensor einlesen
            $this->sensors['bmp'] = (int) $xml->bmp->id;
        }
    }

    /**
     * gibt eine Liste mit allen DHT Sensoren aus
     *
     * @return Array
     */
    public function listDHT() {

        return $this->sensors['dht'];
    }

    /**
     * fuegt einen DHT Sensor hinzu
     *
     * @param  Integer $id   Sensor ID (muss im kompletten Netzwerk eindeutig sein)
     * @param  Integer $type Typ (11|22|2302)
     * @param  Integer $pin  Wiring Pi Pin
     * @param  String  $name Name
     * @return Boolean
     * @throws \RWF\XML\Exception\XmlException
     */
    public function addDHT($id, $type, $pin, $name = '') {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SENSOR_TRANSMITTER, true);

        //Sensor anlegen
        $dht = $xml->addChild('dht');
        $dht->addChild('id', $id);
        $dht->addChild('type', $type);
        $dht->addChild('pin', $pin);
        $dht->addChild('name', $name);

        //Speichern
        $xml->save();
        return true;
    }

    /**
     * entfernt einen DHT Sensor
     *
     * @param  Integer $id Sensor ID
     * @return Boolean
     * @throws \RWF\XML\Exception\XmlException
     */
    public function removeDHT($id) {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SENSOR_TRANSMITTER, true);

        //Sensor suchen
        for($i = 0; $i < count($xml->dht); $i++) {

            if((int) $xml->dht[$i]->id == $id) {

                unset($xml->dht[$i]);

                //Speichern
                $xml->save();
                return true;
            }
        }
        return false;
    }

    /**
     * liest den Wert eines DHT Sensors
     *
     * @param  Integer $id Sensor ID
     * @return Array
     */
    public function readDHT($id) {

        if(isset($this->sensors['dht'][$id])) {

            //Sensor auslesen
            exec('sudo ' . PATH_SHC_CLASSES . 'external/python/dht.py ' . escapeshellcmd($this->sensors['dht'][$id]['type']) . ' ' . escapeshellcmd($this->sensors['dht'][$id]['pin']), $data);

            //Daten verarbeiten
            if($data != 'error') {

                $parts = explode(';', $data[0]);
                if(isset($parts[0]) && isset($parts[1])) {

                    return array(
                        'temp' => $parts[0],
                        'hum' => $parts[1]
                    );
                }
            }
        }
        return array('error');
    }

    /**
     * aktiviert den BMP Sensor
     *
     * @param  Integer $id Sensor ID (muss im kompletten Netzwerk eindeutig sein)
     * @return Boolean
     * @throws \RWF\XML\Exception\XmlException
     */
    public function enableBMP($id) {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SENSOR_TRANSMITTER, true);

        //BMP Sensor aktivieren
        $bmp = $xml->addChild('bmp');
        $bmp->addChild('id', $id);

        //Speichern
        $xml->save();
        return true;
    }

    /**
     * deaktiviert den BMP Sensor
     *
     * @return Boolean
     * @throws \RWF\XML\Exception\XmlException
     */
    public function disableBMP() {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SENSOR_TRANSMITTER, true);

        if(isset($xml->bmp)) {

            unset($xml->bmp);

            //Speichern
            $xml->save();
            return true;
        }
        return false;
    }

    /**
     * gibt an ob der BMP Sensor aktiviert ist
     *
     * @return Boolean
     */
    public function isBMPenabled() {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SENSOR_TRANSMITTER, true);

        if(isset($xml->bmp)) {

            return true;
        }
        return false;
    }

    /**
     * gibt die Sensor ID des BMP zurueck
     *
     * @return Integer
     */
    public function getBMPsensorId() {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SENSOR_TRANSMITTER, true);

        if(isset($xml->bmp)) {

            return (int) $xml->bmp->id;
        }
        return null;
    }

    /**
     * liest die Daten des BMP Sensors ein
     *
     * @return Array
     */
    public function readBMP() {

        if($this->isBMPenabled()) {

            //Sensor auslesen
            exec('sudo ' . PATH_SHC_CLASSES . 'external/python/bmp.py', $data);

            //Daten verarbeiten
            $parts = explode(';', $data[0]);
            if(isset($parts[0]) && isset($parts[1]) && isset($parts[2])) {

                return array(
                    'temp' => $parts[0],
                    'press' => $parts[1] / 100,
                    'alti' => $parts[2]
                );
            }
        }
        return array('error');
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {

    }

    /**
     * gibt den Editor fuer Sensoren zurueck
     *
     * @return \SHC\Sensor\SensorEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new SensorEditor();
        }
        return self::$instance;
    }

}