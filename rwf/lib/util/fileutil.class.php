<?php

namespace RWF\Util;

/**
 * Dateifunktionen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.3-0
 */
class FileUtil {

    /**
     * Datei
     * 
     * @var Integer
     */
    const FILE = 0;

    /**
     * Datei
     * 
     * @var Integer
     */
    const DIR = 1;

    /**
     * Unkomprimiert
     * 
     * @var Integer
     */
    const UNCOMPRESSED = 0;

    /**
     * GZip komprimiert
     *
     * @var Integer
     */
    const GZIP_COMPRESSED = 1;

    /**
     * BZip2 komprimiert
     *
     * @var Integer
     */
    const BZIP_COMPRESSED = 2;

    /**
     * formatiert Bytewerte asl binaerdaten zur Anzeige
     * 
     * @param  Integer $size  Bytewert
     * @param  Boolean $short Ausgabe von Kurzen oder Langen groeßenangaben
     * @return String         Formatierte Zahl
     */
    public static function formatBytesBinary($size, $short = true) {

        if ($short === true) {
            $norm = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB',
                'EiB', 'ZiB', 'YiB');
        } else {
            $norm = array('Byte',
                'Kibibyte',
                'Mebibyte',
                'Gibibyte',
                'Tebibyte',
                'Pebibyte',
                'Exbibyte',
                'Zebibyte',
                'Yobibyte');
        }

        $factor = 1024;

        $count = count($norm) - 1;

        $x = 0;
        while ($size >= $factor && $x < $count) {
            $size /= $factor;
            $x++;
        }

        $size = String::formatFloat($size, 2) . ' ' . $norm[$x];
        return $size;
    }

    /**
     * formatiert Bytewerte zur Anzeige
     * 
     * @param  Integer $size  Bytewert
     * @param  Boolean $short Ausgabe von Kurzen oder Langen groeßenangaben
     * @return String         Formatierte Zahl
     */
    public static function formatBytes($size, $short = true) {

        if ($short === true) {
            $norm = array('B', 'KB', 'MB', 'GB', 'TB', 'PB',
                'EB', 'ZB', 'YB');
        } else {
            $norm = array('Byte',
                'Kilobyte',
                'Megabyte',
                'Gigabyte',
                'Terrabyte',
                'Petabyte',
                'Exabyte',
                'Zettabyte',
                'Yottabyte');
        }

        $factor = 1000;

        $count = count($norm) - 1;

        $x = 0;
        while ($size >= $factor && $x < $count) {
            $size /= $factor;
            $x++;
        }

        $size = String::formatFloat($size, 2) . ' ' . $norm[$x];
        return $size;
    }

    /**
     * entfernt einen Slasch am Anfang eines Pfades 
     * 
     * @param  String $path Pfad
     * @return String       Pfad
     */
    public static function removeLeadingSlash($path) {

        if (substr($path, 0, 1) == '/') {
            return substr($path, 1);
        }
        return $path;
    }

    /**
     * setzt einen Slasch am Anfang eines Pfades 
     * 
     * @param  String $path Pfad
     * @return String       Pfad
     */
    public static function addLeadingSlash($path) {

        if (substr($path, 0, 1) == '/') {
            return '/' . $path;
        }
        return $path;
    }

    /**
     * entfernt einen Slasch am Ende eines Pfades 
     * 
     * @param  String $path Pfad
     * @return String       Pfad
     */
    public static function removeTrailingSlash($path) {

        if (substr($path, -1) == '/') {
            return substr($path, 0, -1);
        }
        return $path;
    }

    /**
     * setzt einen Slasch am Ende eines Pfades 
     * 
     * @param  String $path Pfad
     * @return String       Pfad
     */
    public static function addTrailigSlash($path) {

        if (substr($path, -1) != '/') {
            return $path . '/';
        }
        return $path;
    }

    /**
     * Ordnertrenner von \ zu / umwandeln
     *
     * @param  String $path Pfad
     * @return String
     */
    public static function convertDirSeperatorsToUnix($path) {

        $path = str_replace('\\\\', '/', $path);
        $path = str_replace('\\', '/', $path);
        return $path;
    }

    /**
     * gibt an ob der Pfad eine URL ist
     *
     * @param  String  $path Pfad
     * @return Boolean
     */
    public static function isURL($path) {

        if (preg_match('#^(https?|ftps?)://#', $path)) {

            return true;
        }

        return false;
    }

    /**
     * ertslett die Datei falls es noch nicht existiert
     *
     * @param  String  $path      Dateipfad
     * @param  Integer $chmod     Oktalzahl mit Rechten
     * @param  Boolean $recursive Ordner erstellen wenn nicht vohanen
     * @return Boolean
     */
    public static function createFile($path, $chmod = 0777, $recursive = true) {

        if (file_exists($path)) {

            return false;
        }

        //Ordner erzeugen falls nicht vorhanden
        if (!@is_dir(dirname($path))) {

            if ($recursive == true && !self::createDir(dirname($path))) {

                return false;
            }
        }

        //Datei erzeugen
        if (!file_put_contents($path, '') == false) {

            return false;
        }

        //Rechte Setzen
        if (!@chmod($path, $chmod)) {

            clearstatcache();
            return false;
        }

        return true;
    }

    /**
     * kopiert eine Datei
     *
     * @param  String  $src       Quelldatei
     * @param  String  $dest      Zieldatei
     * @param  Integer $chmod     Rechte der kopierten Datei
     * @param  Boolean $overwrite Ueberschreiben falls Vorhanden
     * @param  Boolean $recursive Ordner erstellen wenn nicht vohanen
     * @return Boolean
     */
    public static function copyFile($src, $dest, $chmod = 0777, $overwrite = false, $recursive = true) {

        //Datei Existiert nicht
        if (!@is_file($src)) {

            return false;
        }

        //Zieldatei Existiert schon und soll nicht Ueberschrieben werden
        if (@is_file($dest) && $overwrite === false) {

            return false;
        }

        //Ordner erstellen falls nicht vorhanden
        $dir = dirname($dest);
        if (!@is_dir($dir) && $recursive == true) {

            if (!FileUtil::createDirectory($dir)) {

                return false;
            }
        }

        //kopieren
        if (!@copy($src, $dest)) {

            return false;
        }

        //Rechte setzen
        if (@chmod($dest, $chmod)) {

            clearstatcache();
        } else {

            return false;
        }

        //Pruefen
        if (@is_file($dest)) {

            return true;
        }

        return false;
    }

    /**
     * verschiebt eine Datei
     *
     * @param  String  $src       Quelldatei
     * @param  String  $dest      Zieldatei
     * @param  Integer $chmod     Rechte der kopierten Datei
     * @param  Boolean $overwrite Ueberschreiben falls Vorhanden
     * @param  Boolean $recursive Ordner erstellen wenn nicht vohanen
     * @return Boolean
     */
    public static function moveFile($src, $dest, $chmod = 0777, $overwrite = false, $recursive = true) {

        //Datei Kopieren
        if (!self::copyFile($src, $dest, $chmod, $overwrite, $recursive)) {

            return false;
        }

        //alte Datei loeschen
        if (self::deleteFile($src)) {

            return true;
        }

        return false;
    }

    /**
     * loescht eine Datei
     *
     * @param  String  $file Dateiname
     * @return Boolean
     */
    public static function deleteFile($file) {

        //Datei Existiert nicht
        if (!@is_file($file)) {

            return false;
        }

        //Datei Loeschen
        @chmod($file, 0777);
        if (!@unlink($file)) {

            return false;
        }

        return true;
    }

    /**
     * Datei Umbenennen
     *
     * @param  String  $file    Dateiname
     * @param  String  $newName Neuer Name
     * @return Boolean
     */
    public static function renameFile($file, $newName) {

        //Datei Existiert nicht
        if (!@is_file($file)) {

            return false;
        }

        //Umbenennen
        $parent = self::addTrailigSlash(dirname($file));
        if (!@rename($file, $parent . $newName)) {

            return false;
        }

        return true;
    }

    /**
     * erstellt einen Ordner
     *
     * @param  String  $path      Pfad
     * @param  Boolean $recursive Pfad erstellen falls er nicht existiert
     * @param  Integer $chmod     Zugriffsrechte
     * @return Boolean
     */
    public static function createDirectory($path, $recursive = true, $chmod = 0777) {

        //abbruch wenn der Ordner schon existiert
        if (file_exists($path)) {

            return true;
        }

        //pruefen ob der Elternordner existiert
        $parent = dirname($path);
        if ($parent != $path) {

            //Elternordner erstellen wenn er nicht Existiert
            $parent = self::addTrailigSlash($parent);
            if (!@file_exists($parent) && $recursive == true) {

                if (!self::createDirectory($parent, $chmod)) {

                    return false;
                }
            } elseif (!@file_exists($parent) && $recursive == false) {

                return false;
            }

            //Ordner erstellen
            $old = @umask(0);
            if (!@mkdir($path, $chmod)) {

                return false;
            }

            @umask($old);
            if (!@chmod($path, $chmod)) {

                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * kopiert einen Ordner
     *
     * @param  String  $src           Quellordner
     * @param  String  $dest          Zielordner
     * @param  Integer $chmod         Rechte
     * @param  Boolean $recursive     Unterordner durchlaufen
     * @param  Boolean $overwrite     Ueberschreiben
     * @param  Boolean $bringTogether Zusammenfuehren
     * @return Boolean
     */
    public static function copyDirectory($src, $dest, $chmod = 0777, $recursive = true, $overwrite = false, $bringTogether = false) {

        //Ordner Existiert nicht
        if (!is_dir($src)) {

            return false;
        }

        //Zielordner Existiert und soll nicht Ueberschrieben oder aufgefuellt werden
        if (@is_dir($dest) && $overwrite === false && $bringTogether === false) {

            return false;
        }

        //Ordner durchlaufen
        $dir = @opendir($src);
        $srcPath = self::addTrailigSlash($src);
        $destPath = self::addTrailigSlash($dest);
        while ($file = @readdir($dir)) {

            //nutzlose Elemente ueberspringen
            if ($file == '.' || $file == '..') {

                continue;
            }

            //Datei
            if (@is_file($srcPath . $file)) {

                if (@is_file($destPath . $file) && $overwrite === true) {

                    //Datei Existiert und wird Ueberschrieben
                    if (!self::copyFile($srcPath . $file, $destPath . $file, $chmod, true)) {

                        @closedir($dir);
                        return false;
                    }

                    continue;
                } elseif (!@is_file($destPath . $file)) {

                    //Datei Existiert nicht
                    if (!self::copyFile($srcPath . $file, $destPath . $file, $chmod, false)) {

                        @closedir($dir);
                        return false;
                    }

                    continue;
                } else {

                    //Datei Existiert wird aber nicht ueberschrieben
                    continue;
                }
            }

            //Ordner (wenn Rekursiv gewaehlt)
            if (@is_dir($srcPath . $file) && $recursive === true) {

                if (@is_dir($destPath . $file) && $bringTogether === true) {

                    //Ordner Exisztiert und wird Zusammengefuehrt
                    if (!self::copyDirectory($srcPath . $file, $destPath . $file, $chmod, $recursive, $overwrite, $bringTogether)) {

                        @closedir($dir);
                        return false;
                    }

                    continue;
                } elseif (!@is_dir($destPath . $file)) {

                    //Ordner Existiert nicht
                    if (!self::copyDirectory($srcPath . $file, $destPath . $file, $chmod, $recursive, $overwrite, $bringTogether)) {

                        @closedir($dir);
                        return false;
                    }

                    continue;
                } else {

                    //Ordner Existiert wird aber nicht zusammengefuehrt
                    continue;
                }
            }
        }

        @closedir($dir);
        return true;
    }

    /**
     * verschiebt einen Ordner
     *
     * @param  String  $src           Quellordner
     * @param  String  $dest          Zielordner
     * @param  Integer $chmod         Rechte
     * @param  Boolean $recursive     Unterordner durchlaufen
     * @param  Boolean $overwrite     Ueberschreiben
     * @param  Boolean $bringTogether Zusammenfuehren
     * @return Boolean
     */
    public static function moveDirectory($src, $dest, $chmod = 0777, $recursive = true, $overwrite = false, $bringTogether = false) {

        //Ordner Kopieren
        if (!self::copyDirectory($src, $dest, $chmod, $recursive, $overwrite, $bringTogether)) {

            return false;
        }

        //Ordner loeschen
        if (!self::deleteDirectory($src)) {

            return false;
        }

        return true;
    }

    /**
     * loescht einen Ordner
     *
     * @param  String  $dir           Ordner
     * @param  Boolean $deleteNoEmpty Nicht leere Ordner löschen
     * @return Boolean
     */
    public static function deleteDirectory($dir, $deleteNoEmpty = true) {

        //Ordner Existiert nicht
        if (!is_dir($dir)) {

            return false;
        }

        //Ordner durchlaufen
        $dirPath = self::addTrailigSlash($dir);
        $handle = @opendir($dirPath);
        while ($file = @readdir($handle)) {

            //nutzlose Elemente ueberspringen
            if ($file == '.' || $file == '..') {

                continue;
            }

            //Datei
            if (@is_file($dirPath . $file) && $deleteNoEmpty == true) {

                if (!self::deleteFile($dirPath . $file)) {

                    return false;
                }

                continue;
            }

            //Ordner
            if (@is_dir($dirPath . $file)) {

                if (!self::deleteDirectory($dirPath . $file, $deleteNoEmpty)) {

                    return false;
                }

                continue;
            }
        }
        @closedir($handle);

        //Ordner Loeschen
        if (!@rmdir($dir)) {

            return false;
        }

        return true;
    }

    /**
     * bennent einen Ordner um
     *
     * @param  String  $dir     Ordner
     * @param  String  $newName Neuer Ordnername
     * @param  Integer $chmod   Zugriffsrechte
     * @return Boolean
     */
    public static function renameDirectory($dir, $newName, $chmod = 0777) {

        $parent = self::addTrailigSlash(dirname($dir));
        return self::moveDirectory($dir, $parent . $newName, $chmod, true, true, true);
    }

    /**
     * gibt den Speicherbedarf eines Ordners in Bytes zurueck
     *
     * @param  String  $dir       Pfad
     * @param  Boolean $recursive Unterorner einbeziehen
     * @return Integer
     */
    public static function getDirectorySize($dir, $recursive = true) {

        if (!@is_dir($dir)) {

            return null;
        }

        //groeße ermitteln
        $size = 0;
        $path = self::addTrailigSlash($dir);
        $folder = @opendir($dir);
        while ($file = @readdir($folder)) {

            //Element
            $element = $path . $file;

            //nutzlose Elemente ueberspringen
            if ($file == '.' || $file == '..') {

                continue;
            }

            //Datei
            if (@is_file($element)) {

                $size += filesize($element);
                continue;
            }

            //Ordner
            if (@is_dir($element) && $recursive === true) {

                $size += self::getDirectorySize($element, $recursive);
                continue;
            }
        }

        @closedir($folder);
        return $size;
    }

    /**
     * gibt die Dateigroeße einer Datei in Bytes zurueck
     *
     * @param  String  $file Datei
     * @return Integer
     */
    public static function getFileSize($file) {

        if (@is_file($file)) {

            return filesize($file);
        }

        return 0;
    }

    /**
     * gibt ein Array mit allen enthaltenen Dateien und Ordnern des Ordners zurueck
     *
     * @param  String  $dir          Pfad
     * @param  Boolean $recursive    Unterorner einbeziehen
     * @param  Boolean $ignoreHidden versteckte Dateien ignorieren
     * @param  Boolean $onlyNames    Nur Datei und Ornder Namen ohne zusaetzliche Informationen
     * @return Array
     */
    public static function listDirectoryFiles($dir, $recursive = false, $ignoreHidden = false, $onlyNames = false) {

        //Ordner Existiert nicht
        if (!@is_dir($dir)) {

            return null;
        }

        $path = self::addTrailigSlash($dir);
        $files = array();

        //Ordner Durchlaufen
        $folder = @opendir($dir);
        while ($file = @readdir($folder)) {

            //nutzlose Elemente ueberspringen
            if ($file == '.' || $file == '..') {

                continue;
            }

            //Element
            $entry = array();
            $element = $path . $file;

            //Datei
            if (@is_file($element)) {

                //Versteckte Dateien ignorieren
                if ($ignoreHidden == true && substr($file, 0, 1) == '.') {

                    continue;
                }
                $entry['name'] = $file;
                $entry['type'] = self::FILE;
                if ($onlyNames == false) {

                    $entry['editTime'] = @filectime($element);
                    $entry['accessTime'] = @fileatime($element);
                    $entry['size'] = @filesize($element);
                }
            }

            //Ordner
            if (@is_dir($element)) {

                if ($recursive === true) {

                    //Rekursiver aufruf
                    $entry = self::listDirectoryFiles($element, $recursive, $ignoreHidden, $onlyNames);
                    $entry['name'] = $file;
                    $entry['type'] = self::DIR;
                    if ($onlyNames == false) {

                        $entry['editTime'] = @filectime($element);
                        $entry['accessTime'] = @fileatime($element);
                        $entry['size'] = self::getDirectorySize($element);
                    }
                } else {

                    //Normaler aufruf
                    $entry['name'] = $file;
                    $entry['type'] = self::DIR;
                    if ($onlyNames == false) {

                        $entry['editTime'] = @filectime($element);
                        $entry['accessTime'] = @fileatime($element);
                        $entry['size'] = self::getDirectorySize($element);
                    }
                }
            }

            $files[] = $entry;
        }

        @closedir($folder);
        return $files;
    }

    /**
     * sucht eine Datei in einem Ordner
     *
     * @param  String  $path      Pfad
     * @param  String  $file      Datei
     * @param  Boolean $recursive Unterordner Duchsuchen
     * @return String
     */
    public static function scannDirectory($path, $file, $recursive = true) {

        //existiert der Ordner
        if (!@is_dir($path)) {

            return null;
        }

        //Suchobjekt nicht leer
        $file = trim($file);
        if (strlen($file) <= 0) {

            return null;
        }

        //Ordner oeffnen
        $path = self::addTrailigSlash($path);
        $dir = @opendir($path);
        while ($filename = @readdir($dir)) {

            //. und .. ueberspringen
            if ($filename == '.' || $filename == '..') {

                continue;
            }

            //Datei
            if (trim($filename) == $file) {

                @closedir($dir);
                return $path . $filename;
            }

            //Ordner
            if ($recursive === true && @is_dir($path . $filename)) {

                $match = self::scannDirectory($path . $filename, $file, $recursive);
                if ($match !== null && strlen($match) > 0) {

                    @closedir($dir);
                    return $match;
                }
            }
        }

        @closedir($dir);
        return null;
    }

    /**
     * liest den Kompletten Inhalt einer Datei als Zeichenkette aus unter Verwendung von Locks
     *
     * @param  String  $file       Datei
     * @param  Integer $maxRetries Maximale Anzahl an versuchen
     * @return String
     */
    public static function fileGetContentsWithLock($file, $maxRetries = 100) {

        //Initialisieren
        $fileHandle = fopen($file, 'r');
        $retries = 0;

        //Fehler beim oeffnen
        if($fileHandle === false) {

            return false;
        }

        //auf Lock warten
        while(!flock($fileHandle, LOCK_SH | LOCK_NB)) {

            if($retries <= $maxRetries) {

                usleep(rand(1, 10000));
                $retries += 1;
            } else {

                //Lock nicht erhalten nach mehr als $maxRetries durchlaeufen
                return false;
            }
        }

        //Daten Lesen
        $data = '';
        while($row = fread($fileHandle, 2048)) {

            $data .= $row;
        }

        //Lock loesen
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);

        //Daten zurueckgeben
        return $data;
    }

    /**
     * schreibt den Inhalt einer Datei als Zeichenkette aus unter Verwendung von Locks
     *
     * @param  String  $file       Datei
     * @param  String  $content    Inhalt
     * @param  Integer $maxRetries Maximale Anzahl an versuchen
     * @return Boolean
     */
    public static function filePutContentsWithLock($file, $content, $maxRetries = 100) {

        //Initialisieren
        $fileHandle = fopen($file, 'w');
        $retries = 0;

        //Fehler beim oeffnen
        if($fileHandle === false) {

            return false;
        }

        //auf Lock warten
        while(!flock($fileHandle, LOCK_EX | LOCK_NB)) {

            if($retries <= $maxRetries) {

                usleep(rand(1, 10000));
                $retries += 1;
            } else {

                //Lock nicht erhalten nach mehr als $maxRetries durchlaeufen
                return false;
            }
        }

        //Daten schreiben
        ftruncate($fileHandle, 0);
        fwrite($fileHandle, $content);

        //Lock loesen
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);

        //Daten zurueckgeben
        return true;
    }

}
