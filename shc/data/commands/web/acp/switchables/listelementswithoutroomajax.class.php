<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\Sensor\SensorPointEditor;
use SHC\Switchable\SwitchableEditor;

/**
 * Zeigt eine Liste mit allen Elementen an die keinem Raum zugeordnet sind
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListElementsWithoutRoomAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Typ zuruecksetzen falls vorhanden
        if(RWF::getSession()->issetVar('type')) {

            RWF::getSession()->remove('type');
        }

        //Template vorbereiten und Anzeigen
        $tpl = RWF::getTemplate();
        $tpl->assign('switchables', SwitchableEditor::getInstance()->listElementsWithoutRoom(SwitchableEditor::SORT_BY_NAME));
        $tpl->assign('sensors', SensorPointEditor::getInstance()->listSensorsWithoutRoom(SensorPointEditor::SORT_BY_NAME));
        $this->data = $tpl->fetchString('listelementswithoutroom.html');
    }

}