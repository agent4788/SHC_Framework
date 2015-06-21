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
     * @var \RWF\ClassLoader\ClassLoader
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
    
    /**
     * vom Packen ausgeschlossene Dateien
     * 
     * @var Array 
     */
    protected $excludes = array('classloader.class.php', 'classnotfoundexception.class.php', 'error.class.php');
    
    /**
     * Klassendateien die beim Packen zuerst geladen werden muessen
     * 
     * @var Array 
     */
    protected $loadFirst = array('abstractform.class.php', 'abstractformelement.class.php', 'abstractcommand.class.php');

    protected function __construct() {

        //eigenen Namensraum anmelden
        $this->registerBaseNamespace('RWF', PATH_RWF_CLASSES);
    }

    /**
     * includiert die gepackte Klassendatei
     */
    public function icnludeClasses() {
        
        //Klassen Packen wenn Datei nicht vorhanden
        if(!file_exists(PATH_RWF_CACHE . APP_NAME .'_classes.php')) {
            
            $this->packClasses();
        } 
        
        //Wenn Classes Datei noch nicht includiert, Datei includieren
        if($this->classesLoaded === false) {
            
            require_once(PATH_RWF_CACHE . APP_NAME .'_classes.php');
            $this->classesLoaded = true;
        }
    }

    /**
     * packt alle Klassen in eine Klassendatei
     */
    protected function packClasses() {
        
        //Datei loeschen falls schon vorhanden
        if(file_exists(PATH_RWF_CACHE . APP_NAME .'_classes.php')) {
            
            @unlink(PATH_RWF_CACHE . APP_NAME .'_classes.php');
        }
        
        //Datei Initialisieren
        file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', "<?php \n\n/**\n * Diese Datei wird automatisch erstellt und sollte nicht von Hand veraendert werden\n * Erstellt am: " . date('r') . "\n * @author Oliver Kleditzsch\n * @copyright Copyright (c) " . date('Y') . ", Oliver Kleditzsch\n * @license http://opensource.org/licenses/gpl-license.php GNU Public License\n*/\n");
        
        //Alle bekannten Namensraume durchlaufen
        foreach($this->namespaces as $name => $path) {
            
            //Dateibaum einlesen
            $files = $this->readFiles($path);
            
            //Alle Klassen in die Klassendatei Packen
            $orderedList = array('interface' => array('first' => array(), 'normal' => array()), 'abstract' => array('first' => array(), 'normal' => array()), 'class' => array('first' => array(), 'normal' => array()));
            foreach($files as $file) {
                
                $content = file_get_contents($file);
                if(preg_match('#\n\s*interface\s+#', $content)) {
                    
                    //Interface
                    $split = preg_split('#/#', $file);
                    $filename = $split[count($split) - 1];
                    if(in_array($filename, $this->loadFirst)) {
                        
                        $orderedList['interface']['first'][] = $file;
                    } else {
                        
                        $orderedList['interface']['normal'][] = $file;
                    }
                } elseif(preg_match('#\n\s*abstract\s+class\s+#', $content)) {
                    
                    //Abstracte Klasse
                    $split = preg_split('#/#', $file);
                    $filename = $split[count($split) - 1];
                    if(in_array($filename, $this->loadFirst)) {
                        
                        $orderedList['abstract']['first'][] = $file;
                    } else {
                        
                        $orderedList['abstract']['normal'][] = $file;
                    }
                } else {
                    
                    //Normale Klasse
                    $split = preg_split('#/#', $file);
                    $filename = $split[count($split) - 1];
                    if(in_array($filename, $this->loadFirst)) {
                        
                        $orderedList['class']['first'][] = $file;
                    } else {
                        
                        $orderedList['class']['normal'][] = $file;
                    }
                }
            }
            
            //Interfaces Packen
            foreach($orderedList['interface']['first'] as $file) {
                
                file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', $this->packFile($file), FILE_APPEND);
            }
            foreach($orderedList['interface']['normal'] as $file) {
                
                file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', $this->packFile($file), FILE_APPEND);
            }
            
            //Abstracte Klassen Packen
            foreach($orderedList['abstract']['first'] as $file) {
                
                file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', $this->packFile($file), FILE_APPEND);
            }
            foreach($orderedList['abstract']['normal'] as $file) {
                
                file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', $this->packFile($file), FILE_APPEND);
            }
            
            //normale Klassen Packen
            foreach($orderedList['class']['first'] as $file) {
                
                file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', $this->packFile($file), FILE_APPEND);
            }
            foreach($orderedList['class']['normal'] as $file) {
                
                file_put_contents(PATH_RWF_CACHE . APP_NAME .'_classes.php', $this->packFile($file), FILE_APPEND);
            }
        }
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
        return $content . "\n";
    }
    
    /**
     * liest den Verzeichnisbaum ein
     * 
     * @param  String $path Pfad
     * @return Array
     */
    protected function readFiles($path = PATH_RWF_CLASSES) {

        $files = array();

        $dir = opendir($path);
        while ($file = readdir($dir)) {

            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_file($path . $file) && preg_match('#.class.php$#i', $path . $file) && !in_array($file, $this->excludes)) {

                $files[] = $path . $file;
            }

            if (is_dir($path . $file)) {

                $result = $this->readFiles($path . $file . '/');
                $files = array_merge($files, $result);
            }
        }

        return $files;
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

        //Basis Namensraum
        $matches = array();
        preg_match('#^(\S+?)\\\\#', $class, $matches);
        if(!isset($matches[1])) {

            throw new ClassNotFoundException($class, 1000, 'Unbekannter Namensraum ""');
        }
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
                if (!class_exists($class, false) && !interface_exists($class, false) && !trait_exists($class, false)) {

                    throw new ClassNotFoundException($class, 1002, 'Die Klasse "' . $class . '" konnte nicht geladen werden');
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
     * @return \RWF\ClassLoader\ClassLoader  
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new ClassLoader();
        }
        return self::$instance;
    }

}
