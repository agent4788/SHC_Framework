<?php

namespace PCC\Command\Smartphone;

//Imports
use PCC\Core\PCC;
use PCC\Form\Forms\UserGroupForm;
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\User\UserEditor;
use RWF\Util\Message;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddGroupFormPage extends PageCommand {

    protected $template = 'groupform.html';

    protected $requiredPremission = 'pcc.acp.userManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'usermanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Headline Daten
        $tpl->assign('apps', PCC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', PCC::getStyle());
        $tpl->assign('user', PCC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=pcc&m&page=listusers');
        $tpl->assign('device', PCC_DETECTED_DEVICE);

        //Formular erstellen
        $groupForm = new UserGroupForm();
        $groupForm->setView(UserGroupForm::SMARTPHONE_VIEW);
        $groupForm->setAction('index.php?app=pcc&m&page=addgroupform');
        $groupForm->addId('shc-view-form-addGroup');

        if(!$groupForm->isSubmitted() || ($groupForm->isSubmitted() && !$groupForm->validate() === true)) {

            $tpl->assign('groupForm', $groupForm);
        } else {

            //Speichern
            $name = $groupForm->getElementByName('name')->getValue();
            $description = $groupForm->getElementByName('description')->getValue();
            $premissions = array();
            foreach(UserEditor::getInstance()->getUserGroupById(1)->listPermissions() as $premissionName => $premissionValue) {

                if(preg_match('#^pcc\.#', $premissionName)) {

                    $value = $groupForm->getElementByName(str_replace('.', '_', $premissionName))->getValue();
                    $premissions[$premissionName] = ($value === null ? false : $value);
                }
            }

            $message = new Message();
            try {

                UserEditor::getInstance()->addUserGroup($name, $description, $premissions);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.success.addGroup'));
            } catch(\Exception $e) {

                if($e->getCode() == 1112) {

                    //Benutzername schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1112'));
                }  elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1102.group'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.group'));
                }
            }
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=pcc&m&page=listgroups');
            $this->response->setBody('');
            $this->template = '';
        }

    }
}