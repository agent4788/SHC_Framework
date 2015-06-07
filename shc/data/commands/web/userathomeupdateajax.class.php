<?php

namespace SHC\Command\Web;

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

    protected $requiredPremission = 'shc.ucp.viewUserAtHome';

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

        $usersAtHome = UserAtHomeEditor::getInstance()->listUsersAtHome(UserAtHomeEditor::SORT_BY_ORDER_ID);

        $html = '';
        //Benutzer zu Hause auflisten
        foreach ($usersAtHome as $userAtHome) {

            /* @var $userAtHome \SHC\UserAtHome\UserAtHome */
            if($userAtHome->isEnabled() && $userAtHome->isVisible()) {

                $html .= '<div class="shc-view-userAtHome-icon" title="' . ($userAtHome->getState() == UserAtHome::STATE_ONLINE ? RWF::getLanguage()->get('index.userAtHome.online') : RWF::getLanguage()->get('index.userAtHome.offline')) . '">';
                $html .= '<span class="' . ($userAtHome->getState() == UserAtHome::STATE_ONLINE ? 'shc-view-userAtHome-icon-online' : 'shc-view-userAtHome-icon-offline') . '"></span>';
                $html .= '<span class="shc-view-userAtHome-icon-text">' . String::encodeHTML($userAtHome->getName()) . '</span>';
                $html .= '</div>';
            }
        }
        $this->data = $html;
    }

}