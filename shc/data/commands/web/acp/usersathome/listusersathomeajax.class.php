<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\UserAtHome\UserAtHome;
use SHC\UserAtHome\UserAtHomeEditor;

/**
 * Zeigt eine Liste mit allen Benutzern zu Hause an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListUsersAtHomeAjax extends AjaxCommand {

    protected $premission = 'shc.acp.usersathomeManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('usersathomemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Sortierung speichern
        $r = $this->request;
        if($r->issetParam('req', Request::GET) && $r->getParam('req', Request::GET, DataTypeUtil::STRING) == 'saveorder') {

            //Meldung
            $message = new Message();

            //eingabedaten Pruefen
            $order = $r->getParam('userOrder', Request::POST);
            $filteredOrder = array();
            $valid = true;
            foreach($order as $id => $orderId) {

                $userAtHome = UserAtHomeEditor::getInstance()->getUserTaHomeById($id);
                if($userAtHome instanceof UserAtHome ) {

                    $filteredOrder[$id] = DataTypeUtil::convert($orderId, DataTypeUtil::INTEGER);
                } else {

                    //Fehlerhafte Eingaben
                    $valid = false;
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('form.message.typedWrong'));
                    break;
                }
            }

            //Speichern
            if($valid === true) {

                try {

                    UserAtHomeEditor::getInstance()->editOrder($filteredOrder);
                    UserAtHomeEditor::getInstance()->loadData();
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.success.orderUser'));
                } catch (\Exception $e) {

                    //Fehler beim speichern
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error.orderUser'));
                }
            }
            $tpl->assign('message', $message);
        }

        //User auflisten
        $tpl->assign('userAtHomeList', UserAtHomeEditor::getInstance()->listUsersAtHome(UserAtHomeEditor::SORT_BY_ORDER_ID));
        $this->data = $tpl->fetchString('listusersathome.html');
    }

}