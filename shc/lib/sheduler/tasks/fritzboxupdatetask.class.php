<?php

namespace SHC\Sheduler\Tasks;

//Imports
use RWF\AVM\FritzBoxFactory;
use RWF\Core\RWF;
use RWF\Util\CliUtil;
use SHC\Core\SHC;
use SHC\Sheduler\AbstractTask;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\FritzBox;

/**
 * wenn aktiviert kann eine LED mit 0,5Hz Binktakt anzeigen ob der Sheduler läuft
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxUpdateTask extends AbstractTask {

    /**
     * Prioriteat
     *
     * @var Integer
     */
    protected $priority = 20;

    /**
     * Wartezeit zwischen 2 durchläufen
     *
     * @var String
     */
    protected $interval = 'PT15S';

    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public function executeTask() {

        //pruefen ob die FritzBox Konfiguriert ist
        if(RWF::getSetting('rwf.fritzBox.user') != '') {

            //Intervall festlegen
            switch(SHC::getSetting('shc.shedulerDaemon.performanceProfile')) {

                case 1:

                    //fast
                    $this->interval = 'PT10S';
                    break;
                case 2:

                    //default
                    $this->interval = 'PT15S';
                    break;
                case 3:

                    //slow
                    $this->interval = 'PT60S';
                    break;
            }

            try {

                //Fritz Box initialisieren
                $fritzBox = FritzBoxFactory::getFritzBox();
                $smartHome = $fritzBox->getSmartHome();
                $wlan = $fritzBox->getWlan();

                //Cache erneuern
                $smartHome->rebuildCache();
                $wlan->rebuildCache();

                //Geräteliste abrufen
                $deviceList = $smartHome->listDevices();

                //schaltbare Elemente laden
                SwitchableEditor::getInstance()->loadData();
                $switchableList = SwitchableEditor::getInstance()->listElements();

                //Geraete durchlaufen
                foreach($deviceList as $smartHomeDevice) {

                    //schaltbare Elemente aktualisieren
                    foreach($switchableList as $switchable) {

                        if($switchable instanceof AvmSocket && $switchable->getAin() == $smartHomeDevice['device']['ain'] && isset($smartHomeDevice['switch']['state'])) {

                            //status der Steckdose akualisieren
                            $switchable->setState(((int) $smartHomeDevice['switch']['state'] == 1 ? 1 : 0));
                        } elseif($switchable instanceof FritzBox && $switchable->getFunction() == 1) {

                            //WLan 1 Status aktualisieren
                            $switchable->setState($wlan->is2GHzWlanEnabled());
                        } elseif($switchable instanceof FritzBox && $switchable->getFunction() == 2) {

                            //WLan 2 Status aktualisieren
                            $switchable->setState($wlan->is5GHzWlanEnabled());
                        } elseif($switchable instanceof FritzBox && $switchable->getFunction() == 3) {

                            //WLan 3 Status aktualisieren
                            $switchable->setState($wlan->isGuestWlanEnabled());
                        }
                    }

                    SwitchableEditor::getInstance()->updateState();

                    //Sensordaten an den Sensorempfaenger senden
                    if($smartHomeDevice['present'] == 1 && (isset($smartHomeDevice['powermeter']) || isset($smartHomeDevice['temperature']))) {

                        $get = '&spid=999';
                        $get .= '&sid='. urlencode($smartHomeDevice['device']['ain']);
                        $get .= '&type=7';
                        if(isset($smartHomeDevice['temperature'])) {

                            $get .= '&v1='. $smartHomeDevice['temperature']['temperature'];
                        } else {

                            $get .= '&v1=0.0';
                        }
                        if(isset($smartHomeDevice['powermeter'])) {

                            $get .= '&v2='. $smartHomeDevice['powermeter']['power'];
                            $get .= '&v3='. $smartHomeDevice['powermeter']['energy'];
                        } else {

                            $get .= '&v2=0';
                            $get .= '&v3=0';
                        }

                        //HTTP Anfrage
                        $http_options = stream_context_create(array(
                            'http' => array(
                                'method'  => 'GET',
                                'user_agent' => "SHC Framework Sensor Transmitter Version ". SHC::VERSION,
                                'max_redirects' => 3
                            )
                        ));
                        @file_get_contents('http://localhost:80/shc/index.php?app=shc&a&ajax=pushsensorvalues'. $get, false, $http_options);
                    }
                }
            } catch(\SoapFault $e) {

                $cli = new CliUtil();
                $cli->writeLineColored('Fritz!Box verbindung Fehlgeschlagen: '. $e->getMessage(), 'red');
                FritzBoxFactory::getFritzBox()->rebuildCache();
            }
        }
    }
}