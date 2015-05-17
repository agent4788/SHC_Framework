<?php

namespace MB\Core;

//Imports
use RWF\Core\RWF;
use RWF\Style\StyleEditor;
use RWF\User\User;

/**
 * Kernklasse (initialisiert die MovieBase)
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */
class MB extends RWF {

    /**
     * Version
     *
     * @var String
     */
    const VERSION = '2.2.0 RC 5';

    /**
     * Style
     *
     * @var \RWF\Style\Style
     */
    protected static $style = null;

    public function __construct() {

        //XML Initialisieren
        $this->initXml();

        //Basisklasse initalisieren
        parent::__construct();

        //SHC Initialisieren
        if (ACCESS_METHOD_HTTP) {

            //Template Ordner anmelden
            self::$template->addTemplateDir(PATH_MB . 'data/templates');
            $this->initStyle();
        }
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

        if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

            //Mobilen Style laden
            if (self::$visitor instanceof User && self::$visitor->getMobileStyle() != '') {

                $mobileStyle = self::$visitor->getMobileStyle();
            } else {

                $mobileStyle = self::getSetting('pcc.defaultMobileStyle');
            }
            self::$style = StyleEditor::getInstance()->getMobileStyle($mobileStyle);
        } elseif(defined('RWF_DEVICE') && RWF_DEVICE == 'web') {

            //Webstyle laden
            if (self::$visitor instanceof User && self::$visitor->getWebStyle() != '') {

                $webStyle = self::$visitor->getWebStyle();
            } else {

                $webStyle = self::getSetting('pcc.defaultStyle');
            }
            self::$style = StyleEditor::getInstance()->getWebStyle($webStyle);
        }
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