<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Util\String;
use SHC\UserAtHome\UserAtHome;
use SHC\UserAtHome\UserAtHomeEditor;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserAtHomeUpdateAjax extends AjaxCommand {

    protected $premission = 'shc.ucp.viewUserAtHome';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Objekte Laden
        $usersAtHome = UserAtHomeEditor::getInstance()->listUsersAtHome(UserAtHomeEditor::SORT_BY_ORDER_ID);
        $data['data'] = '';
        $data['success'] = false;

        //Liste erstellen
        $html = '<li data-role="list-divider" role="heading" data-theme="b">'. RWF::getLanguage()->get('index.userAtHome.boxTitle') .'</li>';
        $online = '';
        $offline = '';
        //Benutzer zu Hause auflisten
        foreach ($usersAtHome as $userAtHome) {

            /* @var $userAtHome \SHC\UserAtHome\UserAtHome */
            if($userAtHome->getState() == UserAtHome::STATE_ONLINE) {

                $online .= '<li>'. String::encodeHTML($userAtHome->getName()) .'</li>';
            } else {

                $offline .= '<li>'. String::encodeHTML($userAtHome->getName()) .'</li>';
            }

            $html .= '<li data-role="list-divider">'. RWF::getLanguage()->get('index.userAtHome.online') .'</li>';
            $html .= $online;
            $html .= '<li data-role="list-divider">'. RWF::getLanguage()->get('index.userAtHome.offline') .'</li>';
            $html .= $offline;
        }
        $data['data'] = $html;
        $data['success'] = true;
        $this->data = $data;
    }

}