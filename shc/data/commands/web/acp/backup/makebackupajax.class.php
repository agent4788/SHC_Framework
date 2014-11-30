<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Util\Message;
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
class MakeBackupAjax extends AjaxCommand {

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

        $message = new Message();
        if(BackupEditor::getInstance()->setPath(PATH_SHC_BACKUP)->makeBackup(false)) {

            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.backupsManagement.success.makeBackup'));
        } else {

            $message->setType(Message::ERROR);
            $message->setMessage(RWF::getLanguage()->get('acp.backupsManagement.error.makeBackup'));
        }

        $tpl = RWF::getTemplate();
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('makebackup.html');
    }

}