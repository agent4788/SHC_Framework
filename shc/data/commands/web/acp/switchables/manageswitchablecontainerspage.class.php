<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\FormElements\SwitchCommandChooser;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\EdimaxSocket;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\VirtualSocket;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * Listet die schaltbaren Elemente
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ManageSwitchableContainersPage extends PageCommand {

    protected $template = 'manageswitchablecontainers.html';

    protected $requiredPremission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchablemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Header Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());

        //Meldungen
        if(RWF::getSession()->getMessage() != null) {
            $tpl->assign('message', RWF::getSession()->getMessage());
            RWF::getSession()->removeMessage();
        }

        //Element Objekt laden
        $elementId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $element = SwitchableEditor::getInstance()->getElementById($elementId);

        if($element instanceof Activity || $element instanceof Countdown) {

            //Formularfelder erstellen
            $elementChooser = new Select('element');
            $values = array();
            foreach(SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_BY_NAME) as $switchableElement) {

                if(
                    $switchableElement instanceof RadioSocket
                    || $switchableElement instanceof RpiGpioOutput
                    || $switchableElement instanceof WakeOnLan
                    || $switchableElement instanceof Shutdown
                    || $switchableElement instanceof Reboot
                    || $switchableElement instanceof Script
                    || $switchableElement instanceof AvmSocket
                    || $switchableElement instanceof FritzBox
                    || $switchableElement instanceof EdimaxSocket
                    || $switchableElement instanceof VirtualSocket
                ) {

                    //pruefen ob Element schon registriert
                    $found = false;
                    foreach($element->listSwitchables() as $switchable) {

                        if($switchable['object'] == $switchableElement) {

                            $found = true;
                            break;
                        }
                    }
                    if($found == true) {

                        //wenn schon registriert Element ueberspringen
                        continue;
                    }

                    $type = $switchableElement->getTypeName();
                    $values[$switchableElement->getId()] = $switchableElement->getName() .' ('. $type .') ['. $switchableElement->getNamedRoomList(true) .']';
                }
            }
            $elementChooser->setValues($values);

            //Schaltbefehl
            $switchCommand = new SwitchCommandChooser('switchCommand');

            //Elemente Liste Template Anzeigen
            $tpl->assign('SwitchableContainer', $element);
            $tpl->assign('elementChooser', $elementChooser);
            $tpl->assign('switchCommand', $switchCommand);
            $tpl->assign('elementList', $element->listSwitchables());
        } else {

            //Ungueltige ID
            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
            $this->response->setBody('');
            $this->template = '';
        }
    }

}