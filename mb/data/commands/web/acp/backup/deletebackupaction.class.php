<?php

namespace MB\Command\Web;

//Imports
use RWF\Backup\Backup;
use RWF\Backup\BackupEditor;
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;

/**
 * loescht ein Backup
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteBackupAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'mb.acp.backupsManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=mb&page=listbackups';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'backupsmanagement', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Backuppfad setzen
        BackupEditor::getInstance()->setPath(PATH_RWF_BACKUP);

        //Backup Objekt laden
        $hash = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::MD5);
        $backup = BackupEditor::getInstance()->getBackupByMD5Hash($hash);

        //pruefen ob das Backup existiert
        if(!$backup instanceof Backup) {

            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.backupsManagement.error.hash')));
            return;
        }

        //Backup loeschen
        $message = new Message();
        if(BackupEditor::getInstance()->removeBackup($hash)) {

            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.backupsManagement.success.deleteBackup'));
        } else {

            $message->setType(Message::ERROR);
            $message->setMessage(RWF::getLanguage()->get('acp.backupsManagement.error.deleteBackup'));
        }

        RWF::getSession()->setMessage($message);
    }
}