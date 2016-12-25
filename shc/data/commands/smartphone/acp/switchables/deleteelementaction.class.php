<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteElementAction extends ActionCommand {

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
    protected $location = 'index.php?app=shc&m&page=listswitchables';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchablemanagement', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Element Objekt laden
        $elementId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $element = SwitchableEditor::getInstance()->getElementById($elementId);

        //pruefen ob das Element existiert
        if(!$element instanceof Switchable && !$element instanceof Readable) {

            //Ungueltige ID
            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            SwitchableEditor::getInstance()->removeSwitchable($elementId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.delete.success'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.delete.error.1102'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.delete.error'));
            }
        }
        RWF::getSession()->setMessage($message);
    }
}