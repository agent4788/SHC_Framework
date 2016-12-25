<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\View\Room\ViewHelperBox;
use SHC\View\Room\ViewHelperEditor;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteBoxAction extends ActionCommand {

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
    protected $location = 'index.php?app=shc&page=listswitchables';

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

        $boxId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $box = ViewHelperEditor::getInstance()->getBoxById($boxId);

        //pruefen ob das Element existiert
        if(!$box instanceof ViewHelperBox) {

            //Ungueltige ID
            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));
            return;
        }

        //Box loeschen
        $message = new Message();
        try {

            ViewHelperEditor::getInstance()->removeBox($boxId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteBox.success'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteBox.error.1102'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteBox.error'));
            }
        }
        RWF::getSession()->setMessage($message);
    }
}