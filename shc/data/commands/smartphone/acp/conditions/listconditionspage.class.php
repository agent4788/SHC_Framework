<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use SHC\Condition\ConditionEditor;
use SHC\Core\SHC;

/**
 * Zeigt eine Liste mit allen Schaltservern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListConditionsPage extends PageCommand {

    protected $template = 'listconditions.html';

    protected $requiredPremission = 'shc.acp.conditionsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'conditionmanagement', 'switchablemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=acp');
        $tpl->assign('device', SHC_DETECTED_DEVICE);
        if(RWF::getSession()->getMessage() != null) {
            $tpl->assign('message', RWF::getSession()->getMessage());
            RWF::getSession()->removeMessage();
        }

        //Typ zuruecksetzen falls vorhanden
        if(RWF::getSession()->issetVar('type')) {

            RWF::getSession()->remove('type');
        }

        //Schaltserver Liste
        $tpl->assign('conditionList', ConditionEditor::getInstance()->listConditions(ConditionEditor::SORT_BY_NAME));
    }

}