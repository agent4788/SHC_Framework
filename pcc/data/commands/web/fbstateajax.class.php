<?php

namespace PCC\Command\Web;

//Imports
use PCC\Core\PCC;
use RWF\AVM\FritzBoxFactory;
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;

/**
 * Zeigt den Systemstatus an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FBStateAjax extends AjaxCommand {

    protected $requiredPremission = 'pcc.ucp.fbState';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Pruefen ob aktiv
        if(!RWF::getSetting('pcc.fritzBox.showState')) {

            throw new \Exception('Die Funktion ist deaktiviert', 1014);
        }

        //Template anzeigen
        $tpl = RWF::getTemplate();

        //Daten zusammenstellen
        $fritzBox = FritzBoxFactory::getFritzBox();

        //Geraeteinformationen
        $fbDev = $fritzBox->getDevice();
        $tpl->assign('modelName', $fbDev->getModelName());
        $tpl->assign('uptime', $fbDev->getUpTime());
        $tpl->assign('hardwareVersion', $fbDev->getHardwareVersion());
        $tpl->assign('softwareVersion', $fbDev->getSoftwareVersion());
        $tpl->assign('dns1', $fbDev->getDNSServer1());
        $tpl->assign('dns2', $fbDev->getDNSServer2());
        $tpl->assign('voipDns1', $fbDev->getVoipDNSServer1());
        $tpl->assign('voipDns2', $fbDev->getVoipDNSServer2());

        //WAN Informationen
        if(PCC::getSetting('pcc.fritzBox.dslConnected')) {

            $fbWan = $fritzBox->getWan();
            $tpl->assign('pyhsicalState', $fbWan->getPhysicalLinkStatus());
            $tpl->assign('accessType', $fbWan->getAccessType());
            $tpl->assign('connectState', $fbWan->getConnectionStatus());
            $tpl->assign('connectUptime', $fbWan->getConnectionUptime());
            $tpl->assign('downMax', $fbWan->getConnectionDownstreamMaxBitRate() / 1000);
            $tpl->assign('upMax', $fbWan->getConnectionUpstreamMaxBitRate() / 1000);
            $tpl->assign('downCurr', $fbWan->getConnectionDownstreamCurrentBitRate() / 1000);
            $tpl->assign('upCurr', $fbWan->getConnectionUpstreamCurrentBitRate() / 1000);
            $tpl->assign('downTotal', $fbDev->getTotalBytesReceived());
            $tpl->assign('upTotal', $fbDev->getTotalBytesSent());
            $tpl->assign('externalIp', $fbWan->getExternalIp());
        }

        //WLan Informationen
        $fbWlan = $fritzBox->getWlan();
        $tpl->assign('wlan1IsEnabled', $fbWlan->is2GHzWlanEnabled());
        $tpl->assign('wlan1Standard', $fbWlan->get2GHzWlanStandard());
        $tpl->assign('wlan1Mac', $fbWlan->get2GHzWlanMacAddress());
        $tpl->assign('wlan1MaxBitRate', $fbWlan->get2GHzWlanMaxBitRate());
        $tpl->assign('wlan1Ssid', $fbWlan->get2GHzWlanSSID());
        $tpl->assign('wlan2IsEnabled', $fbWlan->is5GHzWlanEnabled());
        $tpl->assign('wlan2Standard', $fbWlan->get5GHzWlanStandard());
        $tpl->assign('wlan2Mac', $fbWlan->get5GHzWlanMacAddress());
        $tpl->assign('wlan2MaxBitRate', $fbWlan->get5GHzWlanMaxBitRate());
        $tpl->assign('wlan2Ssid', $fbWlan->get5GHzWlanSSID());
        $tpl->assign('wlanGuestIsEnabled', $fbWlan->isGuestWlanEnabled());
        $tpl->assign('wlanGuestStandard', $fbWlan->getGuestWlanStandard());
        $tpl->assign('wlanGuestMac', $fbWlan->getGuestWlanMacAddress());
        $tpl->assign('wlanGuestMaxBitRate', $fbWlan->getGuestWlanMaxBitRate());
        $tpl->assign('wlanGuestSsid', $fbWlan->getGuestWlanSSID());

        //HTML senden
        $this->data = $tpl->fetchString('fbstate.html');
    }
}