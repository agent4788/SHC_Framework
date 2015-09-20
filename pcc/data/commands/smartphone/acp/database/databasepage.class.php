<?php

namespace PCC\Command\Smartphone;

//Imports
use PCC\Core\PCC;
use RWF\Date\DateTime;
use RWF\Request\Commands\PageCommand;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DatabasePage extends PageCommand {

    protected $template = 'database.html';

    protected $requiredPremission = 'pcc.acp.databaseManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('acpindex', 'database', 'index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = PCC::getTemplate();
        $db = PCC::getDatabase();

        //Headline Daten
        $tpl->assign('apps', PCC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', PCC::getStyle());
        $tpl->assign('user', PCC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=pcc&m&page=acp');
        $tpl->assign('device', PCC_DETECTED_DEVICE);
        if(PCC::getSession()->getMessage() != null) {
            $tpl->assign('message', PCC::getSession()->getMessage());
            PCC::getSession()->removeMessage();
        }

        //Daten
        $info = $db->info();
        if(isset($info['rdb_last_save_time'])){

            $lastSave = $info['rdb_last_save_time'];
        } elseif($info['last_save_time']) {

            $lastSave = $info['last_save_time'];
        } else {

            $lastSave = 0;
        }
        $lastSaveDate = DateTime::now();
        $lastSaveDate->setTimestamp($lastSave);

        $tpl->assign('info', $info);
        $tpl->assign('lastSaveDate', $lastSaveDate);
    }

}