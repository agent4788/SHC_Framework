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
class DeleteSwitchableFormContainerAction extends ActionCommand {

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

                //loeschen
                if ($element instanceof Activity) {

                    SwitchableEditor::getInstance()->removeSwitchableFromActivity($element->getId(), $switchableElementId);
                } elseif ($element instanceof Countdown) {

                    SwitchableEditor::getInstance()->removeSwitchableFromCountdown($element->getId(), $switchableElementId);
                }
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteElementFromActivity.success'));
            } catch (\Exception $e) {

                if($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteElementFromActivity.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteElementFromActivity.error'));
                }
            }
        }
        RWF::getSession()->setMessage($message);
    }
}