<?php

namespace SHC\Command\Web;

//Imports
use RWF\Backup\Backup;
use RWF\Backup\BackupEditor;
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Session\Session;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;

/**
 * download eines Backups
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LoadBackupAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.backupsManagement';

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

        //Backup Download
        //Download initalisieren
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="' . $backup->getFileName() . '"');
        header('Content-Length: ' . $backup->getSize());
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');

        //Daten senden
        $fh = fopen($backup->getPath() . $backup->getFileName(), 'rb');

        //Daten Senden
        while (!feof($fh)) {

            echo fread($fh, 2048);
            flush();
        }

        //Datei schliesen
        fclose($fh);

        //Anwendung vorzeitig beenden

        //Sessionobjekt abschliesen
        if (RWF::getSession() instanceof Session) {

            RWF::getSession()->finalize();
        }
        exit(0);
    }
}