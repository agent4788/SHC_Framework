<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\Condition\ConditionEditor;
use SHC\Room\RoomEditor;


/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListConditionsAjax extends AjaxCommand {

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

        //Typ zuruecksetzen falls vorhanden
        if(RWF::getSession()->issetVar('type')) {

            RWF::getSession()->remove('type');
        }

        $tpl = RWF::getTemplate();
        $tpl->assign('conditionList', ConditionEditor::getInstance()->listConditions(ConditionEditor::SORT_BY_NAME));
        $this->data = $tpl->fetchString('listconditions.html');
    }

}