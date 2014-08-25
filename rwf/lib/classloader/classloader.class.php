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
     * vom Packer ausgeschlossene Dateien/Ordner
     * 
     * @var Array 
     */
    protected $excludes = array('classloader.class.php', 'classnotfoundexception.class.php', 'error.class.php');

    /**
     * gibt an ob die Klassendatei schon geladen wurde
     * 
     * @var Boolean 
     */
    protected $classesLoaded = false;

    public function __construct() {

        //eigenen Namensraum anmelden
        $this->registerBaseNamespace('RWF', PATH_RWF_CLASSES);
    }

    /**
     * schliest eine Datei vom AUtoload aus
     * 
     * @param String $file Dateiname
     */
    public function addExclude($file) {

        $this->excludes[] = $file;
    }

    /**
     * laedt alle Klassen durch die gepackte Klassendatei
     */
    public function loadAllClasses($class = '') {

        //pruefen ob Klassendatei schon geladen
        if ($this->classesLoaded === true) {

            throw new ClassNotFoundException($class, 1004, 'Die Klasse "' . $class . '" konnte nicht geladen werden oder existiert nicht');
        }

        //Classes.php erstellen falls nicht vorhanden
        if (!file_exists(PATH_RWF_CACHE . 'classes.php')) {

            $this->pack();
        }
        //Klassendatei einbinden
        require_once(PATH_RWF_CACHE . 'classes.php');
        $this->classesLoaded = true;
    }

    /**
     * packt alle Klassen in eine Datei
     * (aus den vorher registrierten Namensraeumen)
     */
    protected function pack() {

        //pruefen ob Schreibrechte vorhanden
        if (!is_writeable(PATH_RWF_CACHE)) {

            throw new \Exception('Die "classes.php" kann wegen felenden Schreibrechten nicht erstellt werden', 1003);
        }

        //Datei oeffnen und Inhalt initialisieren
        $classesFile = fopen(PATH_RWF_CACHE . 'classes.php', 'w');
        fwrite($classesFile, "<?php \n\n/**\n * Diese Datei wird automatisch erstellt und sollte nicht von Hand veraendert werden\n * Erstellt am: " . date('r') . "\n * @author Oliver Kleditzsch\n * @copyright Copyright (c) " . date('Y') . ", Oliver Kleditzsch\n * @license http://opensource.org/licenses/gpl-license.php GNU Public License\n*/\n");

        //Alle Namensraume durchlaufen
        foreach ($this->namespaces as $classPath) {

            //Alle Ordner und Dateien durchlaufen
            $files = $this->readFiles($classPath, $this->excludes);
            foreach ($files as $file) {

                fwrite($classesFile, $this->packFile($file) . "\n");
            }
        }

        //Datei schliesen
        fclose($classesFile);
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
     * Filtert alle .class.php Dateien aus einem Ordner und gibt die Dateinamen als Array zurück
     * 
     * @param  String $path     Pfad
     * @param  Array  $excludes Dateien/Ordner die nicht mit eingelesen werden sollen
     * @return Array
     */
    protected function readFiles($path, array $excludes = array()) {

        $files = array();

        $dir = opendir($path);
        while ($file = readdir($dir)) {

            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_file($path . $file) && preg_match('#.class.php$#i', $path . $file) && !in_array($file, $excludes)) {

                $files[] = $path . $file;
            }

            if (is_dir($path . $file)) {

                $result = $this->readFiles($path . $file . '/', $excludes);
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
