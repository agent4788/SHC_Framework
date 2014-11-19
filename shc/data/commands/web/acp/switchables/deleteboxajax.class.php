<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\View\Room\ViewHelperBox;
use SHC\View\Room\ViewHelperEditor;


/**
 * loescht ein Element
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteBoxAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        $boxId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $box = ViewHelperEditor::getInstance()->getBoxById($boxId);

        //pruefen ob das Element existiert
        if(!$box instanceof ViewHelperBox) {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));
            $this->data = $tpl->fetchString('deletebox.html');
            return;
        }

        //Box loeschen
        $message = new Message();
        try {

            ViewHelperEditor::getInstance()->removeBox($boxId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteBox.success'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteBox.error.1102'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteBox.error'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deletebox.html');
    }

}