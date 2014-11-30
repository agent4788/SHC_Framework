<?php

namespace RWF\Session;

//Imports
use RWF\Core\RWF;
use RWF\User\User;
use RWF\User\UserEditor;
use RWF\Request\Cookie;

/**
 * Login/Logout
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class Login {

    /**
     * meldet den Beutzer an
     * 
     * @param  String  $name          Benutzername
     * @param  String  $password      Passwort
     * @param  Boolean $longTimeLogin Langzeit Login
     * @return Boolean
     * @throws \Exception
     */
    public static final function loginUser($name, $password, $longTimeLogin = false) {

        //Benutzer suchen
        $user = UserEditor::getInstance()->getUserByName($name);
        if ($user instanceof User) {

            //Passwort pruefen
            $authCode = $user->checkPasswordHash($password);
            if ($authCode !== null) {

                //Session aktualisieren
                RWF::getSession()->regenerateSessionId();
                RWF::getSession()->set('authCode', $authCode);

                //Langzeit Login (kann per EInstellung deaktiviert werden
                if ($longTimeLogin == true && RWF::getSetting('rwf.session.allowLongTimeLogin') === true) {

                    //neues Cookie sezen
                    $cookie = new Cookie(RWF_COOKIE_PREFIX, 'authCode', $authCode);
                    $cookie->setTimeByInterval(0, 6);
                    RWF::getResponse()->addCookie($cookie);
                }
                return true;
            }
        }
        throw new \Exception('Benutzername oder Passwort falsch', 1114);
    }

    /**
     * meldet den Benutzer ab
     * 
     * @return Boolean
     */
    public static final function logoutUser() {

        $cookie = RWF::getRequest()->getCookie('authCode');
        if ($cookie instanceof Cookie) {

            $cookie->remove();
        }
        RWF::getSession()->remove('authCode');
        RWF::getSession()->regenerateSessionId();
        return true;
    }

}
