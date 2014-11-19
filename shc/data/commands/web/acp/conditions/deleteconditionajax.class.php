<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Condition\AbstractCondition;
use SHC\Condition\ConditionEditor;

/**
 * loescht eine Bedingung
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteConditionAjax extends AjaxCommand {

    protected $premission = 'shc.acp.conditionsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('conditionmanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Bedingung Objekt laden
        $conditionId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $condition = ConditionEditor::getInstance()->getConditionByID($conditionId);

        //pruefen ob das Element existiert
        if(!$condition instanceof AbstractCondition) {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.id')));
            $this->data = $tpl->fetchString('deletecondition.html');
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            ConditionEditor::getInstance()->removeCondition($conditionId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.delete.success'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.delete.error.1102'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.delete.error'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deletecondition.html');
    }

}