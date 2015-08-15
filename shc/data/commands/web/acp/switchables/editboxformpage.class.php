<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\BoxForm;
use SHC\Sensor\SensorPointEditor;
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\View\Room\ViewHelperBox;
use SHC\View\Room\ViewHelperEditor;

/**
 * Listet die schaltbaren Elemente
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditBoxFormPage extends PageCommand {

    protected $template = 'boxform.html';

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

        //Box Objekt laden
        $boxId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $box = ViewHelperEditor::getInstance()->getBoxById($boxId);

        if ($box instanceof ViewHelperBox) {

            //Formular erstellen
            $boxForm = new BoxForm($box);
            $boxForm->setAction('index.php?app=shc&page=editboxform&id='. $box->getBoxId());
            $boxForm->addId('shc-view-form-editBox');

            if ($boxForm->isSubmitted() && $boxForm->validate()) {

                //Speichern
                $name = $boxForm->getElementByName('name')->getValue();
                $elements = $boxForm->getElementByName('elements')->getValues();

                $message = new Message();
                try {

                    //Box Speichern
                    ViewHelperEditor::getInstance()->editBox($boxId, $name, null, null);

                    //Anpassungen an den Elementen vornehmen
                    $boxes = ViewHelperEditor::getInstance()->listBoxes();
                    ViewHelperEditor::getInstance()->removeAllElementsFromBox($boxId);
                    foreach($elements as $element) {

                        $matches = array();
                        preg_match('#((element)-(\d+))|((sensor)-(.+))#', $element, $matches);

                        if(isset($matches[2]) && $matches[2] == 'element') {

                            //Element
                            $id = (int) $matches[3];
                            $elementObj = SwitchableEditor::getInstance()->getElementById($id);
                            if($elementObj instanceof Readable) {

                                ViewHelperEditor::getInstance()->addToBox($boxId, ViewHelperEditor::TYPE_READABLE, $id);
                                //Element aus den anderen Boxen entfernen
                                foreach($boxes as $listedBox) {

                                    //pruefen ob das Element in einer anderen Box registriert ist
                                    if($listedBox->isElementInBox($elementObj) && $listedBox->getBoxId() != $box->getBoxId()) {

                                        ViewHelperEditor::getInstance()->removeElementFromBox($listedBox->getBoxId(), ViewHelperEditor::TYPE_READABLE, $id);
                                    }
                                }
                            } elseif($elementObj instanceof Switchable) {

                                ViewHelperEditor::getInstance()->addToBox($boxId, ViewHelperEditor::TYPE_SWITCHABLE, $id);
                                //Element aus den anderen Boxen entfernen
                                foreach($boxes as $listedBox) {

                                    //pruefen ob das Element in einer anderen Box registriert ist
                                    if($listedBox->isElementInBox($elementObj) && $listedBox->getBoxId() != $box->getBoxId()) {

                                        ViewHelperEditor::getInstance()->removeElementFromBox($listedBox->getBoxId(), ViewHelperEditor::TYPE_SWITCHABLE, $id);
                                    }
                                }
                            }
                        } elseif(isset($matches[5]) && $matches[5] == 'sensor') {

                            //Sensor
                            if(preg_match('#^(\d+)|(\d\d-[\da-fA-F]{12})$#', $matches[6])) {

                                $id = (string) $matches[6];
                                $sensorObj = SensorPointEditor::getInstance()->getSensorById($id);
                                ViewHelperEditor::getInstance()->addToBox($boxId, ViewHelperEditor::TYPE_SENSOR, $id);
                                //Element aus den anderen Boxen entfernen
                                foreach ($boxes as $listedBox) {

                                    //pruefen ob das Element in einer anderen Box registriert ist
                                    if ($listedBox->isElementInBox($sensorObj) && $listedBox->getBoxId() != $box->getBoxId()) {

                                        ViewHelperEditor::getInstance()->removeElementFromBox($listedBox->getBoxId(), ViewHelperEditor::TYPE_SENSOR, $id);
                                    }
                                }
                            }
                        }
                    }

                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editbox.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addbox.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addbox.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('box', $box);
                $tpl->assign('boxForm', $boxForm);
            }
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