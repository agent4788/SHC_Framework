<?php

namespace RWF\Benchmark;

//Imports
use RWF\Runtime\Runtime;
use RWF\Util\String;

/**
 * Laufzeitverhalten Messen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class Benchmark {

    /**
     * Zeitmessungen
     *
     * @var Array
     */
    protected $time = array();

    /**
     * Speichermessungen
     *
     * @var Array
     */
    protected $memory = array();

    /**
     * Beschreibungen
     *
     * @var Array
     */
    protected $descriptions = array();

    /**
     * Beendete Benchmarks
     *
     * @var Array
     */
    protected $finishedBenchmarks = array();

    /**
     * letzter beendeter Block Benchmark
     *
     * @var Array
     */
    protected $lastFinishedBlock = array();

    /**
     * gibt die vergangene Zeit seit Start der Anwendung zurueck
     *
     * @return Float Zeit in Millisekunden
     */
    public function getGlobalExceutionTime() {

        return round(strtok(microtime(), ' ') . strtok('') - MICROTIME_NOW, 6);
    }

    /**
     * startet den Benchmark um einen Block zu messen
     *
     * @param String $name        Name des Blocks
     * @param String $description Beschreibung
     */
    public function startBlockBenchmark($name, $description = '') {

        $name = String::toLower($name);
        $this->time[$name]         = strtok(microtime(), ' ') . strtok('');
        $this->memory[$name]       = Runtime::getInstance()->getMemorySize();
        $this->descriptions[$name] = $description;
    }

    /**
     * beendet den Blockbenchmark und wertet die Daten aus
     *
     * @param  String $name Name des Blocks
     * @return Array        Daten der Auswertung (0 = Zeit, 1 = Speicherbedarf, 2 = Beschreibung, 3 = Name)
     */
    public function stopBlockBenchmark($name) {

        $name = String::toLower($name);
        //Auswertung
        $this->finishedBenchmarks[$name][0] = round(strtok(microtime(), ' ') . strtok('') - $this->time[$name], 6);
        $this->finishedBenchmarks[$name][1] = Runtime::getInstance()->getMemorySize() - $this->memory[$name];
        $this->finishedBenchmarks[$name][2] = $this->descriptions[$name];

        //Letzter beendeter Block
        $this->lastFinishedBlock[0] = $this->finishedBenchmarks[$name][0];
        $this->lastFinishedBlock[1] = $this->finishedBenchmarks[$name][1];
        $this->lastFinishedBlock[2] = $this->finishedBenchmarks[$name][2];
        $this->lastFinishedBlock[3] = $name;

        return $this->lastFinishedBlock;
    }

    /**
     * gibt die Auswertung vom letzten deendeten Block zurueck
     *
     * @return Array        Daten der Auswertung (0 = Zeit, 1 = Speicherbedarf, 2 = Beschreibung)
     */
    public function getLastBlock() {

        return $this->lastFinishedBlock;
    }

    /**
     * gibt ein mehrdimensionales Array mit den Auswertungen zurueck
     *
     * @return Array Array Daten der Auswertung (0 = Zeit, 1 = Speicherbedarf, 2 = Beschreibung)
     */
    public function getAllBlocks() {

        return $this->finishedBenchmarks;
    }

}