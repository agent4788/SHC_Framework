<?php

namespace SHC\Sheduler\Tasks;

//Imports
use RWF\AVM\FritzBoxFactory;
use SHC\Core\SHC;
use SHC\Sheduler\AbstractTask;
use SHC\Switchable\SwitchableEditor;

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

        //Fritz Box initialisieren
        $fritzBox = FritzBoxFactory::getFritzBox();
        $smartHome = $fritzBox->getSmartHome();
        $deviceList = $smartHome->listDevices();
        $wlan = $fritzBox->getWlan();

        //schaltbare Elemente laden
        SwitchableEditor::getInstance()->loadData();
        $switchableList = SwitchableEditor::getInstance()->listElements();

        //Geraete durchlaufen
        foreach($deviceList as $smartHomeDevice) {

            //schaltbare Elemente aktualisieren
            foreach($switchableList as $switchable) {

                if($switchable instanceof \SHC\Switchable\Switchables\AvmSocket && $switchable->getAin() == $smartHomeDevice['device']['ain'] && isset($smartHomeDevice['switch']['state'])) {

                    //status der Steckdose akualisieren
                    $switchable->setState(((int) $smartHomeDevice['switch']['state'] == 1 ? 1 : 0));
                } elseif($switchable instanceof \SHC\Switchable\Switchables\FritzBox && $switchable->getFunction() == 1) {

                    //WLan 1 Status aktualisieren
                    $switchable->setState($wlan->is2GHzWlanEnabled());
                } elseif($switchable instanceof \SHC\Switchable\Switchables\FritzBox && $switchable->getFunction() == 2) {

                    //WLan 2 Status aktualisieren
                    $switchable->setState($wlan->is5GHzWlanEnabled());
                } elseif($switchable instanceof \SHC\Switchable\Switchables\FritzBox && $switchable->getFunction() == 3) {

                    //WLan 3 Status aktualisieren
                    $switchable->setState($wlan->isGuestWlanEnabled());
                }
            }

            SwitchableEditor::getInstance()->updateState();

            //Sensordaten an den Sensorempfaenger senden
            if(isset($smartHomeDevice['powermeter']) || isset($smartHomeDevice['temperature'])) {

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

            $smartHome->rebuildCache();
            $wlan->rebuildCache();
        }
    }
}