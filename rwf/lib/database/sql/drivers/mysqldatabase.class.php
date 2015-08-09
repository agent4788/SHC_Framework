<?php

namespace RWF\Database\SQL\Drivers;

//Imports
use RWF\Database\SQL\Database;

/**
 * MySQL Datenbanktreiber
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */
class MySQLDatabase extends Database {

    /**
     * baut die Datenbankverbindung auf
     *
     * @param  Boolean $persitent Dauerhafte Verbindung nutzen
     * @throws \PDOException
     */
    public function connect($persitent = true) {

        $options = array();
        //Datenbankspezifisch (MySQL)
        $options[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
        if ($persitent == true) {
            $options[\PDO::ATTR_PERSISTENT] = true;
        }

        $this->pdo = new \PDO('mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->database, $this->user, $this->pass, $options);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

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
    public function selectQuery($table, $fields = '*', $conditions = '', array $options = array()) {

        //SELECT Initialisieren
        $query = 'SELECT ' . $fields . ' FROM ' . $table;

        //WHERE
        if ($conditions != '') {
            $query .= ' WHERE ' . $conditions;
        }

        //GROUP BY
        if (isset($options['group_by'])) {
            $query .= ' GROUP BY ' . $options['group_by'];
        }

        //ORDER BY
        if (isset($options['order_by'])) {
            $query .= ' ORDER BY ' . $options['order_by'];
        }

        //LIMIT
        if (isset($options['limit_start']) && isset($options['limit'])) {
            $query .= ' LIMIT ' . $options['limit_start'] . ', ' . $options['limit'];
        } elseif (isset($options['limit'])) {
            $query .= ' LIMIT ' . $options['limit'];
        }

        return $this->pdo->query($query);
    }

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
    public function updateQuery($table, array $data, $where = '', $limit = '') {

        $comma = '';
        $query = '';

        //Liste der Felder und der neuen Werte
        foreach ($data as $field => $value) {
            $query .= $comma . '`' . $field . '` = :' . $field;
            $comma = ' , ';
        }

        //WHERE
        if ($where != '') {
            $query .= ' WHERE ' . $where;
        }

        //WHERE
        if ($limit != '') {
            $query .= ' LIMIT ' . $limit;
        }

        //Abfrage vorbereiten und ausfuehren
        $smt = $this->pdo->prepare("UPDATE " . $table . " SET " . $query);
        $smt->execute($data);
        return $smt->rowCount();
    }

    /**
     * fuehrt eine einfache DELETE Abfrage aus und gibt die Anzahl der betroffenen Zeilen zurueck
     *
     * @param  String  $table Tabellenname
     * @param  String  $where WHERE Bedingung
     * @param  Integer $limit LIMIT Bedingung
     * @return Integer
     * @throws \PDOException
     */
    public function deleteQuery($table, $where = '', $limit = 1) {

        $query = '';

        //WHERE
        if ($where != '') {
            $query .= ' WHERE ' . $where;
        }

        //LIMIT
        if ($limit > 0) {
            $query .= ' LIMIT ' . $limit;
        }

        //Abfrage vorbereiten und ausfuehren
        $smt = $this->pdo->prepare("DELETE FROM " . $table . " " . $query);
        $smt->execute();
        return $smt->rowCount();
    }

    /**
     * fuehrt eine einfache INSERT abfrage aus
     *
     * @param  String $table Tabellenname
     * @param  Array  $array Name Werte paare der einzufügenden Felder
     * @return Integer Auto Increment ID des Angelegten Datensatzes wenn ein Feld mit Auto Increment verfuegbar ist (null wenn keine Daten eingefuegt)
     * @throws \PDOException
     */
    public function insertQuery($table, array $data) {

        //Feldnamen
        $fields = '`' . implode('`,`', array_keys($data)) . '`';
        //Werte
        $values = ':' . implode(', :', array_keys($data)) . '';

        //Abfrage vorbereiten und ausfuehren
        $smt = $this->pdo->prepare("INSERT INTO " . $table . " (" . $fields . ") VALUES (" . $values . ")");
        $smt->execute($data);
        return $this->pdo->lastInsertId();
    }

    /**
     * fuehrt eine einfache INSERT abfrage mit mehreren Zeilen aus
     *
     * @param  String $table Tabellenname
     * @param  Array  $array Mehrdimensionales Array mit Name Werte paare der einzufügenden Felder
     * @return Array Auto Increment ID`s der Angelegten Datensaetze wenn ein Feld mit Auto Increment verfuegbar ist (null wenn keine Daten eingefuegt)
     * @throws \PDOException
     */
    public function insertMultibleQuery($table, array $data) {

        //Feldnamen
        $fields = '`' . implode('`,`', array_keys($data[0])) . '`';
        //Werte
        $values = ':' . implode(', :', array_keys($data[0])) . '';

        //Abfrage vorbereiten und ausfuehren
        $smt = $this->pdo->prepare("INSERT INTO " . $table . " (" . $fields . ") VALUES (" . $values . ")");
        $return = array();
        foreach ($data as $row) {
            $smt->execute($row);
            $return[] = $this->pdo->lastInsertId();
        }
        return $return;
    }

    /**
     * prueft ob eine Tabelle vorhanden ist
     *
     * @param  String $table Tabellenname
     * @return Boolean
     * @throws \PDOException
     */
    public function tableExists($table) {

        $array = $this->pdo->query("SHOW TABLES LIKE '" . $table . "'")->fetchAll();
        if (count($array[0]) > 0) {

            return true;
        }
        return false;
    }

    /**
     * prueft ob ein Feld in einer Tabelle vorhanden ist
     *
     * @param  String $table Tabellenname
     * @param  String $field Feldname
     * @return Boolean
     * @throws \PDOException
     */
    public function fieldExists($table, $field) {

        $array = $this->pdo->query("SHOW COLUMNS FROM " . $table . " LIKE '" . $field . "'")->fetchAll();
        if (count($array[0]) > 0) {

            return true;
        }
        return false;
    }

    /**
     * gibt ein Array mit allen Datenbanken des RDBMS zurueck (ein Leeres Array wenn Funktion nicht verfuegbar)
     *
     * @return Array
     * @throws \PDOException
     */
    public function listDatabases() {

        $databases = array();
        $smt = $this->pdo->query('SHOW DATABASES');
        while ($row = $smt->fetch(\PDO::FETCH_NUM)) {

            $databases[] = $row[0];
        }

        return $databases;
    }

    /**
     * gibt die Anzahl der Datenbanken im RDBMS zurueck (-1 wenn Funktion nicht verfuegbar)
     *
     * @return Integer
     * @throws \PDOException
     */
    public function countDatabases() {

        $databases = $this->listDatabases();
        return count($databases);
    }

    /**
     * loescht eine Datenbank
     *
     * @param  String  $database Datenbank
     * @param  Boolean $hard     Hart loeschen
     * @throws \PDOException
     */
    public function dropDatabase($database, $hard = false) {

        if ($hard == true) {

            $query = 'DROP DATABASE ' . $database;
        } else {

            $query = 'DROP DATABASE IF EXISTS ' . $database;
        }

        $this->pdo->query($query);
    }

    /**
     * gibt ein Array mit Allen Tabellen der Datenbank zurueck(ein Leeres Array wenn Funktion nicht verfuegbar)
     *
     * @param  String  $database Datenbank
     * @return Array
     * @throws \PDOException
     */
    public function listTables($database = '') {

        $tables = array();
        if (strlen($database) >= 1) {
            $smt = $this->pdo->query('SHOW TABLES FROM ' . $database);
        } else {
            $smt = $this->pdo->query('SHOW TABLES');
        }
        while ($row = $smt->fetch(\PDO::FETCH_NUM)) {

            $tables[] = $row[0];
        }
        return $tables;
    }

    /**
     * gibt die Anzahl der Tabellen in der Datenbank zurueck (-1 wenn Funktion nicht verfuegbar)
     *
     * @return Integer
     * @throws \PDOException
     */
    public function countTables() {

        $tables = $this->listTables();
        return count($tables);
    }

    /**
     * gibt die Anzahl der Zeilen in der Tabelle zurueck
     *
     * @param  String  $table Datenbanktabelle
     * @return Integer
     * @throws \PDOException
     */
    public function countRows($table) {

        $smt = $this->selectQuery($table, 'COUNT(*) AS count');
        $row = $smt->fetch();
        return intval($row['count']);
    }

    /**
     * loescht alle Daten aus einer Tabelle
     *
     * @param  String $table Tabellenname
     * @throws \PDOException
     */
    public function emptyTable($table) {

        $this->pdo->query('TRUNCATE TABLE ' . $table);
    }

    /**
     * loescht eine Datenbanktabelle
     *
     * @param  String  $table Tabelle
     * @param  Boolean $hard  Hart loeschen
     * @throws \PDOException
     */
    public function dropTable($table, $hard = false) {

        if ($hard == true) {

            $query = 'DROP TABLE ' . $this->database;
        } else {

            $query = 'DROP TABLE IF EXISTS ' . $this->database;
        }

        $this->pdo->query($query);
    }

    /**
     * erzeugt einen Datenbankspezifisches SQL Statement der Create Anweissung der Tabelle
     *
     * @param  String $table Datenbanktabelle
     * @return String
     * @throws \PDOException
     */
    public function showCreateTable($table) {

        $smt = $this->pdo->query('SHOW CREATE TABLE ' . $table);
        $row = $smt->fetch(\PDO::FETCH_NUM);
        return $row[1];
    }

    /**
     * gibt ein Array mit den Spalten der Tabelle zurueck
     *
     * @param  String  $table      Datenbanktabelle
     * @param  Boolean $fieldsOnly Nur Spaltennamen
     * @return Array
     * @throws \PDOException
     */
    public function showFieldsFrom($table, $fieldsOnly = false) {

        $smt = $this->pdo->query('SHOW FIELDS FROM ' . $table);

        $entrys = array();
        while ($row = $smt->fetch(\PDO::FETCH_NUM)) {

            if ($fieldsOnly == true) {

                $entrys[] = $row[0];
            } else {

                $entrys[] = array('field' => $row[0],
                    'type' => $row[1],
                    'null' => $row[2],
                    'key' => $row[3],
                    'default' => $row[4],
                    'extra' => $row[5]
                );
            }
        }

        return $entrys;
    }

    /**
     * gibt den Speicherbedarf der datenbank in Bytes zurueck
     *
     * @param  String $database Datenbank
     * @return Integer
     * @throws \PDOException
     */
    public function getSize($database = '') {

        $append = '';
        if ($database != '') {
            $append = ' FROM ' . $database;
        }

        $smt = $this->pdo->query('SHOW TABLE STATUS' . $append);

        $size = 0;
        while ($row = $smt->fetch(\PDO::FETCH_NUM)) {

            $size += $row[6] + $row[8];
        }
        return $size;
    }

    /**
     * gibt den Ueberhang der angegbenen Tabellen zurueck
     *
     * @return Array
     * @throws \PDOException
     */
    public function getOverload() {

        $smt = $this->pdo->query('SHOW TABLE STATUS');

        $size = 0;
        while ($row = $smt->fetch(\PDO::FETCH_NUM)) {

            $size += $row[9];
        }
        return $size;
    }

    /**
     * Optimiert die angegbenen Tabelle
     *
     * @param  String $table Tabellenname
     * @return \PDOStatement
     * @throws \PDOException
     */
    public function optimizeTable($table) {

        return $this->pdo->query('OPTIMIZE TABLE ' . $table);
    }

    /**
     * Analysiert die angegbenen Tabelle
     *
     * @param  String $table Tabellenname
     * @return \PDOStatement
     * @throws \PDOException
     */
    public function analyzeTable($table) {

        return $this->pdo->query('ANALYZE TABLE ' . $table);
    }

    /**
     * Repariert die angegbenen Tabelle
     *
     * @param  String $table Tabellenname
     * @return \PDOStatement
     * @throws \PDOException
     */
    public function repairTable($table) {

        return $this->pdo->query('REPAIR TABLE ' . $table);
    }

    /**
     * Ueberprueft die angegbenen Tabelle
     *
     * @param  String $table Tabellenname
     * @return \PDOStatement
     * @throws \PDOException
     */
    public function checkTable($table) {

        return $this->pdo->query('CHECK TABLE ' . $table);
    }

    /**
     * gibt die Version des Datenbankservers zurueck
     *
     * @return String
     * @throws \PDOException
     */
    public function getVersion() {

        $result = $this->pdo->query("SELECT VERSION()")->fetch(\PDO::FETCH_NUM);
        return $result[0];
    }

    /**
     * gibt an ob die Verbindungsart von PHP unterstuetzt wird
     *
     * @return Boolean
     */
    public static function isSupported() {

        return (extension_loaded('PDO') && extension_loaded('pdo_mysql'));
    }

}