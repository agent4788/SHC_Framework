<?php

namespace RWF\Error;

//Imports
use RWF\ClassLoader\Exception\ClassNotFoundException;
use RWF\XML\Exception\XmlException;

/**
 * Fehlerbehandlung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Error {

    /**
     * Fehlermeldungen Anzeigen
     * 
     * @var Boolean
     */
    protected $displayErrors = false;

    /**
     * Fehlermeldungen Loggen
     * 
     * @var Boolean
     */
    protected $logErrors = true;

    /**
     * Fehlerbehandlung initialisieren
     *
     * @param Boolean $initAsErrorHandler sich selbst fuer die Fehlerbehandlung anmelden
     */
    public function __construct($initAsErrorHandler = true) {

        //Fehlerfunktionen registrieren
        if($initAsErrorHandler == true) {

            set_error_handler(array(&$this, 'handlePhpError'));
            set_exception_handler(array(&$this, 'handleException'));
            libxml_use_internal_errors(true);
        }
    }

    /**
     * schaltet das Anzeigen von Fehlern ein
     * 
     * @param Boolean $enabled
     */
    public function enableDisplayErrors($enabled) {

        if ($enabled == true) {

            $this->displayErrors = true;
            return;
        }
        $this->displayErrors = false;
    }

    /**
     * schaltet das Loggen von Fehlern ein
     * .
     * @param Boolean $enabled
     */
    public function enableLogErrors($enabled) {

        if ($enabled == true) {

            $this->logErrors = true;
            return;
        }
        $this->logErrors = false;
    }

    /**
     * PHP Fehler
     * 
     * @param Integer $type
     * @param String  $message
     * @param String  $file
     * @param Integer $line
     * @param Array   $context
     * @param String  $logFile Datei in die das Fehlerlog geschrieben werden soll
     */
    public function handlePhpError($type, $message, $file, $line, $context = array(), $logFile = 'error.log') {

        //Fehler mit vorrangestelltem @ ignorieren
        if (ini_get('error_reporting') == 0) {

            return;
        }

        //Typ ermitteln
        switch ($type) {

            case E_NOTICE:
            case E_USER_NOTICE:
                $errorName = "Notice";
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $errorName = "Warning";
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $errorName = "Fatal Error";
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $errorName = "Deprecated";
                break;
            case E_STRICT:
                $errorName = "Strict Error";
                break;
            default:
                $errorName = "Unknown";
                break;
        }

        $file = str_replace(PATH_RWF, '', $file);
        $trace = debug_backtrace();

        //Anzeigen
        if ($this->displayErrors === true) {

            //Datum und Zeit
            $date = new \DateTime();
            
            if (PHP_SAPI == 'cli') {

                echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                echo "// PHP " . $errorName . "\n";
                echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                echo "Datei:         " . $file . "\n";
                echo "Zeile:         " . $line . "\n";
                echo "Meldung:       " . $message . "\n";
                echo "Fehler Nummer: " . $type . "\n";
                echo "Zeit:          " . $date->format('d.m.Y H:i:s') . "\n";

                echo "//Trace///////////////////////////////////////////////////////////////////////////////////////////\n";
                foreach ($trace as $index => $row) {

                    //Daten Aufbereiten
                    $file = str_replace(PATH_RWF, '', (isset($row['file']) ? $row['file'] : ''));

                    $args = '';
                    $comma = '';
                    if (isset($row['args'])) {

                        foreach ($row['args'] as $item) {

                            if (is_string($item)) {

                                if (preg_match('#' . PATH_RWF . '#', $item)) {

                                    $item = str_replace(PATH_RWF, '', $item);
                                    $args .= $comma . "'" . $item . "'";
                                } else {

                                    $args .= $comma . "'" . (strlen($item) > 30 ? substr($item, 0, 30) . '...' : $item) . "'";
                                }
                                $comma = ', ';
                            } elseif (is_int($item) || is_float($item)) {

                                $args .= $comma . $item;
                                $comma = ', ';
                            } elseif (is_array($item)) {

                                $args .= $comma . 'Array(' . count($item) . ')';
                                $comma = ', ';
                            } elseif (is_object($item)) {

                                $args .= $comma . get_class($item);
                                $comma = ', ';
                            }
                        }
                    }

                    echo '#' . $index . ' ' . $file . ' @ Line: ' . (isset($row['line']) ? $row['line'] : 0) . ' ';
                    echo (isset($row['class']) ? $row['class'] . $row['type'] : '') . $row['function'] . '(' . $args . ')' . "\n";
                }

                echo '#' . ++$index . ' {main}' . "\n\n";
            } else {

                $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head>';
                $html .= '<title>PHP ' . $this->html($errorName) . '</title>';
                $html .= '<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><meta http-equiv="content-style-type" content="text/css" /><meta http-equiv="content-language" content="de" />';
                $html .= '<meta name="robots" content="index,follow" /><meta name="Revisit" content="After 14 days" /><style type="text/css">';
                $html .= 'body {background-color: #efefef; font-family: verdana, arial, sans-serif; color: #000000;}#error_box {width: 65%; margin: 0 auto; margin-top: 150px; background-color: #ffffff; border: 1px solid #e4e4e4; padding: 20px;}#';
                $html .= 'content {border: 1px solid #b60101;}#ueberschrift {background-color: #b60101; color: #ffffff; font-size: 14px; font-weight: bold; padding: 4px;}#error {margin: 5px; font-size: 12px;}';
                $html .= 'table {width: 100%;}table tr {margin-bottom: 3px;}table tr td {border-bottom: 1px solid #000000; padding: 2px;}table tr td:first-child {font-weight: bold; width: 50px;}</style>';
                $html .= '</head><body><div  style="color: #000000;" id="error_box"><div id="content">';
                $html .= '<div id="ueberschrift">PHP ' . $this->html($errorName) . '</div>';
                $html .= '<div id="error">';
                $html .= '<p>' . $this->html($message) . '</p>';
                $html .= '<table><tr><td>File:</td>';
                $html .= '<td>' . $this->html($file) . '</td>';
                $html .= '</tr><tr><td>Line:</td>';
                $html .= '<td>' . $this->html($line) . '</td>';
                $html .= '</tr><tr><td>Code:</td>';
                $html .= '<td>' . $this->html($type) . '</td>';
                $html .= '</tr><tr><td>Stack:</td>';
                $html .= '<td>' . $this->formatStackTrace($trace) . '</td>';
                $html .= '</tr></table></div></div></div></body></html>';

                echo utf8_encode($html);
            }
        } else {

            $this->displayDefaultError();
        }

        //Loggen
        if ($this->logErrors === true) {

            $this->logError($logFile, $errorName, $file, $line, $message, $type, $trace);
        }

        //Bearbeitung abbrechen
        exit(1);
    }

    /**
     * Ausnahmen behandeln
     * 
     * @param \Exception $e
     * @param String    $logFile Datei in die das Fehlerlog geschrieben werden soll
     */
    public function handleException(\Exception $e, $logFile = 'exception.log') {

        //XML Fehler
        if ($e instanceof XmlException) {

            $this->handleXMLException($e);
            return;
        }

        if ($e instanceof ClassNotFoundException) {

            $this->handelClassNotFoundException($e);
            return;
        }

        //Anzeigen
        if ($this->displayErrors) {

            //Hilfsvariablen vorbereiten
            $file = str_replace(PATH_RWF, '', $e->getFile());
            $date = new \DateTime();
            
            if (PHP_SAPI == 'cli') {

                echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                echo "// System error\n";
                echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                echo "Datei:         " . $file . "\n";
                echo "Zeile:         " . $e->getLine() . "\n";
                echo "Meldung:       " . $e->getMessage() . "\n";
                echo "Klasse:        " . get_class($e) . "\n";
                echo "Fehler Nummer: " . $e->getCode() . "\n";
                echo "Zeit:          " . $date->format('d.m.Y H:i:s') . "\n";

                echo "//Trace///////////////////////////////////////////////////////////////////////////////////////////\n";
                foreach ($e->getTrace() as $index => $row) {

                    //Daten Aufbereiten
                    $file = str_replace(PATH_RWF, '', (isset($row['file']) ? $row['file'] : ''));

                    $args = '';
                    $comma = '';
                    if (isset($row['args'])) {

                        foreach ($row['args'] as $item) {

                            if (is_string($item)) {

                                if (preg_match('#' . PATH_RWF . '#', $item)) {

                                    $item = str_replace(PATH_RWF, '', $item);
                                    $args .= $comma . "'" . $item . "'";
                                } else {

                                    $args .= $comma . "'" . (strlen($item) > 30 ? substr($item, 0, 30) . '...' : $item) . "'";
                                }
                                $comma = ', ';
                            } elseif (is_int($item) || is_float($item)) {

                                $args .= $comma . $item;
                                $comma = ', ';
                            } elseif (is_array($item)) {

                                $args .= $comma . 'Array(' . count($item) . ')';
                                $comma = ', ';
                            } elseif (is_object($item)) {

                                $args .= $comma . get_class($item);
                                $comma = ', ';
                            }
                        }
                    }

                    echo '#' . $index . ' ' . $file . ' @ Line: ' . (isset($row['line']) ? $row['line'] : 0) . ' ';
                    echo (isset($row['class']) ? $row['class'] . $row['type'] : '') . $row['function'] . '(' . $args . ')' . "\n";
                }

                echo '#' . ++$index . ' {main}' . "\n\n";
            } else {

                $html = $this->createHtmlErrorHeader('System Exception - ' . $file . ' in Zeile ' . $e->getLine());
                $html .= '<body><div  style="color: #000000;" id="error_box"><div id="content">';
                $html .= '<div id="ueberschrift">System Error</div>';
                $html .= '<div id="error">';
                $html .= '<p>' . $this->html($e->getMessage()) . '</p>';
                $html .= '<table><tr><td>Klasse:</td>';
                $html .= '<td>' . $this->html(get_class($e)) . '</td>';
                $html .= '</tr><tr><td>File:</td>';
                $html .= '<td>' . $this->html($file) . '</td>';
                $html .= '</tr><tr><td>Line:</td>';
                $html .= '<td>' . $this->html($e->getLine()) . '</td>';
                $html .= '</tr><tr><td>Code:</td>';
                $html .= '<td>' . $this->html($e->getCode()) . '</td>';
                $html .= '</tr><tr><td>Stack:</td>';
                $html .= '<td>' . $this->formatStackTrace($e->getTrace()) . '</td>';
                $html .= '</tr></table></div></div></div></body></html>';

                echo utf8_encode($html);
            }
        } else {

            $this->displayDefaultError();
        }

        //Loggen
        if ($this->logErrors) {

            $this->logError($logFile, 'System Exception', $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), $e->getTrace(), array('Klasse' => get_class($e)));
        }

        exit(1);
    }

    /**
     * Ausnahmen behandeln
     * 
     * @param \Exception $e
     * @param Boolean    $logOnly nur Log Eintrag erzeugen
     * @param String     $logFile Datei in die das Fehlerlog geschrieben werden soll
     */
    public function handleXMLException(XMLException $e, $logOnly = false, $logFile = 'xml.log') {

        //Anzeigen
        if ($this->displayErrors && $logOnly == false) {

            //Hilfsvariablen vorbereiten
            $file = str_replace(PATH_RWF, '', $e->getFile());
            $date = new \DateTime();

            if (PHP_SAPI == 'cli') {

                echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                echo "// XML Fehler\n";
                echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                echo "Datei:         " . $file . "\n";
                echo "Zeile:         " . $e->getLine() . "\n";
                echo "Meldung:       " . $e->getMessage() . "\n";
                echo "Klasse:        " . get_class($e) . "\n";
                echo "Fehler Nummer: " . $e->getCode() . "\n";
                echo "Zeit:          " . $date->format('d.m.Y H:i:s') . "\n";

                echo "//XML error///////////////////////////////////////////////////////////////////////////////////////\n";
                $first = true;
                foreach ($e->getXmlErrors() as $error) {

                    if ($error instanceof \libXMLError) {

                        if ($first === true) {

                            echo "\n";
                        }

                        echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                        echo 'Level: ' . ($error->level == LIBXML_ERR_WARNING ? 'Warnung' : 'Fehler') . "\n";
                        echo 'Code: ' . $error->code . "\n";
                        echo 'Message: ' . $error->message;
                        echo 'Zeile: ' . $error->line . "\n";
                        $first = false;
                    }
                }

                echo "//Trace///////////////////////////////////////////////////////////////////////////////////////////\n";
                foreach ($e->getTrace() as $index => $row) {

                    //Daten Aufbereiten
                    $file = str_replace(PATH_RWF, '', (isset($row['file']) ? $row['file'] : ''));

                    $args = '';
                    $comma = '';
                    if (isset($row['args'])) {

                        foreach ($row['args'] as $item) {

                            if (is_string($item)) {

                                if (preg_match('#' . PATH_RWF . '#', $item)) {

                                    $item = str_replace(PATH_RWF, '', $item);
                                    $args .= $comma . "'" . $item . "'";
                                } else {

                                    $args .= $comma . "'" . (strlen($item) > 30 ? substr($item, 0, 30) . '...' : $item) . "'";
                                }
                                $comma = ', ';
                            } elseif (is_int($item) || is_float($item)) {

                                $args .= $comma . $item;
                                $comma = ', ';
                            } elseif (is_array($item)) {

                                $args .= $comma . 'Array(' . count($item) . ')';
                                $comma = ', ';
                            } elseif (is_object($item)) {

                                $args .= $comma . get_class($item);
                                $comma = ', ';
                            }
                        }
                    }

                    echo '#' . $index . ' ' . $file . ' @ Line: ' . (isset($row['line']) ? $row['line'] : 0) . ' ';
                    echo (isset($row['class']) ? $row['class'] . $row['type'] : '') . $row['function'] . '(' . $args . ')' . "\n";
                }

                echo '#' . ++$index . ' {main}' . "\n\n";
            } else {

                $html = $this->createHtmlErrorHeader('XMLException - ' . $file . ' in Zeile ' . $e->getLine());
                $html .= '<body><div style="color: #000000;" id="error_box"><div id="content">';
                $html .= '<div id="ueberschrift">XML Error</div>';
                $html .= '<div id="error">';
                $html .= '<p>' . $this->html($e->getMessage()) . '</p>';
                $html .= '<table><tr><td>Klasse:</td>';
                $html .= '<td>' . $this->html(get_class($e)) . '</td>';
                $html .= '</tr><tr><td>File:</td>';
                $html .= '<td>' . $this->html($file) . '</td>';
                $html .= '</tr><tr><td>Line:</td>';
                $html .= '<td>' . $this->html($e->getLine()) . '</td>';
                $html .= '</tr><tr><td>Code:</td>';
                $html .= '<td>' . $this->html($e->getCode()) . '</td>';
                $html .= '</tr><tr><td>Stack:</td>';
                $html .= '<td>' . $this->formatStackTrace($e->getTrace()) . '</td>';
                $html .= '</tr><tr><td>XML:</td>';
                $html .= '<td>';

                $first = true;
                foreach ($e->getXmlErrors() as $error) {

                    if ($error instanceof \libXMLError) {

                        if ($first === false) {

                            $html .= '<div><hr/></div>';
                        }
                        $html .= '<div><b>Level:</b> ' . ($error->level == LIBXML_ERR_WARNING ? 'Warnung' : 'Fehler') . '</div>';
                        $html .= '<div><b>Code:</b> ' . $error->code . '</div>';
                        $html .= '<div><b>Message:</b> ' . $error->message . '</div>';
                        $html .= '<div><b>Zeile:</b> ' . $error->line . '</div>';
                        $first = false;
                    }
                }

                $html .= '</td>';
                $html .= '</tr></table></div></div></div></body></html>';

                echo utf8_encode($html);
            }
        } elseif($logOnly == false) {

            $this->displayDefaultError();
        }

        //Loggen
        if ($this->logErrors || $logOnly == true) {

            $first = true;
            $data = '';
            foreach ($e->getXmlErrors() as $error) {

                if ($error instanceof \libXMLError) {

                    if ($first === true) {

                        $data .= "\n";
                    }

                    $data .= "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                    $data .= 'Level: ' . ($error->level == LIBXML_ERR_WARNING ? 'Warnung' : 'Fehler') . "\n";
                    $data .= 'Code: ' . $error->code . "\n";
                    $data .= 'Message: ' . $error->message;
                    $data .= 'Zeile: ' . $error->line . "\n";
                    $first = false;
                }
            }

            $this->logError($logFile, 'XML Fehler', $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), $e->getTrace(), array('XML Fehler' => $data));
        }

        if($logOnly == false) {

            exit(1);
        }
    }
    
    /**
     * behandelt alle Fehler vom ClassLoader
     * 
     * @param \RWF\ClassLoader\Exception\ClassNotFoundException $e
     * @param String    $logFile Datei in die das Fehlerlog geschrieben werden soll
     */
    protected function handelClassNotFoundException(ClassNotFoundException $e, $logFile = 'exception.log') {
        
        //Anzeigen
        if ($this->displayErrors) {

            //hilfsvariablen Vorbereiten
            $file = str_replace(PATH_RWF, '', $e->getFile());
            $date = new \DateTime();
            
            if (PHP_SAPI == 'cli') {

                echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                echo "// System error\n";
                echo "//////////////////////////////////////////////////////////////////////////////////////////////////\n";
                echo "Datei:         " . $file . "\n";
                echo "Zeile:         " . $e->getLine() . "\n";
                echo "Meldung:       " . $e->getMessage() . "\n";
                echo "Klasse:        " . $e->getClass() . "\n";
                echo "Fehler Nummer: " . $e->getCode() . "\n";
                echo "Zeit:          " . $date->format('d.m.Y H:i:s') . "\n";

                echo "//Trace///////////////////////////////////////////////////////////////////////////////////////////\n";
                foreach ($e->getTrace() as $index => $row) {

                    //Daten Aufbereiten
                    $file = str_replace(PATH_RWF, '', (isset($row['file']) ? $row['file'] : ''));

                    $args = '';
                    $comma = '';
                    if (isset($row['args'])) {

                        foreach ($row['args'] as $item) {

                            if (is_string($item)) {

                                if (preg_match('#' . PATH_RWF . '#', $item)) {

                                    $item = str_replace(PATH_RWF, '', $item);
                                    $args .= $comma . "'" . $item . "'";
                                } else {

                                    $args .= $comma . "'" . (strlen($item) > 30 ? substr($item, 0, 30) . '...' : $item) . "'";
                                }
                                $comma = ', ';
                            } elseif (is_int($item) || is_float($item)) {

                                $args .= $comma . $item;
                                $comma = ', ';
                            } elseif (is_array($item)) {

                                $args .= $comma . 'Array(' . count($item) . ')';
                                $comma = ', ';
                            } elseif (is_object($item)) {

                                $args .= $comma . get_class($item);
                                $comma = ', ';
                            }
                        }
                    }

                    echo '#' . $index . ' ' . $file . ' @ Line: ' . (isset($row['line']) ? $row['line'] : 0) . ' ';
                    echo (isset($row['class']) ? $row['class'] . $row['type'] : '') . $row['function'] . '(' . $args . ')' . "\n";
                }

                echo '#' . ++$index . ' {main}' . "\n\n";
            } else {

                $html = $this->createHtmlErrorHeader('SystemException - ' . $file . ' in Zeile ' . $e->getLine());
                $html .= '<body><div  style="color: #000000;" id="error_box"><div id="content">';
                $html .= '<div id="ueberschrift">System Error</div>';
                $html .= '<div id="error">';
                $html .= '<p>' . $this->html($e->getMessage()) . '</p>';
                $html .= '<table><tr><td>Klasse:</td>';
                $html .= '<td>' . $this->html($e->getClass()) . '</td>';
                $html .= '</tr><tr><td>File:</td>';
                $html .= '<td>' . $this->html($file) . '</td>';
                $html .= '</tr><tr><td>Line:</td>';
                $html .= '<td>' . $this->html($e->getLine()) . '</td>';
                $html .= '</tr><tr><td>Code:</td>';
                $html .= '<td>' . $this->html($e->getCode()) . '</td>';
                $html .= '</tr><tr><td>Stack:</td>';
                $html .= '<td>' . $this->formatStackTrace($e->getTrace()) . '</td>';
                $html .= '</tr></table></div></div></div></body></html>';

                echo utf8_encode($html);
            }
        } else {

            $this->displayDefaultError();
        }

        //Loggen
        if ($this->logErrors) {

            $this->logError($logFile, 'Autoload Fehler', $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), $e->getTrace(), array('Klasse' => $e->getClass()));
        }

        exit(1);
    }

    /**
     * Zeigt eine Standart Fehlermeldung an
     */
    protected function displayDefaultError() {

        if (PHP_SAPI == 'cli') {
            
            echo "It is an error occurred, we apologize for that, please try again later.";
        } else {

            $html = $this->createHtmlErrorHeader('There was an error');
            $html .= '<body><div  style="color: #000000;" id="error_box"><div id="content">';
            $html .= '<div id="ueberschrift">There was an error</div>';
            $html .= '<div id="error">';
            $html .= '<p>It is an error occurred, we apologize for that, please try again later.</p>';
            $html .= '</div></div></div></body></html>';

            echo utf8_encode($html);
        }
    }

    /**
     * erzeigt den HTML Head Bereichder error Seiten
     * 
     * @param  String $title Titel
     * @return String
     */
    protected function createHtmlErrorHeader($title) {

        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
        $html .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . "\n";
        $html .= '<head>' . "\n";
        $html .= '<title>' . $this->html($title) . '</title>' . "\n";
        $html .= '<meta http-equiv="content-type" content="text/html;charset=UTF-8" />' . "\n";
        $html .= '<meta http-equiv="content-style-type" content="text/css" />' . "\n";
        $html .= '<meta http-equiv="content-language" content="en" />' . "\n";
        $html .= '<meta name="robots" content="index,follow" />' . "\n";
        $html .= '<meta name="Revisit" content="After 14 days" />' . "\n";
        $html .= '<style type="text/css">' . "\n";
        $html .= 'body {background-color: #efefef; font-family: verdana, arial, sans-serif; color: #000000; !important}' . "\n";
        $html .= '#error_box {width: 65%; margin: 0 auto; margin-top: 150px; background-color: #ffffff; border: 1px solid #e4e4e4; padding: 20px;}' . "\n";
        $html .= '#content {border: 1px solid #b60101;}' . "\n";
        $html .= '#ueberschrift {background-color: #b60101; color: #ffffff; font-size: 14px; font-weight: bold; padding: 4px;}' . "\n";
        $html .= '#error {margin: 5px; font-size: 12px;}' . "\n";
        $html .= 'table {width: 100%;}' . "\n";
        $html .= 'table tr {margin-bottom: 3px;}' . "\n";
        $html .= 'table tr td {border-bottom: 1px solid #000000; padding: 2px;}' . "\n";
        $html .= 'table tr td:first-child {font-weight: bold; width: 50px;}' . "\n";
        $html .= '</style>' . "\n";
        $html .= '</head>' . "\n";

        return $html;
    }

    /**
     * Formatiert den StackTrace
     * 
     * @param Array $trace
     */
    protected function formatStackTrace(array $trace) {

        $result = '<div><dl>';
        foreach ($trace as $index => $row) {

            //Daten Aufbereiten
            $file = str_replace(PATH_RWF, '', (isset($row['file']) ? $row['file'] : ''));

            $args = '';
            $comma = '';
            if (isset($row['args'])) {

                foreach ($row['args'] as $item) {

                    if (is_string($item)) {

                        $args .= $comma . "'" . (strlen($item) < 30 ? substr($item, 0, 30) . '...' : $item) . "'";
                        $comma = ', ';
                    } elseif (is_int($item) || is_float($item)) {

                        $args .= $comma . $item;
                        $comma = ', ';
                    } elseif (is_array($item)) {

                        $args .= $comma . 'Array(' . count($item) . ')';
                        $comma = ', ';
                    } elseif (is_object($item)) {

                        $args .= $comma . get_class($item);
                        $comma = ', ';
                    }
                }
            }

            $result .= '';
            $result .= '<dt>';
            $result .= $this->html('#' . $index . ' ' . $file . ' @ Line: ' . (isset($row['line']) ? $row['line'] : 0));
            $result .= '</dt>';
            $result .= '<dd>';
            $result .= $this->html((isset($row['class']) ? $row['class'] . $row['type'] : '') . $row['function'] . '(' . $args . ')');
            $result .= '</dd>' . "\n";
        }

        $result .= '<dt>';
        $result .= $this->html('#' . ++$index . ' {main}');
        $result .= '</dt>' . "\n";

        $result .= '</dl></div>' . "\n";
        return $result;
    }

    /**
     * Schreibt die Fehler Log Datei
     * 
     * @param Array  $data
     * @param String $file
     */
    protected function logError($logFile, $errorType, $file, $line, $message, $code, array $trace, array $additional = array()) {

        $file = str_replace(PATH_RWF, '', $file);
        $date = new \DateTime();

        $handle = fopen(PATH_RWF_LOG . $logFile, 'a');
        fwrite($handle, "//////////////////////////////////////////////////////////////////////////////////////////////////\n");
        fwrite($handle, "// " . $errorType . "\n");
        fwrite($handle, "//////////////////////////////////////////////////////////////////////////////////////////////////\n");
        fwrite($handle, "Datei:         " . $file . "\n");
        fwrite($handle, "Zeile:         " . $line . "\n");
        fwrite($handle, "Meldung:       " . $message . "\n");
        fwrite($handle, "Fehler Nummer: " . $code . "\n");
        fwrite($handle, "Zeit:          " . $date->format('d.m.Y H:i:s') . "\n");
        foreach ($additional as $index => $additionalRow) {
            fwrite($handle, str_pad($index . ":", 15) . $additionalRow . "\n");
        }

        fwrite($handle, "//Trace///////////////////////////////////////////////////////////////////////////////////////////\n");
        foreach ($trace as $index => $row) {

            //Daten Aufbereiten
            $file = str_replace(PATH_RWF, '', (isset($row['file']) ? $row['file'] : ''));

            $args = '';
            $comma = '';
            if (isset($row['args'])) {

                foreach ($row['args'] as $item) {

                    if (is_string($item)) {

                        if (preg_match('#' . PATH_RWF . '#', $item)) {

                            $item = str_replace(PATH_RWF, '', $item);
                            $args .= $comma . "'" . $item . "'";
                        } else {

                            $args .= $comma . "'" . (strlen($item) > 30 ? substr($item, 0, 30) . '...' : $item) . "'";
                        }
                        $comma = ', ';
                    } elseif (is_int($item) || is_float($item)) {

                        $args .= $comma . $item;
                        $comma = ', ';
                    } elseif (is_array($item)) {

                        $args .= $comma . 'Array(' . count($item) . ')';
                        $comma = ', ';
                    } elseif (is_object($item)) {

                        $args .= $comma . get_class($item);
                        $comma = ', ';
                    }
                }
            }

            fwrite($handle, '#' . $index . ' ' . $file . ' @ Line: ' . (isset($row['line']) ? $row['line'] : 0) . ' ');
            fwrite($handle, (isset($row['class']) ? $row['class'] . $row['type'] : '') . $row['function'] . '(' . $args . ')' . "\n");
        }

        fwrite($handle, '#' . ++$index . ' {main}' . "\n\n");
        fclose($handle);
    }
            
    /**
     * ersetzt HTML Sonderzeichen
     * 
     * @param  String $str
     * @return String
     */
    protected function html($str) {

        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

}

?>