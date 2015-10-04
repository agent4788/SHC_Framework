<?php

namespace MB\Core;

//Imports
use MB\Database\NoSQL\Redis;
use RWF\Core\RWF;
use RWF\Session\Session;
use RWF\Settings\Settings;
use RWF\Style\StyleEditor;
use RWF\User\User;
use RWF\User\UserEditor;

/**
 * Kernklasse (initialisiert die MovieBase)
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.3.0-0
 * @version    2.3.0-0
 */
class MB extends RWF {

    /**
     * Version
     *
     * @var String
     */
    const VERSION = '2.3.0 alpha 1';

    /**
     * Style
     *
     * @var \RWF\Style\Style
     */
    protected static $style = null;

    public function __construct() {

        //XML Initialisieren
        $this->initXml();

        //Berechtigungen initialisieren
        $this->initPermissions();

        //Basisklasse initalisieren
        parent::__construct();

        //pruefen ob App installiert ist
        if (ACCESS_METHOD_HTTP) {

            $found = false;
            foreach(self::$appList as $app) {

                if($app['app'] == 'mb') {

                    $found = true;
                    break;
                }
            }

            if($found === false) {

                throw new \Exception('Die App "Movie Base" ist nicht installiert', 1013);
            }
        }

        //MB Initialisieren
        if (ACCESS_METHOD_HTTP) {

            //Template Ordner anmelden
            self::$template->addTemplateDir(PATH_MB . 'data/templates');
            $this->initStyle();
        }
    }

    /**
     * initialisiert die Einstellungen
     */
    protected function initSettings() {

        parent::initSettings();
        $settings = self::$settings;

        //Movie Base Einstellungen hinzufuegen
        $settings->addSetting('mb.title', Settings::TYPE_STRING, 'Movie Base 2.3');
        $settings->addSetting('mb.defaultStyle', Settings::TYPE_STRING, 'redmond');
    }

    /**
     * initialisiert die Berechtigungen
     */
    protected function initPermissions() {

        $userEditor = UserEditor::getInstance();

        //Benutzerrechte
        $userEditor->addPermission('mb.ucp.viewMovies', true);
        $userEditor->addPermission('mb.ucp.viewMovieCollections', true);
        $userEditor->addPermission('mb.ucp.viewStatistics', true);

        //Admin Rechte
        $userEditor->addPermission('mb.acp.menue', false);
        $userEditor->addPermission('mb.acp.userManagement', false);
        $userEditor->addPermission('mb.acp.settings', false);
        $userEditor->addPermission('mb.acp.databaseManagement', false);
        $userEditor->addPermission('mb.acp.backupsManagement', false);
    }

    /**
     * XML Verwaltung initialisieren
     */
    protected function initXml() {


    }

    /**
     * initialisiert den Style
     */
    protected function initStyle() {

        //Webstyle laden
        if (self::$visitor instanceof User && self::$visitor->getWebStyle() != '') {

            $webStyle = self::$visitor->getWebStyle();
        } else {

            $webStyle = self::getSetting('mb.defaultStyle');
        }
        self::$style = StyleEditor::getInstance()->getWebStyle($webStyle);
    }

    /**
     * gibt den Style zurueck
     *
     * @return \RWF\Style\Style
     */
    public static function getStyle() {

        return self::$style;
    }
}