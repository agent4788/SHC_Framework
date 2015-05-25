<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
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
class AddSwitchableToContainerAction extends ActionCommand {

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
    protected $location = 'index.php?app=shc&page=manageswitchablecontainers&id=';

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
        $switchCommand = RWF::getRequest()->getParam('switchCommand', Request::GET, DataTypeUtil::INTEGER);
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

        if (!in_array($switchCommand, array('0', '1'))) {

            $message->setType(Message::ERROR);
            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.error.command'));
            $error = true;
        }

        //Element hinzufuegen
        if ($error === false) {

            try {

                //Speichern
                if ($element instanceof Activity) {

                    SwitchableEditor::getInstance()->addSwitchableToActivity($element->getId(), $switchableElementId, $switchCommand);
                } elseif ($element instanceof Countdown) {

                    SwitchableEditor::getInstance()->addSwitchableToCountdown($element->getId(), $switchableElementId, $switchCommand);
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