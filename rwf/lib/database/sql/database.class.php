<?php

namespace RWF\Database\SQL;

/**
 * Datenbank Basisklasse
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */
abstract class Database {

    /**
     * Hostadresse des Datenbankservers
     *
     * @var String
     */
    protected $host = '';

    /**
     * Port des Datenbankservers
     *
     * @var Integer
     */
    protected $port = 3306;

    /**
     * Benutzer des Datenbankservers
     *
     * @var String
     */
    protected $user = '';

    /**
     * Passwort des Datenbankservers
     *
     * @var String
     */
    protected $pass = '';

    /**
     * Datenbank
     *
     * @var String
     */
    protected $database = '';

    /**
     * Datenbankobjekt
     *
     * @var \PDO
     */
    protected $pdo = null;

    /**
     * initialisiert die Datenbankverbindung
     *
     * @param String $host     Host Adresse
     * @param String $port     Port
     * @param String $user     Benutzer
     * @param String $password Passwort
     * @param String $database Datenbank
     */
    public function __construct($host, $port, $user, $password, $database) {

        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $password;
        $this->database = $database;
    }

    /**
     * schuetzt Sonderzeichen im String mit Escape Sequenzen und setzt den String in einfache Anfuehrungszeichen
     *
     * @param  String $str Zeichenkette
     * @return String
     */
    public function quote($str) {

        return $this->pdo->quote($str);
    }

    /**
     * fuehrt eine Datenbankabfrage aus
     *
     * @param String $query SQL Abfrage
     * @return \PDOStatement
     */
    public function query($query) {

        return $this->pdo->query($query);
    }

    /**
     * fuehrt eine Datenbankabfrage aus und gibt die Anzahl der betroffenen Zeilen zurueck
     *
     * @param String $query SQL Abfrage
     * @return Integer
     */
    public function exec($query) {

        return $this->pdo->exec($query);
    }

    /**
     * startet eine Transaction
     *
     * @return Boolean
     */
    public function beginTransaction() {

        return $this->pdo->beginTransaction();
    }

    /**
     * beendet die Transaktion mit dem Speichern der Daten
     *
     * @return Boolean
     */
    public function commit() {

        return $this->pdo->commit();
    }

    /**
     * beendet die Transaktion mit dem Zuruecksetzen der Daten
     *
     * @return Boolean
     */
    public function rollback() {

        return $this->pdo->rollBack();
    }

    /**
     * gibt an ob gerade eine Transaktion aktiv ist
     *
     * @return Boolean
     */
    public function inTransaction() {

        return $this->pdo->inTransaction();
    }

    /**
     * baut die Datenbankverbindung auf
     * @param  Boolean $persitent Dauerhafte Verbindung nutzen
     * @throws \PDOException
     */
    public abstract function connect($persitent = true);

    /**
     * fuehrt eine einfache SELECT Abfrage aus
     *
     * @param  String $table      Tabelle aus der die daten gelesen werden sollen
     * @param  String $fields     Felder die ausgelesen werden sollen (Optional, Standart *)
     * @param  String $conditions Bedingunen (WHERE)
     * @param  Array  $options    Diverse Optionen (LIMIT, ORDER BY, GROUP BY)
     * @return \PDOStatement
     * @throws \PDOException
     */
    public abstract function selectQuery($table, $fields = '*', $conditions = '', array $options = array());

    /**
     * fuehrt eine einfache UPDATE Abfrage aus und gibt die Anzahl der betroffenen Zeilen zurueck
     *
     * @param  String  $table    Tabellenname
     * @param  Array   $data     Array mit den Name Werte paaren
     * @param  String  $where    WHERE Bedingung
     * @param  Integer $limit    LIMIT Bedingung
     * @return Integer
     * @throws \PDOException
     */
    public abstract function updateQuery($table, array $data, $where = '', $limit = '');

    /**
     * fuehrt eine einfache DELETE Abfrage aus und gibt die Anzahl der betroffenen Zeilen zurueck
     *
     * @param  String  $table Tabellenname
     * @param  String  $where WHERE Bedingung
     * @param  Integer $limit LIMIT Bedingung
     * @return Integer
     * @throws \PDOException
     */
    public abstract function deleteQuery($table, $where = '', $limit = 1);

    /**
     * fuehrt eine einfache INSERT abfrage aus
     *
     * @param  String $table Tabellenname
     * @param  Array  $array Name Werte paare der einzufügenden Felder
     * @return Integer Auto Increment ID des Angelegten Datensatzes wenn ein Feld mit Auto Increment verfuegbar ist (null wenn keine Daten eingefuegt)
     * @throws \PDOException
     */
    public abstract function insertQuery($table, array $data);

    /**
     * fuehrt eine einfache INSERT abfrage mit mehreren Zeilen aus
     *
     * @param  String $table Tabellenname
     * @param  Array  $array Mehrdimensionales Array mit Name Werte paare der einzufügenden Felder
     * @return Array Auto Increment ID`s der Angelegten Datensaetze wenn ein Feld mit Auto Increment verfuegbar ist (null wenn keine Daten eingefuegt)
     * @throws \PDOException
     */
    public abstract function insertMultibleQuery($table, array $data);

    /**
     * prueft ob eine Tabelle vorhanden ist
     *
     * @param  String $table Tabellenname
     * @return Boolean
     * @throws \PDOException
     */
    public abstract function tableExists($table);

    /**
     * prueft ob ein Feld in einer Tabelle vorhanden ist
     *
     * @param  String $table Tabellenname
     * @param  String $field Feldname
     * @return Boolean
     * @throws \PDOException
     */
    public abstract function fieldExists($table, $field);

    /**
     * gibt ein Array mit allen Datenbanken des RDBMS zurueck (ein Leeres Array wenn Funktion nicht verfuegbar)
     *
     * @return Array
     * @throws \PDOException
     */
    public abstract function listDatabases();

    /**
     * gibt die Anzahl der Datenbanken im RDBMS zurueck (-1 wenn Funktion nicht verfuegbar)
     *
     * @return Integer
     * @throws \PDOException
     */
    public abstract function countDatabases();

    /**
     * loescht eine Datenbank
     *
     * @param  String  $database Datenbank
     * @param  Boolean $hard     Hart loeschen
     * @throws \PDOException
     */
    public abstract function dropDatabase($database, $hard = false);

    /**
     * gibt ein Array mit Allen Tabellen der Datenbank zurueck(ein Leeres Array wenn Funktion nicht verfuegbar)
     *
     * @param  String  $database Datenbank
     * @return Array
     * @throws \PDOException
     */
    public abstract function listTables($database = '');

    /**
     * gibt die Anzahl der Tabellen in der Datenbank zurueck (-1 wenn Funktion nicht verfuegbar)
     *
     * @return Integer
     * @throws \PDOException
     */
    public abstract function countTables();

    /**
     * gibt die Anzahl der Zeilen in der Tabelle zurueck
     *
     * @param  String  $table Datenbanktabelle
     * @return Integer
     * @throws \PDOException
     */
    public abstract function countRows($table);

    /**
     * loescht alle Daten aus einer Tabelle
     *
     * @param  String $table Tabellenname
     * @throws \PDOException
     */
    public abstract function emptyTable($table);

    /**
     * loescht eine Datenbanktabelle
     *
     * @param  String  $table Tabelle
     * @param  Boolean $hard  Hart loeschen
     * @throws \PDOException
     */
    public abstract function dropTable($table, $hard = false);

    /**
     * erzeugt einen Datenbankspezifisches SQL Statement der Create Anweissung der Tabelle
     *
     * @param  String $table Datenbanktabelle
     * @return String
     * @throws \PDOException
     */
    public abstract function showCreateTable($table);

    /**
     * gibt ein Array mit den Spalten der Tabelle zurueck
     *
     * @param  String  $table      Datenbanktabelle
     * @param  Boolean $fieldsOnly Nur Spaltennamen
     * @return Array
     * @throws \PDOException
     */
    public abstract function showFieldsFrom($table, $fieldsOnly = false);

    /**
     * gibt den Speicherbedarf der datenbank in Bytes zurueck
     *
     * @param  String  $database Datenbank
     * @return Integer
     * @throws \PDOException
     */
    public abstract function getSize($database = '');

    /**
     * gibt den Ueberhang der angegbenen Tabellen zurueck
     *
     * @return Array
     * @throws \PDOException
     */
    public abstract function getOverload();

    /**
     * Optimiert die angegbenen Tabelle
     *
     * @param  String $table Tabellenname
     * @return \PDOStatement
     * @throws \PDOException
     */
    public abstract function optimizeTable($table);

    /**
     * Analysiert die angegbenen Tabelle
     *
     * @param  String $table Tabellenname
     * @return \PDOStatement
     * @throws \PDOException
     */
    public abstract function analyzeTable($table);

    /**
     * Repariert die angegbenen Tabelle
     *
     * @param  String $table Tabellenname
     * @return \PDOStatement
     * @throws \PDOException
     */
    public abstract function repairTable($table);

    /**
     * Ueberprueft die angegbenen Tabelle
     *
     * @param  String $table Tabellenname
     * @return \PDOStatement
     * @throws \PDOException
     */
    public abstract function checkTable($table);

    /**
     * gibt die Version des Datenbankservers zurueck
     *
     * @return String
     * @throws \PDOException
     */
    public abstract function getVersion();

    /**
     * gibt an ob die Verbindungsart von PHP unterstuetzt wird
     *
     * @return Boolean
     */
    public static abstract function isSupported();

    /**
     * gibt das PDO Objekt zurueck
     * @return \PDO
     */
    public function getPDO() {

        return $this->pdo;
    }

    /**
     * beendet die Datenbankverbindung
     */
    public function close() {

        $this->pdo = null;
    }
}