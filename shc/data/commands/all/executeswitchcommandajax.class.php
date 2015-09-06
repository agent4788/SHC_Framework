<?php

namespace SHC\Command\All;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use SHC\Command\CommandSheduler;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * fuehrt einen Schaltbefehl aus
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ExecuteSwitchCommandAjax extends AjaxCommand {

    protected $premission = '';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('room');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //HTML Encode aus
        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Objekt und Befehl holen
        $switchableId = RWF::getRequest()->getParam('sid', Request::GET, DataTypeUtil::INTEGER);
        $switchable = SwitchableEditor::getInstance()->getElementById($switchableId);
        $command = RWF::getRequest()->getParam('command', Request::GET, DataTypeUtil::INTEGER);

        //pruefen ob das schaltbare Element existiert
        if(!$switchable instanceof Switchable) {

            $this->data = array(
                'success' => false,
                'message' => RWF::getLanguage()->get('index.room.error.id')
            );
            RWF::getLanguage()->enableAutoHtmlEndocde();
            return;
        }

        //pruefen ob deaktiviert
        if(!$switchable->isEnabled()) {

            $this->data = array(
                'success' => false,
                'message' => RWF::getLanguage()->get('index.room.error.disabled')
            );
            RWF::getLanguage()->enableAutoHtmlEndocde();
            return;
        }

        //Berechtigungen Pruefen
        if(!$switchable->isUserEntitled(RWF::getVisitor())) {

            $this->data = array(
                'success' => false,
                'message' => RWF::getLanguage()->get('index.room.error.premission')
            );
            RWF::getLanguage()->enableAutoHtmlEndocde();
            return;
        }

        try{

            //je nach befehl schalten
            if($command == 0) {

                $switchable->switchOff();
            } elseif($command == 1) {

                $switchable->switchOn();
            } elseif($command == 2) {

                $switchable->toggle();
            } else {

                //Fehler ungueltiger Befehl
                $this->data = array(
                    'success' => false,
                    'message' => RWF::getLanguage()->get('index.room.error.command')
                );
                RWF::getLanguage()->enableAutoHtmlEndocde();
                return;
            }
        } catch(\SoapFault $e) {

            //Fehler ungueltiger Befehl
            $this->data = array(
                'success' => false,
                'message' => 'Fritz!Box Error: '. $e->getMessage()
            );
            RWF::getLanguage()->enableAutoHtmlEndocde();
            return;
        }

        //Befele Senden (nur Wake On lan sendet die Pakete direkt
        if(!$switchable instanceof WakeOnLan && !$switchable instanceof AvmSocket && !$switchable instanceof FritzBox) {

            try {

                CommandSheduler::getInstance()->sendCommands();
            } catch (\Exception $e) {

                if ($e->getCode() == 1510) {

                    //GPIO Schaltserver nicht errreicht
                    $this->data = array(
                        'success' => false,
                        'message' => RWF::getLanguage()->get('index.room.error.1510')
                    );
                    RWF::getLanguage()->enableAutoHtmlEndocde();
                    return;
                } elseif ($e->getCode() == 1511) {

                    //Schaltserver unterstuetzt kein GPIO schalten
                    $this->data = array(
                        'success' => false,
                        'message' => RWF::getLanguage()->get('index.room.error.1511')
                    );
                    RWF::getLanguage()->enableAutoHtmlEndocde();
                    return;
                } elseif ($e->getCode() == 1512) {

                    //kein Schaltserver erreichbar
                    $this->data = array(
                        'success' => false,
                        'message' => RWF::getLanguage()->get('index.room.error.1512')
                    );
                    RWF::getLanguage()->enableAutoHtmlEndocde();
                    return;
                }
            }
        }

        //aktuellen Status speicherb
        SwitchableEditor::getInstance()->updateState();

        //schalten erfolgreich
        $this->data = array(
            'success' => true
        );
        RWF::getLanguage()->enableAutoHtmlEndocde();
        return;
    }

}