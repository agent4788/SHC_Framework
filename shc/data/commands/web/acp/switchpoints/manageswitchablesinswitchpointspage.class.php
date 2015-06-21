<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use SHC\Core\SHC;
use RWF\Form\FormElements\Select;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Condition\ConditionEditor;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Timer\SwitchPoint;
use SHC\Timer\SwitchPointEditor;

/**
 * Zeigt eine Liste mit allen Schaltservern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ManageSwitchablesInSwitchPointsPage extends PageCommand {

    protected $template = 'manageswitchablesinswitchpoints.html';

    protected $requiredPremission = 'shc.acp.switchpointsManagement';

    protected $languageModules = array('switchablemanagement', 'conditionmanagement', 'acpindex', 'form', 'switchpointsmanagment', 'index');

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

        //Schaltpunkt Objekt laden
        $switchPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchPoint = SwitchPointEditor::getInstance()->getSwitchPointById($switchPointId);

        if ($switchPoint instanceof SwitchPoint) {

            //Formularfelder erstellen

            //Bedingungen
            $conditionChooser = new Select('condition');
            $values = array();
            foreach(ConditionEditor::getInstance()->listConditions(ConditionEditor::SORT_BY_NAME) as $condition) {

                //pruefen ob Bedingung schon registriert
                $found = false;
                foreach($switchPoint->listConditions() as $compareCondition) {

                    if($compareCondition == $condition) {

                        $found = true;
                        break;
                    }
                }
                if($found == true) {

                    //wenn schon registriert Bedingung ueberspringen
                    continue;
                }

                RWF::getLanguage()->disableAutoHtmlEndocde();
                $type = $condition->getTypeName();
                RWF::getLanguage()->enableAutoHtmlEndocde();
                $values[$condition->getId()] = $condition->getName() .' ('. $type .')';
            }
            $conditionChooser->setValues($values);

            //Schaltbare Elemente fuer den Schaltpunkt ermitteln
            $switchablesInSwitchPoint = array();
            $switchablesOutSwitchPoint = array();
            $switchables = SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_BY_NAME);
            foreach($switchables as $switchable) {

                /* @var $switchable \SHC\Switchable\Switchable */
                if($switchable instanceof Switchable) {

                    $switchPoints = $switchable->listSwitchPoints();
                    $found = false;
                    foreach($switchPoints as $switchableSwitchPoint) {

                        if($switchableSwitchPoint->getId() == $switchPoint->getId()) {

                            $switchablesInSwitchPoint[] = $switchable;
                            $found = true;
                            break;
                        }
                    }

                    if($found === false) {

                        $switchablesOutSwitchPoint[] = $switchable;
                    }
                }
            }

            //schaltbare Elemente
            $elementChooser = new Select('element');
            $values = array();
            foreach($switchablesOutSwitchPoint as $switchableElement) {

                if($switchableElement instanceof Switchable) {

                    RWF::getLanguage()->disableAutoHtmlEndocde();
                    $type = $switchableElement->getTypeName();
                    RWF::getLanguage()->enableAutoHtmlEndocde();
                    $values[$switchableElement->getId()] = $switchableElement->getName() .' ('. $type .') ['. $switchableElement->getNamedRoomList(true) .']';
                }
            }
            $elementChooser->setValues($values);

            //Elemente Liste Template Anzeigen
            $tpl->assign('switchPoint', $switchPoint);
            $tpl->assign('conditionChooser', $conditionChooser);
            $tpl->assign('elementChooser', $elementChooser);
            $tpl->assign('elementList', $switchablesInSwitchPoint);
            $tpl->assign('conditionList', $switchPoint->listConditions());
        } else {

            //Ungueltige ID
            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchpointsManagment.form.error.id')));

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&page=listswitchpoints');
            $this->response->setBody('');
            $this->template = '';
        }
    }

}