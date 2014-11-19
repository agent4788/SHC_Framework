<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\Sensor\SensorPointEditor;

/**
 * liste mit allen Sensorpunkten
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListSensorPointsAjax extends AjaxCommand {

    protected $premission = 'shc.acp.sensorpointsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('sensorpointsmanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();
        $tpl->assign('sensorPointsList', SensorPointEditor::getInstance()->listSensorPoints(SensorPointEditor::SORT_BY_NAME));
        $this->data = $tpl->fetchString('listsensorpoints.html');
    }

}