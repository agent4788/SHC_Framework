<?php

namespace PCC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;

/**
 * Zeigt die Daten des Systems an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DataAjax extends AjaxCommand {

    protected $requiredPremission = 'pcc.ucp.viewSysData';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template anzeigen
        $tpl = RWF::getTemplate();
        $this->data = $tpl->fetchString('data.html');
    }
}