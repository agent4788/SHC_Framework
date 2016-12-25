<?php

namespace PCC\Command\Smartphone;

//Imports
use PCC\Core\PCC;
use RWF\AVM\FritzBoxFactory;
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Request\Commands\PageCommand;

/**
 * Startseite
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FbCallListPage extends PageCommand {

    /**
     * Template
     *
     * @var String
     */
    protected $template = 'fbcalllist.html';

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'pcc.ucp.fbCallList';

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
        if(!RWF::getSetting('pcc.fritzBox.showCallList')) {

            throw new \Exception('Die Funktion ist deaktiviert', 1014);
        }

        $tpl = PCC::getTemplate();

        //Headline Daten
        $tpl->assign('apps', PCC::listApps());
        $tpl->assign('style', PCC::getStyle());
        $tpl->assign('user', PCC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=pcc&m&page=index');
        $tpl->assign('device', PCC_DETECTED_DEVICE);
        $tpl->assign('title', PCC::getLanguage()->get('index.tabs.showCallList'));

        //Daten zusammenstellen
        $fritzBox = FritzBoxFactory::getFritzBox();

        //Anrufliste abrufen
        $fbCallList = $fritzBox->getCallList();
        $callList = $fbCallList->getCallListXml(RWF::getSetting('pcc.fritzBox.callListMax'), RWF::getSetting('pcc.fritzBox.callListDays'));

        $typeNames = array(
            1 => RWF::getLanguage()->val('index.fbCalList.type.1'),
            2 => RWF::getLanguage()->val('index.fbCalList.type.2'),
            3 => RWF::getLanguage()->val('index.fbCalList.type.3'),
            9 => RWF::getLanguage()->val('index.fbCalList.type.9'),
            10 => RWF::getLanguage()->val('index.fbCalList.type.10'),
            11 => RWF::getLanguage()->val('index.fbCalList.type.11')
        );

        $creationDate = new DateTime();
        $creationDate->setTimestamp((int) $callList->timestamp);
        $tpl->assign('creationDate', $creationDate);

        $calls = array();
        foreach($callList->Call as $call) {

            $id = (int) $call->Id;
            $type = (int) $call->Type;
            $calls[$id] = array(
                'id' => $id,
                'type' => $type,
                'typeNamed' => $typeNames[(int) $call->Type],
                'device' => (string) $call->Device,
                'date' => DateTime::createFromFormat('d.m.y H:i', (string) $call->Date),
                'duration' => (string) $call->Duration
            );

            if($type == 1 || $type == 2 || $type == 9 || $type == 10) {

                //Einegehend
                $calls[$id]['extContact'] = ((string) $call->Name != '' ? (string) $call->Name : (string) $call->Caller);
                $calls[$id]['intContact'] = (string) $call->CalledNumber;
            } else {

                //Ausgehend
                $calls[$id]['extContact'] = ((string) $call->Name != '' ? (string) $call->Name : (string) $call->Called);
                $calls[$id]['intContact'] = (string) $call->CallerNumber;
            }

        }
        $tpl->assign('callList', $calls);
    }
}