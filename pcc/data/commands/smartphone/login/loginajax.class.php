<?php

namespace PCC\Command\Smartphone;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Session\Login;
use RWF\Util\DataTypeUtil;

/**
 * Login
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LoginAjax extends AjaxCommand {

    /**
     * Daten verarbeiten
     */
    public function processData() {

        try {
            
            //Loginversuch
            Login::loginUser(
                    RWF::getRequest()->getParam('user', Request::POST, DataTypeUtil::PLAIN), 
                    RWF::getRequest()->getParam('password', Request::POST, DataTypeUtil::PLAIN), 
                    RWF::getRequest()->getParam('longTime', Request::POST, DataTypeUtil::BOOLEAN)
            );  
            
        } catch (\Exception $ex) {

            //Anmeldung Fehlgeschlagen
            RWF::getLanguage()->loadModul('index');
            $this->data['success'] = false;
            $this->data['message'] = RWF::getLanguage()->get('index.login.error');
            return;
        }
        
        //Anmeldung erfolgreich
        $this->data['success'] = true;
        $this->data['message'] = '';
        return;
    }

}
