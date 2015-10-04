<?php

namespace MB\Command\Web;

//Imports
use MB\Core\MB;
use RWF\Date\DateTime;
use RWF\Request\Commands\PageCommand;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DatabasePage extends PageCommand {

    protected $template = 'database.html';

    protected $requiredPremission = 'mb.acp.databaseManagement';

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

        $tpl = MB::getTemplate();
        $db = MB::getDatabase();

        //Header Daten
        $tpl->assign('apps', MB::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', MB::getStyle());
        $tpl->assign('user', MB::getVisitor());
        if(MB::getSession()->getMessage() != null) {
            $tpl->assign('message', MB::getSession()->getMessage());
            MB::getSession()->removeMessage();
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