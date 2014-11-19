<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\Timer\SwitchPointEditor;

/**
 * Zeigt eine Liste mit allen Schaltpunkten an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListSwitchPointsAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchpointsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchpointsmanagment', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();
        $tpl->assign('switchPointsList', SwitchPointEditor::getInstance()->listSwitchPoints(SwitchPointEditor::SORT_BY_NAME));
        $this->data = $tpl->fetchString('listswitchpoints.html');
    }

}