<?php

namespace RWF\Style;

//Imports
use RWF\Util\FileUtil;

/**
 * Verwaltungsklasse fuer Styles
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class StyleEditor {

    /**
     * Liste mit allen Styles
     *  
     * @var Array
     */
    protected $styleList = array();

    /**
     * Liste mit den Style Objekten
     * 
     * @var Array 
     */
    protected $styleObjectList = array();

    /**
     * Singleton Instanz
     * 
     * @var \RWF\Style\StyleEditor  
     */
    protected static $instance = null;

    /**
     * gibt eine Liste mit allen verfuegbaren Styles zurueck
     * 
     * @param  Boolean $mobilStyle Gibt an ob nach Mobilen oder Web STyles gesucht werden soll
     * @return Array
     */
    public function listStyles($mobilStyle = false) {

        //Styles schon geladen, Liste zurueckgeben
        if(count($this->styleObjectList) > 0) {
            
            return $this->styleObjectList[($mobilStyle == true ? 'mobile' : 'web')];
        }
        
        //Styles Laden
        if ($mobilStyle == true) {

            //Styles fuer die Mobiloberflaeche suchen
            $rwfAllStylePath = PATH_RWF . 'inc/style/mobile/all/';
            $rwfStylePath = PATH_RWF . 'inc/style/mobile/';
            $appStyleAllPath = PATH_BASE . '/' . APP_NAME . '/inc/style/mobile/all/';
            $appStylePath = PATH_BASE . '/' . APP_NAME . '/inc/style/mobile/';
        } else {

            //Styles fuer die Weboberflaeche suchen
            $rwfAllStylePath = PATH_RWF . 'inc/style/web/all/';
            $rwfStylePath = PATH_RWF . 'inc/style/web/';
            $appStyleAllPath = PATH_BASE . '/' . APP_NAME . '/inc/style/web/all/';
            $appStylePath = PATH_BASE . '/' . APP_NAME . '/inc/style/web/';
        }
        $rwfAllStyles = FileUtil::listDirectoryFiles($rwfAllStylePath, false, true, true);
        $rwfStyles = FileUtil::listDirectoryFiles($rwfStylePath, false, true, true);
        $appAllStyles = FileUtil::listDirectoryFiles($appStyleAllPath, false, true, true);
        $appStyles = FileUtil::listDirectoryFiles($appStylePath, false, true, true);

        //RWF Styles
        foreach ($rwfStyles as $style) {

            //den Ordner all nicht als Style verwenden
            if($style['name'] == 'all') {

                continue;
            }

            //Style Daten erstellen
            $this->styleList[$style['name']] = array('js' => array(), 'css' => array());

            //Allgemeine Style Daten laden
            if($rwfAllStyles !== false) {

                foreach($rwfAllStyles as $file) {

                    if ($file['type'] === FileUtil::FILE) {

                        if (preg_match('#\.css$#', $file['name'])) {

                            //CSS Datei
                            $this->styleList[$style['name']]['css'][] = './rwf/inc/style/'. ($mobilStyle == true ? 'mobile' : 'web') .'/all/' . $file['name'];
                        } elseif (preg_match('#\.js$#', $file['name'])) {

                            //JS Datei
                            $this->styleList[$style['name']]['js'][] = './rwf/inc/style/'. ($mobilStyle == true ? 'mobile' : 'web') .'/all/' . $file['name'];
                        }
                    }
                }
            }

            //CSS und JS dateien Suchen
            $files = FileUtil::listDirectoryFiles($rwfStylePath . $style['name'], false, true, true);

            //Style Dateien suchen
            foreach ($files as $file) {

                if ($file['type'] === FileUtil::FILE) {

                    if (preg_match('#\.css$#', $file['name'])) {

                        //CSS Datei
                        $this->styleList[$style['name']]['css'][] = './rwf/inc/style/'. ($mobilStyle == true ? 'mobile' : 'web') .'/' . $style['name'] . '/' . $file['name'];
                    } elseif (preg_match('#\.js$#', $file['name'])) {

                        //JS Datei
                        $this->styleList[$style['name']]['js'][] = './rwf/inc/style/'. ($mobilStyle == true ? 'mobile' : 'web') .'/' . $style['name'] . '/' . $file['name'];
                    }
                }
            }
        }

        //APP Styles
        foreach ($appStyles as $style) {

            //den Ordner all nicht als Style verwenden
            if($style['name'] == 'all') {

                continue;
            }

            //Style Daten erstellen
            if (!isset($this->styleList[$style['name']])) {

                $this->styleList[$style['name']] = array('js' => array(), 'css' => array());
            }

            //Allgemeine Style Daten laden
            if($appAllStyles != false) {

                foreach($appAllStyles as $file) {

                    if ($file['type'] === FileUtil::FILE) {

                        if (preg_match('#\.css$#', $file['name'])) {

                            //CSS Datei
                            $this->styleList[$style['name']]['css'][] = './' . APP_NAME . '/inc/style/'. ($mobilStyle == true ? 'mobile' : 'web') .'/all/' . $file['name'];
                        } elseif (preg_match('#\.js$#', $file['name'])) {

                            //JS Datei
                            $this->styleList[$style['name']]['js'][] = './' . APP_NAME . '/inc/style/'. ($mobilStyle == true ? 'mobile' : 'web') .'/all/' . $file['name'];
                        }
                    }
                }
            }

            //CSS und JS dateien Suchen
            $files = FileUtil::listDirectoryFiles($appStylePath . $style['name'], false, true, true);

            //Style Dateien suchen
            foreach ($files as $file) {

                if ($file['type'] === FileUtil::FILE) {

                    if (preg_match('#\.css$#', $file['name'])) {

                        //CSS Datei
                        $this->styleList[$style['name']]['css'][] = './' . APP_NAME . '/inc/style/'. ($mobilStyle == true ? 'mobile' : 'web') .'/' . $style['name'] . '/' . $file['name'];
                    } elseif (preg_match('#\.js$#', $file['name'])) {

                        //JS Datei
                        $this->styleList[$style['name']]['js'][] = './' . APP_NAME . '/inc/style/'. ($mobilStyle == true ? 'mobile' : 'web') .'/' . $style['name'] . '/' . $file['name'];
                    }
                }
            }
        }
        
        //Style Objekte erstellen
        foreach ($this->styleList as $name => $data) {

            $this->styleObjectList[($mobilStyle == true ? 'mobile' : 'web')][$name] = new Style($name, $mobilStyle, $data['js'], $data['css']);
        }
        
        //Daten zutrueckgeben
        return $this->styleObjectList[($mobilStyle == true ? 'mobile' : 'web')];
    }

    /**
     * gibt falls vorhanden einen Web Style zurueck
     * 
     * @param  String $name Name des Styles
     * @return \RWF\Style\Style
     */
    public function getWebStyle($name) {
        
        //Styles Laden
        $this->listStyles();
        
        //Objekt zurueck geben
        if(isset($this->styleObjectList['web'][$name])) {
            
            return $this->styleObjectList['web'][$name];
        }
        return null;
    }
    
    /**
     * gibt falls vorhanden einen Mobilen Style zurueck
     * 
     * @param  String $name Name des Styles
     * @return \RWF\Style\Style
     */
    public function getMobileStyle($name) {
        
        //Styles Laden
        $this->listStyles(true);
        
        //Objekt zurueck geben
        if(isset($this->styleObjectList['mobile'][$name])) {
            
            return $this->styleObjectList['mobile'][$name];
        }
        return null;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt den Classloader zurueck
     * 
     * @return \RWF\Style\StyleEditor 
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new StyleEditor();
        }
        return self::$instance;
    }

}
