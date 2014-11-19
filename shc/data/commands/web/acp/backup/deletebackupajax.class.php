<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Backup\Backup;
use SHC\Backup\BackupEditor;

/**
 * loescht ein Backup
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteBackupAjax extends AjaxCommand {

    protected $premission = 'shc.acp.backupsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('backupsmanagement');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Backuppfad setzen
        BackupEditor::getInstance()->setPath(PATH_SHC_BACKUP);

        //Backup Objekt laden
        $hash = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::MD5);
        $backup = BackupEditor::getInstance()->getBackupByMD5Hash($hash);

        //pruefen ob das Backup existiert
        if(!$backup instanceof Backup) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.backupsManagement.error.hash')));
            $this->data = $tpl->fetchString('deletebackup.html');
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        if(BackupEditor::getInstance()->removeBackup($hash)) {

            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.backupsManagement.success.deleteBackup'));
        } else {

            $message->setType(Message::ERROR);
            $message->setMessage(RWF::getLanguage()->get('acp.backupsManagement.error.deleteBackup'));
        }

        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deletebackup.html');
    }

}