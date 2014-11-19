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
 * download eines Backups
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LoadBackupAjax extends AjaxCommand {

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
            $this->data = $tpl->fetchString('loadbackup.html');
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

        //Einstellungen Speichern
        if (RWF::getSettings() instanceof Settings) {

            RWF::getSettings()->finalize();
        }

        //Sessionobjekt abschliesen
        if (RWF::getSession() instanceof Session) {

            RWF::getSession()->finalize();
        }
        exit(0);
    }

}