<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\Backup\BackupEditor;

/**
 * listet alle Backups auf
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListBackupsAjax extends AjaxCommand {

    protected $premission = 'shc.acp.backupsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('backupsmanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();
        $tpl->assign('backupList', BackupEditor::getInstance()->setPath(PATH_SHC_BACKUP)->listBackups(BackupEditor::SORT_BY_NAME));
        $this->data = $tpl->fetchString('listbackups.html');
    }

}