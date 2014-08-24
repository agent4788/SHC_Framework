<?php

namespace RWF\ClassLoader;

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

    public function __construct() {

        $this->registerBaseNamespace('RWF', PATH_RWF_CLASSES);
    }

    /**
     * laedt alle Klassen durch die gepackte Klassendatei
     */
    public function loadAllClasses() {

        //Classes.php erstellen falls nicht vorhanden
//        if (!file_exists(PATH_RWF_CACHE . 'classes.php')) {
//
//            $this->pack();
//        }
        $this->pack();
        //Klassendatei einbinden
        require_once(PATH_RWF_CACHE . 'classes.php');
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
            $files = $this->readFiles($classPath, array('classloader'));
            foreach ($files as $file) {
                
                fwrite($classesFile, $this->packFile($file) ."\n");
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
    protected function readFiles($path = CLASS_PATH, array $excludes = array()) {

        $files = array();

        $dir = opendir($path);
        while ($file = readdir($dir)) {

            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_file($path . $file) && preg_match('#.class.php$#i', $path . $file) && !in_array($file, $excludes)) {

                $files[] = $path . $file;
            }

            if (is_dir($path . $file) && !in_array($file, $excludes)) {

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
            $path = preg_replace(array('#^(\S+)\\\\#', '#\\\\#'), array('', '/'), $class);
            $path = strtolower($path);
            $path = $this->namespaces[$baseNamespace] . $path . '/' . $className . '.class.php';

            if (file_exists($path)) {

                @require_once($path);
                if (!class_exists($class, false) || !interface_exists($class, false)) {

                    throw new Exception\ClassNotFoundException($class, 1002, 'Die Klasse "' . $class . '" konnte nicht geladen werden');
                }
            } else {

                throw new Exception\ClassNotFoundException($class, 1001, 'Die Klasse "' . $class . '" konnte nicht gefunden werden');
            }
        }
        throw new Exception\ClassNotFoundException($class, 1000, 'Unbekannter Namensraum "' . $baseNamespace . '"');
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
