<?php

namespace SHC\Sheduler;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Error\Error;
use RWF\Util\FileUtil;
use RWF\XML\Exception\XmlException;

/**
 * fuehrt Nacheinander alle Aufgaben (Tasks) aus
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Sheduler {

    /**
     * Liste mit den Aufgaben
     * 
     * @var Array
     */
    protected $tasks = array();

    /**
     * ID des Schleifendurchlaufes
     *
     * @var Integer
     */
    protected static $loop = 0;

    public function __construct() {

        $this->loadTasks();
    }

    /**
     * laedt die einzelnen Aufgabe aus dem Dateisystem
     * 
     * @throws \Exception
     */
    public function loadTasks() {

        $path = PATH_SHC_CLASSES . 'sheduler/tasks/';
        $dir = opendir($path);

        //Dateien Laden und Objekte Initialisieren
        while ($file = readdir($dir)) {

            if (preg_match('#\.class\.php$#', $file)) {

                //Datei Includieren
                require_once($path . $file);
                
                $class = '\\SHC\\Sheduler\\Tasks\\'. str_replace('.class.php', '', $file);
                $object = new $class();
                
                if($object instanceof Task) {
                    
                    //Objekt nach Prioritaet Speichern
                    if(!isset($this->tasks[$object->getPriority()])) {
                        
                        $this->tasks[$object->getPriority()] = array();
                    }
                    $this->tasks[$object->getPriority()][] = $object;
                } else {
                    
                    throw new Exception('Die Aufgabe muss die "Task" Schnittstelle implementieren', 1508);
                }
            }
        }

        closedir($dir);
        
        //Nach Prioritaeten sortieren
        ksort($this->tasks, SORT_NUMERIC);
    }

    /**
     * fuehrt in der Reihenfolge der Prioritaet die einzelnen Aufgaben aus
     * 
     * @throws \Exception
     */
    public function executeTasks() {
        
        //Prioritaeten durchlaufen
        foreach($this->tasks as $prio => $objects) {
            
            //Aufgaben nacheinander ausfuehren
            foreach($objects as $task) {
                
                /* @var $task \SHC\Sheduler\Task */
                try {

                    $task->execute();
                    self::$loop++;
                } catch(XmlException $e) {

                    //Ausgabe auf Kommandozeile
                    RWF::getResponse()->writeLnColored($e->getCode() .': '. $e->getMessage(), 'red');
                    //Log Eintrag erzeugen
                    $error = new Error(false);
                    $error->handleXMLException($e, true, 'shedulerXmlError.log');
                    continue;
                }
            }
        }

        //Run Flag alle 60 Sekunden setzen
        if(!isset($time)) {

            $time = DateTime::now();
        }
        if($time <= DateTime::now()) {

            if(!file_exists(PATH_RWF_CACHE . 'shedulerRun.flag')) {

                FileUtil::createFile(PATH_RWF_CACHE . 'shedulerRun.flag', 0777, true);
            }
            file_put_contents(PATH_RWF_CACHE . 'shedulerRun.flag', DateTime::now()->getDatabaseDateTime());
            $time->add(new \DateInterval('PT1M'));
        }
    }

    /**
     * gibt die aktuelle Schleifen ID zurueck
     *
     * @return Integer
     */
    public static function getLoopId() {

        return self::$loop;
    }

}
