<?php

namespace SHC\Command\Web;

//Imports
use RWF\Date\DateTime;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\Message;
use SHC\Core\SHC;

/**
 * Datenbankverwaltung
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DatabaseAjax extends AjaxCommand {

    protected $premission = 'shc.acp.databaseManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('acpindex', 'database');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = SHC::getTemplate();
        $db = SHC::getDatabase();

        if($this->request->issetParam('dump', Request::GET)) {

            $message = new Message();
            if($db->bgsave()) {

                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(SHC::getLanguage()->get('acp.database.dump.success'));
            } else {

                $message->setType(Message::ERROR);
                $message->setMessage(SHC::getLanguage()->get('acp.database.dump.error'));
            }
            $tpl->assign('message', $message);
        }

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

        $this->data = $tpl->fetchString('database.html');
    }

}