<?php

namespace PCC\Command\Smartphone;

//Imports
use RWF\Backup\BackupEditor;
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Util\Message;

/**
 * erstellt ein neues Backup
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class MakeBackupAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'pcc.acp.backupsManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=pcc&m&page=listbackups';

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

        $message = new Message();
        if(BackupEditor::getInstance()->setPath(PATH_RWF_BACKUP)->makeBackup(true)) {

            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.backupsManagement.success.makeBackup'));
        } else {

            $message->setType(Message::ERROR);
            $message->setMessage(RWF::getLanguage()->get('acp.backupsManagement.error.makeBackup'));
        }
        RWF::getSession()->setMessage($message);
    }
}