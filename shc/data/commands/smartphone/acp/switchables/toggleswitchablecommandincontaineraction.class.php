<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Switchable\AbstractSwitchable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\Countdown;

/**
 * schaltbares Element zu Ereignis Hinzufügen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ToggleSwitchableCommandinContainerAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.switchableManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&m&page=manageswitchablecontainers&id=';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //element hinzufuegen
        $elementId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $element = SwitchableEditor::getInstance()->getElementById($elementId);
        $switchableElementId = RWF::getRequest()->getParam('element', Request::GET, DataTypeUtil::INTEGER);
        $this->location .= $elementId;

        //Eingaben pruefen
        $error = false;
        $message = new Message();
        $switchableElementObject = SwitchableEditor::getInstance()->getElementById($switchableElementId);
        if (!$switchableElementObject instanceof Switchable) {

            $message->setType(Message::ERROR);
            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.error.id'));
            $error = true;
        }

        //Element hinzufuegen
        if ($error === false) {

            try {

                //Speichern
                $newCommand = AbstractSwitchable::STATE_OFF;
                foreach($element->listSwitchables() as $switchable) {

                    if($switchable['object'] == $switchableElementObject) {

                        if($switchable['command'] == AbstractSwitchable::STATE_ON) {

                            $newCommand = AbstractSwitchable::STATE_OFF;
                        } else {

                            $newCommand = AbstractSwitchable::STATE_ON;
                        }
                    }
                }
                if ($element instanceof Activity) {

                    SwitchableEditor::getInstance()->setActivitySwitchableCommand($element->getId(), $switchableElementId, $newCommand);
                } elseif ($element instanceof Countdown) {

                    SwitchableEditor::getInstance()->setCountdownSwitchableCommand($element->getId(), $switchableElementId, $newCommand);
                }
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.success'));
            } catch (\Exception $e) {

                if($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.error'));
                }
            }
        }
        RWF::getSession()->setMessage($message);
    }
}