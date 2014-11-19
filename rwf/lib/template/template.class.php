<?php

namespace RWF\Template;

//Imports
use RWF\Util\FileUtil;
use RWF\Template\Exception\TemplateException;

/**
 * Verwaltungsklasse fuer Templates
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Template {

    /**
     * Ordner in denen nach Templates gesucht wird
     * 
     * @var Array
     */
    protected $templateDirs = array();

    /**
     * Ordner in dem die Compilierten Templates Dielegn
     * 
     * @var String
     */
    protected $compileDir = '';

    /**
     * Templates werden immer neu Compiliert
     * 
     * @var Boolean
     */
    protected $forceCompile = false;

    /**
     * Compilierobjekt
     * 
     * @var \RWF\Template\TemplateCompiler
     */
    protected $compiler = null;

    /**
     * Template Prefix
     * 
     * @var String
     */
    protected $prefix = 'ucp_';

    /**
     * Template Variablen
     * 
     * @var Array
     */
    protected $var = array();

    /**
     * Namen der Compilierten Templates
     * 
     * @var Array
     */
    protected $compiledTemplates = array();

    /**
     * Dateiamen der Compilierten Templates
     * 
     * @var Array
     */
    protected $compiledNames = array();

    /**
     * Dateinamen der Templates
     * 
     * @var Array
     */
    protected $sourceNames = array();

    /**
     * aktuelles Template
     * 
     * @var String
     */
    protected $currentTemplate = '';

    /**
     * Block Stack
     * 
     * @var Array
     */
    protected $blocks = array();

    /**
     * @param Array   $templateDirs Ordner mit den Templates
     * @param String  $compileDir   Ordener mit den Compilierten Templates
     * @param String  $prefix       Prefix
     * @param Boolean $forceCompile Templates immer neu Compilieren
     */
    public function __construct($templateDirs = array(), $compileDir = '', $prefix = 'ucp_', $forceCompile = false) {

        //Template Ordner
        foreach ($templateDirs as $dir) {

            $this->addTemplateDir($dir);
        }
        //Compiler Cash
        $this->setCompileDir($compileDir);
        //prefix
        $this->setPrefix($prefix);
        //Immer neu Compilieren
        $this->enableForceCompile($forceCompile);
        //Systemvariablen einfuegen
        $this->assignSystemVars();
    }

    /**
     * setzt das Prefix fuer Compilierte Templates
     * 
     * @param String $prefix Prefix
     */
    public function setPrefix($prefix) {

        $this->prefix = $prefix;
    }

    /**
     * gibt das Prefix fuer Compilierte Templates zurueck
     * 
     * @return String
     */
    public function getPrefix() {

        return $this->prefix;
    }

    /**
     * regestriert einen Template Ordner
     * 
     * @param  String $dir Ordner
     * @throws \RWF\Template\Exception\TemplateException
     */
    public function addTemplateDir($dir) {

        if (is_dir($dir)) {

            $dir = FileUtil::addTrailigSlash($dir);
            $this->templateDirs[] = $dir;
            return;
        }

        throw new TemplateException('Der Template Ordner "' . $dir . '" existiert nicht', 1141);
    }

    /**
     * loescht einen Template Ordner
     * 
     * @param  String $dir Ordner
     */
    public function removeTemplateDir($dir) {

        $this->templateDirs = array_diff($this->templateDirs, array($dir));
    }

    /**
     * gibt eine Liste mit den Template Ordnern zurueck
     * 
     * @return Array
     */
    public function listTemplateDirs() {

        return $this->templateDirs;
    }

    /**
     * setzt den Ordner in de die Compilierten Templates gespeichert werden
     * 
     * @param  String $dir Ordner
     * @throws \RWF\Template\Exception\TemplateException
     */
    public function setCompileDir($dir) {

        if (is_dir($dir)) {

            //Ordner existiert -> speichern
            $dir = FileUtil::addTrailigSlash($dir);
            $this->compileDir = $dir;
            return;
        } elseif (FileUtil::createDirectory($dir, true, 0777)) {

            //Ordner existiert nicht -> erstellen und speichern
            $dir = FileUtil::addTrailigSlash($dir);
            $this->compileDir = $dir;
            return;
        }

        throw new TemplateException('Der Ordner "' . $dir . '" für die Compilierten Dateien existirt nicht und konnte nicht erstellt werden', 1142);
    }

    /**
     * gibt den Ordner in de die Compilierten Templates gespeichert werden zurueck
     * 
     * @return String
     */
    public function getCompileDir() {

        return $this->compileDir;
    }

    /**
     * Schaltet die immer neu Compilieren Funktion ein und aus
     * 
     * @param Boolean $enabled Ein wenn True
     */
    public function enableForceCompile($enabled) {

        if ($enabled == true) {

            $this->forceCompile = true;
            return;
        }
        $this->forceCompile = false;
    }

    /**
     * gibt an ob immer neu Compiliert wird
     * 
     * @return Boolean
     */
    public function isForceCompileEnabled() {

        return $this->forceCompile;
    }

    /**
     * gibt das Compilerobjekt zurueck
     * 
     * @return \RWF\Template\TemplateCompiler
     */
    public function getCompiler() {

        return $this->compiler;
    }

    /**
     * Regestriert Systemvariablen
     */
    protected function assignSystemVars() {

        $this->var['tpl']['get'] = & $_GET;
        $this->var['tpl']['post'] = & $_POST;
        $this->var['tpl']['cookie'] = & $_COOKIE;
        $this->var['tpl']['server'] = & $_SERVER;
        $this->var['tpl']['env'] = & $_ENV;
    }

    /**
     * regestriert eine Template Variable
     * 
     * @param Mixed $name  Name
     * @param Mixed $value Wert
     * @throws \RWF\Template\Exception\TemplateException
     */
    public function assign($name, $value = '') {

        if (is_array($name)) {

            foreach ($name as $index => $val) {

                if ($index != '') {

                    $this->assign($index, $val);
                }
            }
        } else {

            if ($name != 'tpl') {

                $this->var[$name] = $value;
            } else {

                throw new TemplateException('Die "tpl" Variable ist für die Template Engine reserviert', 1143);
            }
        }
    }

    /**
     * regestriert eine Template Variable als Referenz
     * 
     * @param Mixed $name  Name
     * @param Mixed $value Wert
     * @throws \RWF\Template\Exception\TemplateException
     */
    public function assignByRef($name, &$value = '') {

        if (is_array($name)) {

            foreach ($name as $index => $val) {

                if ($index != '') {

                    $this->assignByRef($index, $val);
                }
            }
        } else {

            if ($name != 'tpl') {

                $this->var[$name] = &$value;
            } else {

                throw new TemplateException('Die "tpl" Variable ist für die Template Engine reserviert', 1143);
            }
        }
    }

    /**
     * haengt einen String an eine Template Variable an
     * 
     * @param Mixed $name  Name
     * @param Mixed $value Wert
     * @throws \RWF\Template\Exception\TemplateException
     */
    public function append($name, $value) {

        if (is_array($name)) {

            foreach ($name as $index => $val) {

                if ($index != '') {

                    $this->append($index, $val);
                }
            }
        } else {

            if ($name != 'tpl') {
                if (isset($this->var[$name])) {

                    $this->var[$name] .= $value;
                } else {

                    $this->var[$name] = $value;
                }
            } else {

                throw new TemplateException('Die "tpl" Variable ist für die Template Engine reserviert', 1143);
            }
        }
    }

    /**
     * loescht eine Template Variable
     * 
     * @param String $name Variablenname
     */
    public function deleteAssign($name) {

        if (isset($this->var[$name])) {

            unset($this->var[$name]);
        }
    }

    /**
     * loescht alle Template Variablen
     */
    public function deleteAllAssigns() {

        $temp = $this->var['tpl'];
        $this->var = array();
        $this->var = $temp;
    }

    /**
     * gibt ein Template an den Browser aus
     * 
     * @param String $tpl Template Name
     */
    public function display($tpl) {

        $src = $this->getSourceName($tpl);
        $dest = $this->getCompiledName($tpl);

        if ($this->forceCompile === true || !$this->isCompiled($tpl) || @filemtime($src) > @filemtime($dest)) {

            $this->compileTemplate($tpl, $src, $dest);
        }

        include($dest);
        $this->currentTemplate = $tpl;
    }

    /**
     * gibt das Template als String zurueck
     * 
     * @param  String $tpl Template Name
     * @return String
     */
    public function fetchString($tpl) {

        ob_start();
        $this->display($tpl);
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }

    /**
     * includiert ein Template
     * 
     * @param String  $tpl     Template Name
     * @param Boolean $sandbox in einer Sandbox ausfuehren
     */
    protected function includeTemplate($tpl, $sandbox = false) {

        if ($sandbox == true) {

            $vars = $this->var;
            $this->var = array();
            $this->display($tpl);
            $this->var = $vars;
        } else {

            $this->display($tpl);
        }
    }

    /**
     * gibt den Namen des Compilierten Templates zurueck
     * 
     * @param  String $tpl Template Name
     * @return String
     * @throws \RWF\Template\Exception\TemplateException
     */
    protected function getCompiledName($tpl) {

        foreach ($this->compiledTemplates as $template) {

            if ($template[0] == $tpl) {

                return $template[1];
            }
        }

        foreach ($this->templateDirs as $dir) {

            $path = FileUtil::scannDirectory($dir, $tpl);
            if ($path !== null) {

                $compiledName = $this->compileDir . $this->prefix . urlencode(str_replace(array('.html', '/'), array('', '_'), $tpl)) . '_' . md5($tpl) . '.php';
                $this->compiledTemplates[] = array(0 => $tpl, 1 => $compiledName);
                return $compiledName;
            }
        }

        throw new TemplateException('Das Template "' . $tpl . '" konnte nicht gefunden werden', 1144);
    }

    /**
     * gibt an ob ein Template Compiliert ist
     * 
     * @param  String  $tpl Template Name
     * @return Boolean
     */
    protected function isCompiled($tpl) {

        foreach ($this->compiledTemplates as $name) {

            if ($name == $tpl) {

                return true;
            }
        }

        $compiledFile = $this->getCompiledName($tpl);
        if (file_exists($compiledFile)) {

            $this->compiledTemplates[] = $tpl;
            return true;
        }

        return false;
    }

    /**
     * gibt den Namen des Templates zurueck
     * 
     * @param  String $tpl Template Name
     * @return String      Pfad
     * @throws \RWF\Template\Exception\TemplateException
     */
    protected function getSourceName($tpl) {

        foreach ($this->sourceNames as $template) {

            if ($template[0] == $tpl) {

                return $template[1];
            }
        }

        foreach ($this->templateDirs as $dir) {

            $path = FileUtil::scannDirectory($dir, $tpl);
            if ($path !== null) {

                $this->sourceNames[] = array(0 => $tpl, 1 => $path);
                return $path;
            }
        }

        throw new TemplateException('Das Template "' . $tpl . '" konnte nicht gefunden werden', 1144);
    }

    /**
     * Compiliert ein Template
     * 
     * @param String $tpl          Template Name
     * @param String $sourceFile   Pfad zur Quelldatei
     * @param String $compiledFile Pfad zur Compilierten Datei
     */
    protected function compileTemplate($tpl, $sourceFile, $compiledFile) {

        if ($this->compiler === null) {

            $this->compiler = new TemplateCompiler($this);
        }

        $this->compiler->compile($tpl, $sourceFile, $compiledFile);

    }

}
