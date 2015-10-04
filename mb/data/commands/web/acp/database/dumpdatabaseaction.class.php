<?php

namespace MB\Command\Web;

//Imports
use MB\Core\MB;
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Util\Message;

/**
 * erstellt ein Ba
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DumpDatabaseAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'mb.acp.databaseManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=mb&page=database';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'database', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        $db = MB::getDatabase();
        $message = new Message();
        if($db->bgsave()) {

            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(MB::getLanguage()->get('acp.database.dump.success'));
        } else {

            $message->setType(Message::ERROR);
            $message->setMessage(MB::getLanguage()->get('acp.database.dump.error'));
        }
        RWF::getSession()->setMessage($message);
    }
}