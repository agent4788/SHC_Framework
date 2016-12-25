<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Util\Message;
use SHC\Core\SHC;

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
    protected $requiredPremission = 'shc.acp.databaseManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&m&page=database';

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

        $db = SHC::getDatabase();
        $message = new Message();
        if($db->bgsave()) {

            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(SHC::getLanguage()->get('acp.database.dump.success'));
        } else {

            $message->setType(Message::ERROR);
            $message->setMessage(SHC::getLanguage()->get('acp.database.dump.error'));
        }
        RWF::getSession()->setMessage($message);
    }
}