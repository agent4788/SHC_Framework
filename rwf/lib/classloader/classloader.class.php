<?php

namespace RWF\ClassLoader;

//Imports
use RWF\ClassLoader\Exception\ClassNotFoundException;

/**
 * Klassen Laden
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ClassLoader {

    /**
     * Singleton Instanz
     * 
     * @var ClassLoader 
     */
    protected static $instance = null;

    /**
     * bekannte Namensraeume
     * 
     * @var Array 
     */
    protected $namespaces = array();
    
    /**
     * gibt an ob die Klassendatei schon geladen wurde
     * 
     * @var Boolean 
     */
    protected $classesLoaded = false;

    protected function __construct() {

        //eigenen Namensraum anmelden
        $this->registerBaseNamespace('RWF', PATH_RWF_CLASSES);
    }

    /**
     * liest den Dateiinhalt ein und entfernt unnoetigen Inhalt
     * 
     * @param  String $path Pfad zur Datei
     * @return String
     */
    protected function packFile($path) {

        //Inhalt Laden
        $content = file_get_contents($path);

        //Kommentare entfernen
        $content = preg_replace('#\s+//.*#', '', $content);
        $content = preg_replace('#\s*/\\*.*?\\*/#s', '', $content);

        //PHP Tags entfernen
        $content = preg_replace('#<\\?php#', '', $content);
        $content = preg_replace('#\\?>#', '', $content);

        //unnoetige Leerzeichen und Leerzeilen entfernen
        $content = preg_replace('#^\s*$#', '', $content);
        $content = trim($content);

        //Inhalt zurueckgeben
        return $content;
    }

    /**
     * registriert einen Basis Namensraum und den zugehoerigen Klassen Pfad
     * 
     * @param String $namespace Basisnamensraum
     * @param String $path      Pfad zu den Klassen des Namensraums (das letzte Zeichen muss ein Slash sein!)
     */
    public function registerBaseNamespace($namespace, $path) {

        $this->namespaces[strtolower($namespace)] = $path;
    }

    /**
     * laedt eine Klasse
     * 
     * @param String $class Klasse mit Namensraum
     */
    public function loadClass($class) {

        //Classes PHP Laden falls nicht schon geschehen
        if(!DEVELOPMENT_MODE && $this->classesLoaded === false && is_file(PATH_RWF_CACHE . APP_NAME .'_classes.php')) {
            
            require_once(PATH_RWF_CACHE . APP_NAME .'_classes.php');
            $this->classesLoaded = true;
            
            if (class_exists($class, false) && interface_exists($class, false)) {
                
                return true;
            }
        }
        
        //Basis Namensraum
        $matches = array();
        preg_match('#^(\S+?)\\\\#', $class, $matches);
        $baseNamespace = strtolower($matches[1]);

        //Klasse
        $className = preg_replace('#^\\\\?(\S+\\\\)+#', '', $class);
        $className = strtolower($className);

        //Pruefen ob Namensraum bekannt
        if (isset($this->namespaces[$baseNamespace])) {

            //Versuchen Klasse zu laden
            $path = preg_replace('#^' . $baseNamespace . '\\\\#i', '', $class);
            $path = preg_replace('#\\\\' . $className . '$#i', '', $path);
            $path = str_replace('\\', '/', $path);
            $path = strtolower($path);
            $path = $this->namespaces[$baseNamespace] . $path . '/' . $className . '.class.php';

            if (file_exists($path)) {

                @require_once($path);
                //pruefen ob Klasse jetzt bekannt
                if (!class_exists($class, false) && !interface_exists($class, false)) {

                    throw new ClassNotFoundException($class, 1002, 'Die Klasse "' . $class . '" konnte nicht geladen werden');
                }

                //Wenn Klasse erfolgreich geladen und development Modus aus die Klasse an die classes.php anheangen
                if(!DEVELOPMENT_MODE) {
                    
                    //Schreibrechte pruefen
                    if(!is_writable(PATH_RWF_CACHE)) {
                        
                        throw new ClassNotFoundException($class, 1003, 'Die classes.php kann wegen felenden Schreibrechten nicht erstellt werden');
                    }
                    
                    //Classes.php initalisieren falls die Datei noch nicht existiert
                    if(!file_exists(PATH_RWF_CACHE . APP_NAME .'_classes.php')) {
                        
                        file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', "<?php \n\n/**\n * Diese Datei wird automatisch erstellt und sollte nicht von Hand veraendert werden\n * Erstellt am: " . date('r') . "\n * @author Oliver Kleditzsch\n * @copyright Copyright (c) " . date('Y') . ", Oliver Kleditzsch\n * @license http://opensource.org/licenses/gpl-license.php GNU Public License\n*/\n");
                    }
                    file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', $this->packFile($path), FILE_APPEND);
                }
                //Return wenn die Klasse erfolgreich geladen wurde
                return true;
            } else {

                throw new ClassNotFoundException($class, 1001, 'Die Klasse "' . $class . '" konnte nicht gefunden werden');
            }
        }
        throw new ClassNotFoundException($class, 1000, 'Unbekannter Namensraum "' . $baseNamespace . '"');
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt den Classloader zurueck
     * 
     * @return ClassLoader 
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new ClassLoader();
        }
        return self::$instance;
    }

}
