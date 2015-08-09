<?php

namespace RWF\Template;

//Imports
use RWF\Template\Exception\TemplateCompilerException;
use RWF\Template\Exception\TemplateCompilationException;
use RWF\Util\String;
use RWF\Util\FileUtil;
use RWF\Util\ArrayUtil;

/**
 * Compiliert Templates
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TemplateCompiler {

    /**
     * Template Objekt
     * 
     * @var \RWF\Template\Template
     */
    protected $template = null;

    /**
     * Template Name
     * 
     * @var String
     */
    protected $templateName = '';

    /**
     * Quelldatei
     * 
     * @var String
     */
    protected $srcFile = '';

    /**
     * Zieldatei
     * 
     * @var String
     */
    protected $destFile = '';

    /**
     * Ordner in dem die Plugins liegen
     * 
     * @var Array
     */
    protected $pluginDirs = array();

    /**
     * Plugins
     * 
     * @var Array
     */
    protected $plugins = array();

    /**
     * Pre- und Postfilter
     * 
     * @var Array
     */
    protected $filter = array();

    /**
     * Literale
     * 
     * @var Array
     */
    protected $literals = array();

    /**
     * Zufaelliger hash
     * 
     * @var String
     */
    protected $hash = '';

    /**
     * linker Begrenzer
     * 
     * @var String
     */
    protected $leftDelimiter = '{';

    /**
     * linker Begrenzer
     * 
     * @var String
     */
    protected $ld = '';

    /**
     * rechter Begrenzer
     * 
     * @var String
     */
    protected $rightDelimiter = '}';

    /**
     * rechter Begrenzer
     * 
     * @var String
     */
    protected $rd = '';

    /**
     * Regulaere Ausdruecke
     * 
     * @var Array
     */
    protected $pattern = array();

    /**
     * verfuegbare Funktionen
     * 
     * @var Array
     */
    protected $availableFunctions = array(
        'isset', 'count', 'empty', 'unset'
    );

    /**
     * gesperrte Funktionen im Template
     * 
     * @var Array
     */
    protected $disabledeFunctions = array(
        'system', 'exec', 'passthru', 'shell_exec', //CLI
        'include', 'require', 'include_once', 'require_once', //Includes
        'eval', 'virtual', 'call_user_func_array', 'call_user_func', 'assert' //Code functions
    );

    /**
     * geoeffnete Tags
     * 
     * @var Array
     */
    protected $opendTags = array();

    /**
     * Capture Argumente Speicher
     * 
     * @var Array
     */
    protected $capture = array();

    /**
     * Zeilennummer
     * 
     * @var Integer
     */
    protected $line = 1;

    /**
     * Regulaere Ausdruecke
     * 
     * @var String
     */
    protected $variableOperatorPattern = '',
            $conditionOperatorPattern = '',
            $escapedPattern = '',
            $validVarnamePattern = '',
            $doubleQuotePattern = '',
            $singleQuotePattern = '',
            $quotePattern = '',
            $numericPattern = '';

    /**
     * Zwischenspeicher für Quotes
     * 
     * @var Array
     */
    protected $quoteStack = array();

    /**
     * Zeichenkette
     * 
     * @var Integer
     */
    const STRING = 1;

    /**
     * Zahl
     * 
     * @var Integer
     */
    const NUMERIC = 2;

    /**
     * Variable
     * 
     * @var Integer
     */
    const VARIABLE = 4;

    public function __construct(Template $template, array $pluginDirs = array()) {

        $this->template = $template;
        $this->pluginDirs = $pluginDirs;
        $this->hash = md5(TIME_NOW);

        //Template Plugin Ordner vorbereiten
        if(count($this->pluginDirs) < 1) {

            //Default Odrner nutzen (RWF Namespace Template\Plugin\....
            $this->pluginDirs['RWF'] = PATH_RWF_CLASSES . 'template/plugin/';
            //Template Plugins der App falls vorhanden
            if(file_exists(PATH_BASE . APP_NAME .'/lib/template/plugin')) {
                $this->pluginDirs[APP_NAME] = PATH_BASE . APP_NAME .'/lib/template/plugin/';
            }
        }

        //Delimiter escapen
        $this->ld = $this->quote($this->leftDelimiter) . '(?=\S)';
        $this->rd = '(?<=\S)' . $this->quote($this->rightDelimiter);

        //Plugins Laden
        $this->loadPlugins();

        //Regex Initialisieren
        $this->initPattern();
    }

    /**
     * escape Regex Sonderzeichen
     * 
     * @param  String $str Zeichenkette
     * @return String
     */
    protected function quote($str) {

        return preg_quote($str, '#');
    }

    /**
     * setzt die Blockbegrenzer
     * 
     * @param String $left  linker Begrenzer
     * @param String $right rechter Begrenzer
     */
    public function setDelimiter($left, $right) {

        $this->leftDelimiter = $left;
        $this->rightDelimiter = $right;
        $this->ld = $this->quote($this->leftDelimiter) . '(?=\S)';
        $this->rd = '(?<=\S)' . $this->quote($this->rightDelimiter);
    }

    /**
     * gibt den linken Begrenzer zurueck
     * 
     * @return String
     */
    public function getLeftDelimiter() {

        return $this->leftDelimiter;
    }

    /**
     * gibt den linken Begrenzer zurueck
     * 
     * @return String
     */
    public function getLeftDelimiterPattern() {

        return $this->ld;
    }

    /**
     * gibt den rechten Begrenzer zurueck
     * 
     * @return String
     */
    public function getRightDelimiter() {

        return $this->rightDelimiter;
    }

    /**
     * gibt den rechten Begrenzer zurueck
     * 
     * @return String
     */
    public function getRightDelimiterPattern() {

        return $this->rd;
    }

    /**
     * gibt den Plugin Ordner zurueck
     * 
     * @return Array
     */
    public function getPluginDirs() {

        return $this->pluginDirs;
    }

    /**
     * gibt den Namen des Templates zurueck
     * 
     * @return String
     */
    public function getTemplateName() {

        return $this->templateName;
    }

    /**
     * gibt die Zeile im Template zurueck
     * 
     * @return Integer
     */
    public function getCurrentLine() {

        return $this->line;
    }

    /**
     * meldet ein neues oeffnendes Tag
     * 
     * @param String $tag
     */
    public function openTag($tag) {

        $this->opendTags[] = array($tag, $this->line);
    }

    /**
     * gibt das zuletzt geoeffnete Tag zurueck
     * 
     * @return String
     */
    public function getLastOpendTag() {

        list($openTag) = end($this->opendTags);
        return $openTag;
    }

    /**
     * schließt ein geoeffnetes Tag
     * 
     * @param String $tag
     */
    public function closeTag($tag) {

        list($opendTag, $line) = array_pop($this->opendTags);

        if ($tag == $opendTag) {

            return $opendTag;
        }

        if ($tag == 'if' && ($opendTag == 'else' || $opendTag == 'elseif')) {

            return $this->closeTag($tag);
        }

        if ($tag == 'foreach' && $opendTag == 'foreachelse') {

            return $this->closeTag($tag);
        }

        if ($tag == 'section' && $opendTag == 'sectionelse') {

            return $this->closeTag($tag);
        }
        return '';
    }

    /**
     * laedt die Plugins
     * 
     * @throws \RWF\Template\Exception\TemplateCompilerException
     */
    protected function loadPlugins() {

        //Template Ordner durchlaufen
        foreach($this->pluginDirs as $baseNamespace => $pluginDir) {

            $dir = opendir($pluginDir);
            while ($file = readdir($dir)) {

                if ($file == '.' || $file == '..') {

                    continue;
                }

                preg_match('#^([\w\d_]+)\.class\.php$#', $file, $match);
                $class = '\\'. $baseNamespace .'\\Template\\Plugin\\' . String::firstCharToUpper($match[1]);
                $object = new $class();

                if ($object instanceof TemplatePrefilter) {

                    $this->filter['prefilter'][] = $object;
                } elseif ($object instanceof TemplatePostfilter) {

                    $this->filter['postfilter'][] = $object;
                } elseif ($object instanceof TemplateBlockPlugin) {

                    $this->plugins['block'][] = $object;
                } elseif ($object instanceof TemplateCompilerPlugin) {

                    $this->plugins['compiler'][] = $object;
                } elseif ($object instanceof TemplateCompilerBlockPlugin) {

                    $this->plugins['compilerBlock'][] = $object;
                } elseif ($object instanceof TemplateFunction) {

                    $this->plugins['functions'][] = $object;
                } else {

                    throw new TemplateCompilerException('Die Klasse "' . $class . '" implementiert nicht das richtige Interface', 1145);
                }
            }
            closedir($dir);
        }

        //Prefilter Sortieren
        if (isset($this->filter['prefilter'])) {

            $sortFilters = array();
            foreach ($this->filter['prefilter'] as $filter) {

                $priority = $filter->getPriority();
                $sortFilters[$priority][] = $filter;
            }
            $this->filter['prefilter'] = $sortFilters;
        }

        //Postfilter Sortieren
        if (isset($this->filter['postfilter'])) {

            $sortFilters = array();
            foreach ($this->filter['postfilter'] as $filter) {

                $priority = $filter->getPriority();
                $sortFilters[$priority][] = $filter;
            }
            $this->filter['postfilter'] = $sortFilters;
        }
    }

    /**
     * initialisiert alle regulaeren Ausdruecke
     */
    protected function initPattern() {

        //Operatoren
        $this->variableOperatorPattern = '\-\>|\.|\(|\)|\[|\]|\||\:|\+|\-|\*|\/|\%|\^|\,';
        $this->conditionOperatorPattern = '===|!==|==|!=|<=|<|>=|(?<!-)>|\|\||&&|=';

        //Escape
        $this->escapedPattern = '(?<!\\\\)';

        //Variablen / Funktionsnamen
        $this->validVarnamePattern = '(?:[a-zA-Z_][a-zA-Z_0-9]*)';

        //Anfuehrungszeichen
        $this->doubleQuotePattern = '"(?:[^"\\\\]+|\\\\.)*"';
        $this->singleQuotePattern = '\'(?:[^\'\\\\]+|\\\\.)*\'';
        $this->quotePattern = '(?:' . $this->doubleQuotePattern . '|' . $this->singleQuotePattern . ')';

        //Zahlen
        $this->numericPattern = '(?i)(?:(?:\-?\d+(?:\.\d+)?)|true|false|null)';
    }

    /**
     * compiliert ein Template
     * 
     * @param String $tpl      Template Name 
     * @param String $srcFile  Quelldatei
     * @param String $destFile Zieldatei
     * @throws \RWF\Template\Exception\TemplateCompilerException
     */
    public function compile($tpl, $srcFile, $destFile) {

        //Allgemeine Daten
        $this->templateName = $tpl;
        $this->srcFile = $srcFile;
        $this->destFile = $destFile;

        //Initialisieren
        $this->line = 1;
        $this->opendTags = array();

        //Datei existiert nicht
        if (!file_exists($this->srcFile)) {

            throw new TemplateCompilerException('Die Template Datei "' . $this->templateName . '" existiert nicht oder konnte nicht gefunden werden', 1146);
        }

        //Quelldatei Laden
        $content = file_get_contents($this->srcFile);

        //Prefilter
        $content = $this->executePrefilters($content);
        //PHP Code loeschen
        $content = $this->deletePhpCode($content);
        //Kommentare loeschen
        $content = $this->deleteComments($content);
        //literale entfernen
        $content = $this->seperateLiterals($content);
        //Tags extrahieren
        $matches = array();
        preg_match_all('#' . $this->ld . '(.*?)' . $this->rd . '#', $content, $matches);
        $tags = $matches[1];
        //Text
        $text = preg_split('#' . $this->ld . '.*?' . $this->rd . '#', $content);
        //Tags Compilieren
        $compiledTags = array();
        $count = count($tags);
        for ($i = 0; $i < $count; $i++) {

            $this->line += String::countSubString($text[$i], "\n");
            $compiledTags[$i] = $this->compileTag($tags[$i]);
            $this->line += String::countSubString($tags[$i], "\n");
        }
        //Exception wenn nicht alle Tags geschlossen
        if (count($this->opendTags) > 0) {

            throw new TemplateCompilationException('nicht geschlossenes Tag {' . $this->opendTags[0][0] . '}', $this->templateName, $this->opendTags[0][1]);
        }
        //Compiliertes Template zusammensetzen
        $count = count($compiledTags);
        $compiled = '';
        for ($i = 0; $i < $count; $i++) {

            $compiled .= $text[$i] . $compiledTags[$i];
        }
        $compiled .= $text[$i];
        $compiled = String::trim($compiled);
        //literale einfuegen
        $compiled = $this->insertLiterals($compiled);
        //Postfilter
        $compiled = $this->executePostfilters($compiled);
        //Compiler Kommentare einfuegen
        $compiled = "<?php\n\t/*\n\t * Rapberry Pi Web Framework Template\n\t * Datei: " . $this->templateName . "\n\t * Compiliert am: " . gmdate('r')
                . "\n\t * Compiler: " . get_class($this) . "\n\t */\n?>\n" . $compiled;
        //Compilierte Datei schreiben
        if (!file_exists($this->destFile)) {

            FileUtil::createFile($this->destFile, 0777);
        }

        file_put_contents($this->destFile, $compiled);
    }

    /**
     * fuhrt die Prefilter aus
     * 
     * @param  String $content Content
     * @return String          Content
     */
    protected function executePrefilters($content) {

        if (isset($this->filter['prefilter'])) {

            //Ausfuehren
            foreach ($this->filter['prefilter'] as $priorityFilters) {

                foreach ($priorityFilters as $filter) {

                    $content = $filter->execute($this, $content);
                }
            }
        }
        return $content;
    }

    /**
     * loescht PHP Quelldoe im Template
     * 
     * @param  String $content Content
     * @return String          Content
     */
    protected function deletePhpCode($content) {

        return preg_replace('#(<\?php|<%).*?(\?>|%>)#is', '', $content);
    }

    /**
     * loescht Kommentare
     * 
     * @param  String $content Content
     * @return String          Content
     */
    protected function deleteComments($content) {

        return preg_replace('#' . $this->ld . '\*.*?\*' . $this->rd . '#is', '', $content);
    }

    /**
     * entfernt Literale
     * 
     * @param  String $content Content
     * @return String
     */
    protected function seperateLiterals($content) {

        $literals = array();
        preg_match_all('#(' . $this->ld . 'literal' . $this->rd . '(.*?)' . $this->ld . '/literal' . $this->rd . ')#is', $content, $literals, PREG_SET_ORDER);

        $id = 0;
        foreach ($literals as $literal) {

            $content = str_replace($literal[1], '<RWF-Literals-' . $this->hash . '-' . $id . '>', $content);
            $this->literals[$id] = $literal[2];
            $id++;
        }

        return $content;
    }

    /**
     * fuegt Literale wieder ein
     * 
     * @param  String $content Content
     * @return String
     */
    protected function insertLiterals($content) {

        foreach ($this->literals as $index => $literal) {

            $content = str_replace('<RWF-Literals-' . $this->hash . '-' . $index . '>', $literal, $content);
        }

        return $content;
    }

    /**
     * fuhrt die Postfilter aus
     * 
     * @param  String $content Content
     * @return String          Content
     */
    protected function executePostfilters($content) {

        if (isset($this->filter['postfilter'])) {

            //Ausfuehren
            foreach ($this->filter['postfilter'] as $priorityFilters) {

                foreach ($priorityFilters as $filter) {

                    $content = $filter->execute($this, $content);
                }
            }
        }
        return $content;
    }

    /**
     * fuehrt die Compiler Plugins aus
     * 
     * @param  String $command Befehl
     * @param  Array  $args    Argumente
     * @return String
     */
    protected function executeCompilerPlugins($command, array $args) {

        if (isset($this->plugins['compiler'])) {

            //Plugin suchen
            foreach($this->pluginDirs as $baseNamespace => $pluginDir) {

                $class = '\\'. $baseNamespace .'\\Template\\Plugin\\' . String::firstCharToUpper(String::toLower($command)) . 'CompilerPlugin';
                foreach($this->plugins['compiler'] as $plugin) {

                    if($plugin instanceof $class) {

                        return $plugin->execute($args, $this);
                    }
                }
            }
        }

        return null;
    }

    /**
     * fuehrt die Compiler Block Plugins aus
     * 
     * @param  String $command Befehl
     * @param  Array  $args    Argumente
     * @return String
     */
    protected function executeCompilerBlockPlugins($command, array $args) {

        if (isset($this->plugins['compilerBlock'])) {

            $tagStart = true;
            if (String::subString($command, 0, 1) == '/') {

                $tagStart = false;
                $command = String::subString($command, 1);
            }

            //Plugin suchen
            foreach($this->pluginDirs as $baseNamespace => $pluginDir) {

                $class = '\\'. $baseNamespace .'\\Template\\Plugin\\' . String::firstCharToUpper(String::toLower($command)) . 'CompilerBlockPlugin';
                foreach($this->plugins['compilerBlock'] as $plugin) {

                    if($plugin instanceof $class) {

                        //Abbruch wenn kein Pluginobjekt vorhanen
                        if ($plugin === null) {

                            return null;
                        }

                        if ($tagStart) {

                            $this->openTag($command);
                            return $plugin->executeStart($args, $this);
                        }

                        $this->closeTag($command);
                        return $plugin->executeEnd($this);
                    }
                }
            }
        }

        return null;
    }

    /**
     * fuehrt die Block Plugins aus
     * 
     * @param  String $command Befehl
     * @param  Array  $args    Argumente
     * @return String
     */
    protected function compileBlockPlugins($command, array $args) {

        $tagStart = true;
        if (String::subString($command, 0, 1) == '/') {

            $tagStart = false;
            $command = String::subString($command, 1);
        }

        //Plugin suchen
        foreach($this->pluginDirs as $baseNamespace => $pluginDir) {

            $class = '\\'. $baseNamespace .'\\Template\\Plugin\\' . String::firstCharToUpper(String::toLower($command)) . 'CompilerBlockPlugin';
            foreach($this->plugins['compilerBlock'] as $plugin) {

                if ($plugin instanceof $class) {

                    $code = '<?php ';

                    //Argumente Serialisieren
                    $argsstr = '';
                    foreach ($args as $key => $value) {

                        if ($argsstr != '') {

                            $argsstr .= ', ';
                        }

                        $argsstr .= '\'' . $key . '\' => ' . $value;
                    }

                    if ($tagStart === true) {

                        $this->openTag($command);

                        $code .= '$this->blocks[] = array(\'' . $command . '\', array(' . $argsstr . ')); ';
                        $code .= $class . '::init($this->blocks[count($this->blocks) -1][1], $this); ';
                        $code .= 'ob_start(); ';
                    } else {

                        $this->closeTag($command);

                        $code .= '$content = ob_get_contents(); ';
                        $code .= 'ob_end_clean(); ';
                        $code .= 'echo ' . $class . '::execute($this->blocks[count($this->blocks) -1][1], $content, $this); ';
                        $code .= 'unset($content); ';
                    }

                    $code .= ' ?>';
                    return $code;
                }
            }
        }
        return null;
    }

    /**
     * Compiliert ein Tag
     * 
     * @param  String $tag Tag
     * @return String      Compiliertes Tag
     */
    protected function compileTag($tag) {

        $tag = String::trim($tag);

        //Output Tag
        $output = $this->compileOutputTag($tag);
        if ($output !== null) {

            return $output;
        }

        //elseif
        $tag = preg_replace('#else\s+if#', '', $tag);

        //Command und Argumente trennen
        $match = array();
        preg_match('#^(/?[\w\d_]+)#', $tag, $match);
        $command = String::trim($match[1]);
        $argString = String::trim(String::subString($tag, String::length($command)));
        $args = $this->parseArgs($argString);

        switch ($command) {

            case 'include':

                return $this->compileInclude($args);

                break;
            case 'includeonce':

                return $this->compileInclude($args, true);

                break;
            case 'if':

                $this->openTag('if');
                return $this->compileIfTag($argString);

                break;
            case 'elseif':

                $last = $this->getLastOpendTag();
                if ($last != 'if' && $last != 'elseif') {

                    throw new TemplateCompilationException('nicht erwartetes {elseif} Tag', $this->templateName, $this->line);
                } else {

                    if($last == 'if') {

                        $this->closeTag('if');
                    } else {

                        $this->closeTag('elseif');
                    }
                    $this->openTag('elseif');
                }
                return $this->compileIfTag($argString, true);

                break;
            case 'else':

                $last = $this->getLastOpendTag();
                if ($last != 'if' && $last != 'elseif') {

                    throw new TemplateCompilationException('nicht erwartetes {else} Tag', $this->templateName, $this->line);
                } else {

                    if($last == 'if') {

                        $this->closeTag('if');
                    } else {

                        $this->closeTag('elseif');
                    }
                    $this->openTag('else');
                }

                return '<?php } else { ?>';

                break;
            case '/if':

                $last = $this->getLastOpendTag();
                if ($last != 'if' && $last != 'elseif' && $last != 'else') {

                    throw new TemplateCompilationException('nicht erwartetes {/if} Tag', $this->templateName, $this->line);
                } else {

                    if($last == 'if') {

                        $this->closeTag('if');
                    } elseif($last == 'else') {

                        $this->closeTag('else');
                    } else {

                        $this->closeTag('elseif');
                    }
                }

                return '<?php } ?>';

                break;
            case 'foreach':

                $this->openTag('foreach');
                return $this->compileForeachTag($args);

                break;
            case 'foreachelse':

                $last = $this->getLastOpendTag();
                if ($last != 'foreach') {

                    throw new TemplateCompilationException('nicht erwartetes {foreachelse} Tag', $this->templateName, $this->line);
                } else {

                    $this->closeTag('foreach');
                    $this->openTag('foreachelse');
                }

                return '<?php } } else { { ?>';

                break;
            case '/foreach':

                $last = $this->getLastOpendTag();
                if ($last != 'foreach' && $last != 'foreachelse') {

                    throw new TemplateCompilationException('nicht erwartetes {/foreach} Tag', $this->templateName, $this->line);
                } else {

                    if($last == 'foreach') {

                        $this->closeTag('foreach');
                    } else {

                        $this->closeTag('foreachelse');
                    }
                }
                ;
                return '<?php } } ?>';

                break;
            case 'section':

                $this->openTag('section');
                return $this->compileSectionTag($args);

                break;
            case 'sectionelse':

                $this->closeTag('sectionelse');
                return '<?php } } else { { ?>';

                break;
            case '/section':

                $this->closeTag('section');
                return '<?php } } ?>';

                break;
            case 'capture':

                $this->openTag('capture');
                return $this->compileCaptureTag($args, true);

                break;
            case '/capture':

                $this->closeTag('capture');
                return $this->compileCaptureTag($args, false);

                break;
            case 'rdelim':

                return $this->rightDelimiter;

                break;
            case 'ldelim':

                return $this->leftDelimiter;

                break;
            default:

                //Compiler Plugins
                $result = $this->executeCompilerPlugins($command, $args);

                if ($result !== null) {

                    return $result;
                }

                //Compiler Block Plugins
                $result = $this->executeCompilerBlockPlugins($command, $args);

                if ($result !== null) {

                    return $result;
                }

                //Block Plugins
                $result = $this->compileBlockPlugins($command, $args);

                if ($result !== null) {

                    return $result;
                }
        }

        throw new TemplateCompilationException('unbekanntes {' . $tag . '} Tag', $this->templateName, $this->line);
    }

    /**
     * compiliert ein Ausgabe Tag
     * 
     * @param  String $tag Tag
     * @return String      Compiliertes Tag
     */
    protected function compileOutputTag($tag) {

        if (!preg_match('#^(\#|%|@|\$|' . $this->quotePattern . ')#', $tag)) {

            return null;
        }

        $encodeHTML = false;
        $stripHTML = false;
        $numFormat = false;

        //HTML Encode
        if (preg_match('#^(\#)#', $tag)) {

            $encodeHTML = true;
        }

        //Strip HTML
        if (preg_match('#^(%)#', $tag)) {

            $stripHTML = true;
        }

        //Strip HTML
        if (preg_match('#^(@)#', $tag)) {

            $numFormat = true;
        }

        $var = preg_replace('#^(%|\#|@)#s', '', $tag);
        $var = $this->compileVar($var);

        $compiledTag = $var;

        //HTML Encode
        if ($encodeHTML === true) {

            $compiledTag = 'RWF\Util\String::encodeHTML(' . $compiledTag . ')';
        }

        //Strip HTML
        if ($stripHTML === true) {

            $compiledTag = 'RWF\Util\String::stripHTML(' . $compiledTag . ')';
        }

        //Number Format
        if ($numFormat === true) {

            $compiledTag = 'RWF\Util\String::numberFormat(' . $compiledTag . ')';
        }

        return '<?php echo ' . $compiledTag . '; ?>';
    }

    /**
     * compiliert eine Variable
     * 
     * @param  String $str Variable
     * @return String
     */
    public function compileVar($str) {

        $code = '';
        $type = $this->getType($str);

        //String
        if ($type == self::STRING) {

            return $str;
        }

        //Zahl
        if ($type == self::NUMERIC) {

            return $str;
        }

        //Variable
        //Quotes ersetzen
        $replaced = preg_replace_callback('#' . $this->quotePattern . '#i', array($this, 'replaceQuotes'), $str);

        //Operatoren
        $matches = array();
        preg_match_all('#(' . $this->variableOperatorPattern . ')#', $replaced, $matches);
        $operators = $matches[1];

        //Variablen
        $vars = preg_split('#(?:' . $this->variableOperatorPattern . ')#', $replaced);

        //tags Compilieren
        $statusStack = array(0 => 'start');
        $modifierData = null;

        for ($i = 0, $j = count($vars); $i < $j; $i++) {

            //Statusvariablen
            $status = end($statusStack);
            $operator = (isset($operators[$i]) ? $operators[$i] : null);
            $vars[$i] = trim($vars[$i]);
            $currentVar = $vars[$i];
            $type = $this->getVariableType($currentVar);

            //Variablen
            if ($currentVar != '') {

                switch ($status) {

                    case 'start':

                        $code .= $this->compileSimpleVariable($currentVar, $type);
                        $statusStack[0] = $status = $type;

                        break;
                    case 'dotAccess':

                        $code .= $this->compileSimpleVariable($currentVar, $type);
                        $code .= ']';
                        $statusStack[count($statusStack) - 1] = $status = 'variable';

                        break;
                    case 'objectAccess':

                        if (strpos($currentVar, '$') !== false) {

                            $code .= '{' . $this->compileSimpleVariable($currentVar, $type) . '}';
                        } else {

                            $code .= $currentVar;
                        }
                        $statusStack[count($statusStack) - 1] = $status = 'object';

                        break;
                    case 'objectMethodStart':

                        $statusStack[count($statusStack) - 1] = 'objectMethod';
                        $code .= $this->compileSimpleVariable($currentVar, $type);
                        $statusStack[count($statusStack) - 1] = $status = $type;

                        break;
                    case 'objectMethodParameterSeperator':

                        array_pop($statusStack);
                        $code .= $this->compileSimpleVariable($currentVar, $type);
                        $statusStack[] = $status = $type;

                        break;
                    case 'objectMethod':
                    case 'bracketOpen':
                    case 'leftParenthesis':

                        $code .= $this->compileSimpleVariable($currentVar, $type);
                        $statusStack[] = $status = $type;

                        break;
                    case 'math':

                        $code .= $this->compileSimpleVariable($currentVar, $type);
                        $statusStack[count($statusStack) - 1] = $status = $type;

                        break;
                    case 'modifierEnd':

                        $code .= $this->compileSimpleVariable($currentVar, $type);
                        $statusStack[] = $status = $type;

                        break;
                    case 'modifier':

                        if (strpos($currentVar, '$') !== false) {

                            throw new TemplateCompilationException('unknown modifier "' . $currentVar . '"', $this->templateName, $this->line);
                        }

                        if (substr($currentVar, 0, 1) == '!') {

                            $currentVar = substr($currentVar, 1);
                            $modifierData['negation'] = true;
                        }

                        $modifierData['name'] = String::firstCharToUpper(String::toLower($currentVar));

                        $found = false;
                        foreach($this->pluginDirs as $baseNamespace => $pluginDir) {

                            //Klasse Suchen
                            foreach($this->plugins['functions'] as $plugin) {

                                $class = '\\'. $baseNamespace .'\\Template\\Plugin\\' . $modifierData['name'] . 'Function';
                                if($plugin instanceof $class) {

                                    $modifierData['className'] = '\\'. $baseNamespace .'\\Template\\Plugin\\' . $modifierData['name'] . 'Function';
                                    $modifierData['type'] = 'class';
                                    $found = true;
                                }
                            }
                        }

                        if($found === false) {

                            $modifierData['name'] = String::toLower($modifierData['name']);
                            if ((function_exists($modifierData['name']) || in_array($modifierData['name'], $this->availableFunctions)) && !in_array($modifierData['name'], $this->disabledeFunctions)) {

                                $modifierData['className'] = $modifierData['name'];
                                $modifierData['type'] = 'function';
                            } else {

                                throw new TemplateCompilationException('unbekannte Funktion "' . $modifierData['name'] . '"', $this->templateName, $this->line);
                            }
                        }

                        $statusStack[count($statusStack) - 1] = $status = 'modifierEnd';

                        break;
                    case 'object':
                    case 'variable':
                    case 'string':

                        throw new TemplateCompilationException('unbekanntes {' . $str . '} Tag', $this->templateName, $this->line);

                        break;
                }
            }

            //Operatoren
            if ($operator !== null) {

                switch ($operator) {

                    case '.':

                        if ($status == 'variable' || $status == 'object') {

                            if ($status == 'object') {

                                $statusStack[count($statusStack) - 1] = 'variable';
                            }

                            $code .= '[';
                            $statusStack[] = 'dotAccess';
                            break;
                        }

                        throw new TemplateCompilationException('nicht erwartetes "." im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                    case '->':

                        if ($status == 'variable' || $status == 'object') {

                            $code .= $operator;
                            $statusStack[count($statusStack) - 1] = 'objectAccess';
                            break;
                        }

                        throw new TemplateCompilationException('nicht erwartetes "->" im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                    case '(':

                        if ($status == 'object') {

                            $statusStack[count($statusStack) - 1] = 'variable';
                            $statusStack[] = 'objectMethodStart';
                            $code .= $operator;
                            break;
                        } elseif ($status == 'math' || $status == 'start' || $status == 'leftParenthesis' || $status == 'bracketOpen' || $status == 'modifierEnd') {

                            $statusStack[] = 'leftParenthesis';
                            $code .= $operator;
                            break;
                        }

                        throw new TemplateCompilationException('nicht erwartetes "(" im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                    case ')':

                        while ($oldStatus = array_pop($statusStack)) {

//                            if ($oldStatus != 'variable' && $oldStatus != 'object') {

                                if ($oldStatus != 'variable' || $oldStatus != 'object' || $oldStatus == 'objectMethodStart' || $oldStatus == 'objectMethod' || $oldStatus == 'leftParenthesis' || $oldStatus == 'string') {

                                    $code .= $operator;
                                    break 2;
                                } else {

                                    break;
                                }
//                            }
                        }

                        throw new TemplateCompilationException('nicht erwartetes ")" im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                    case '[':

                        if ($status == 'variable' || $status == 'object') {

                            if ($status == 'object') {

                                $statusStack[count($statusStack) - 1] = 'variable';
                            }

                            $code .= $operator;
                            $statusStack[] = 'bracketOpen';
                            break;
                        }

                        throw new TemplateCompilationException('nicht erwartetes "[" im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                    case ']':

                        while ($oldStatus = array_pop($statusStack)) {

                            if ($oldStatus != 'variable' && $oldStatus != 'object' && $oldStatus != 'string') {

                                if ($oldStatus == 'bracketOpen') {

                                    $code .= $operator;
                                    break 2;
                                } else {

                                    break;
                                }
                            }
                        }

                        throw new TemplateCompilationException('nicht erwartetes "]" im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                    case '|':

                        if ($modifierData !== null) {

                            if ($code != '') {

                                $modifierData['parameter'][] = $code;
                            }

                            $code = $this->compileTemplateFunction($modifierData);
                        }

                        while ($oldStatus = array_pop($statusStack)) {

                            if ($oldStatus != 'variable' && $oldStatus != 'object' && $oldStatus != 'string' && $oldStatus != 'modifierEnd') {

                                throw new TemplateCompilationException('nicht erwartetes "|" im Tag "' . $str . '"', $this->templateName, $this->line);
                            }
                        }

                        $statusStack = array(0 => 'modifier');
                        $modifierData = array('name' => '', 'parameter' => array(0 => $code));
                        $code = '';

                        break;
                    case ':':

                        while ($oldStatus = array_pop($statusStack)) {

                            if ($oldStatus != 'variable' && $oldStatus != 'object' && $oldStatus != 'string') {

                                if ($oldStatus == 'modifierEnd') {

                                    $statusStack[] = 'modifierEnd';
                                    if ($code !== '') {

                                        $modifierData['parameter'][] = $code;
                                    }
                                    $code = '';
                                    break 2;
                                } else {

                                    break;
                                }
                            }
                        }

                        throw new TemplateCompilationException('nicht erwartetes ":" im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                    case ',':

                        while ($oldStatus = array_pop($statusStack)) {

                            if ($oldStatus != 'variable' && $oldStatus != 'object') {

                                if ($oldStatus == 'objectMethod' || $oldStatus == 'string') {

                                    $code .= $operator;
                                    $statusStack[] = 'objectMethod';
                                    $statusStack[] = 'objectMethodParameterSeparator';
                                    break 2;
                                } else {

                                    break;
                                }
                            }
                        }

                        throw new TemplateCompilationException('nicht erwartetes "," im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                    case '+':
                    case '-':
                    case '*':
                    case '/':
                    case '%':
                    case '^':

                        if ($status == 'variable' || $status == 'object' || $status == 'string' || $status == 'modifierEnd') {

                            $code .= $operator;
                            $statusStack[count($statusStack) - 1] = 'math';
                            break;
                        }

                        throw new TemplateCompilationException('nicht erwartetes "' . $operator . '" im Tag "' . $str . '"', $this->templateName, $this->line);

                        break;
                }
            }
        }

        //Funktionen behandeln
        if ($modifierData !== null) {

            if ($code !== '') {

                $modifierData['parameter'][] = $code;
            }
            $code = $this->compileTemplateFunction($modifierData);
        }

        //Quotes wieder einfuegen
        $code = $this->reinsertQuotes($code);

        return $code;
    }

    /**
     * callback Funktion zum ersetzen von Quotes
     * 
     * @param  String $str Fund
     * @return String
     */
    protected function replaceQuotes($str) {

        $hash = md5(mt_rand(0, 10000) * microtime(true));
        $this->quoteStack[$hash] = $str[0];
        return $hash;
    }

    /**
     * setzt die Quotes wieder ein
     * 
     * @param  String $str Code
     * @return String
     */
    protected function reinsertQuotes($str) {

        foreach ($this->quoteStack as $hash => $quote) {

            $str = str_replace($hash, $quote, $str);
        }

        return $str;
    }

    /**
     * compiliert ein Variablentag
     * 
     * @param  String $var  Variable
     * @param  String $type Typ
     * @return String
     */
    protected function compileSimpleVariable($var, $type) {

        switch ($type) {

            case 'variable':

                return '$this->var[\'' . ((substr($var, 0, 1) == '$') ? substr($var, 1) : $var) . '\']';

                break;
            case 'string':

                return $var;

                break;
        }

        return '\'' . $var . '\'';
    }

    /**
     * gibt den Variablentyp zurueck
     * 
     * @param  String $variable Variable
     * @return String
     */
    protected function getVariableType($variable) {

        if (substr($variable, 0, 1) == '$') {

            return 'variable';
        } elseif (String::checkMD5($variable)) {

            return 'string';
        } else {

            return 'unknown';
        }
    }

    /**
     * parst eine Template Funktion
     * 
     * @param  Array  $data Daten
     * @return String
     */
    protected function compileTemplateFunction($data) {

        if (isset($data['className'])) {

            if (isset($data['type']) && $data['type'] == 'class' || !isset($data['type'])) {

                return $data['className'] . '::execute(array(' . implode(',', $data['parameter']) . '), $this)';
            } else {

                if (isset($data['negation'])) {

                    return '!' . $data['className'] . '(' . implode(',', $data['parameter']) . ')';
                } else {

                    return $data['className'] . '(' . implode(',', $data['parameter']) . ')';
                }
            }
        } else {

            return $data['name'] . '(' . implode(',', $data['parameter']) . ')';
        }
    }

    /**
     * gibt den Typ einer Variable zurueck
     * 
     * @param  String  $var Variable
     * @return Integer
     */
    protected function getType($var) {

        if (preg_match('#^' . $this->quotePattern . '$#', $var)) {

            return self::STRING;
        } elseif (preg_match('#^' . $this->numericPattern . '$#', $var)) {

            return self::NUMERIC;
        }

        return self::VARIABLE;
    }

    /**
     * parst die Argumente 
     * 
     * @param  String $str Argumentestring
     * @return Array
     */
    protected function parseArgs($str) {

        if ($str == '') {

            return array();
        }

        $matches = array();
        preg_match_all('#([\w\d_]+)=(' . $this->quotePattern . '|(' . $this->validVarnamePattern . '|' . $this->numericPattern . '|\$[^\s]*))#', $str, $matches, PREG_SET_ORDER);

        $result = array();
        foreach ($matches as $match) {

            $result[$match[1]] = $this->compileVar($match[2]);
        }

        return $result;
    }

    /**
     * compiliert das Include Tag
     * 
     * @param  Array   $args Argumente
     * @param  Boolean $once neu einmal einbinden     
     * @return String
     */
    protected function compileInclude(array $args, $once = false) {

        if (!isset($args['file'])) {

            throw new TemplateCompilationException('Fehlendes "file" Attribut im include Tag', $this->templateName, $this->line);
        }

        $sandbox = 'false';
        if (isset($args['sandbox']) && ($args['sandbox'] == 'true' || $args['sandbox'] == '1')) {

            $sandbox = 'true';
        }

        $code = '<?php ';
        if ($once == true) {

            $code .= 'if(!isset($this->var[\'tpl\'][\'includedTemplates\']) || !in_array(\'' . $this->templateName . '\', $this->var[\'tpl\'][\'includedTemplates\'])) { ';
        }

        $code .= '$this->includeTemplate(' . $args['file'] . ', ' . $sandbox . ');';

        if ($once == true) {

            $code .= '$this->var[\'tpl\'][\'includedTemplates\'][] = \'' . $this->templateName . '\';';
            $code .= ' } ';
        }
        $code .= '?>';
        return $code;
    }

    /**
     * compiliert das Foreach Tag
     * 
     * @param  Array  $args Argumente
     * @return String
     */
    protected function compileForeachTag(array $args) {

        if (!isset($args['from'])) {

            throw new TemplateCompilationException('Fehlendes "from" Attribut im foreach Tag', $this->templateName, $this->line);
        }

        if (!isset($args['item'])) {

            throw new TemplateCompilationException('Fehlendes "item" Attribut im foreach Tag', $this->templateName, $this->line);
        }

        $useItems = false;
        $items = '';
        if (isset($args['name'])) {

            $useItems = true;
            $items = '$this->var[\'tpl\'][\'foreach\'][' . $args['name'] . ']';
        }

        $code = '<?php ';
        if ($useItems === true) {

            $code .= $items . '[\'total\'] = count(' . $args['from'] . ');';
            $code .= $items . '[\'show\'] = (' . $items . '[\'total\'] > 0 ? true : false);';
            $code .= $items . '[\'iteration\'] = 0;';
        }

        $code .= 'if(count(' . $args['from'] . ') > 0) { ';
        if (isset($args['key'])) {

            $code .= 'foreach(' . $args['from'] . ' as ' . (String::subString($args['key'], 0, 1) != '$' ? '$this->var[' . $args['key'] . ']' : $args['key']) . ' => ' . (String::subString($args['item'], 0, 1) != '$' ? '$this->var[' . $args['item'] . ']' : $args['item']) . ') { ';
        } else {

            $code .= 'foreach(' . $args['from'] . ' as ' . (String::subString($args['item'], 0, 1) != '$' ? '$this->var[' . $args['item'] . ']' : $args['item']) . ') { ';
        }

        if ($useItems === true) {

            $code .= $items . '[\'first\'] = (' . $items . '[\'iteration\'] == 0 ? true : false);';
            $code .= $items . '[\'last\'] = ((' . $items . '[\'iteration\'] == ' . $items . '[\'total\'] -1) ? true : false);';
            $code .= $items . '[\'even\'] = (((' . $items . '[\'iteration\'] % 2) == 0) ? true : false);';
            $code .= $items . '[\'odd\'] = (((' . $items . '[\'iteration\'] % 2) == 1) ? true : false);';
            $code .= $items . '[\'iteration\']++;';
        }

        $code .= ' ?>';
        return $code;
    }

    /**
     * compiliert das Capture Tag
     * 
     * @param  Array   $args  Argumente
     * @param  Boolean $start Start Tag
     * @return String
     */
    protected function compileCaptureTag(array $args, $start = true) {

        if ($start === true) {

            if (!isset($args['name'])) {

                $args['name'] = "'default'";
            }

            $append = false;
            if (!isset($args['assign'])) {

                if (isset($args['append'])) {

                    $args['assign'] = $args['append'];
                    $append = true;
                } else {

                    $args['assign'] = '';
                }
            }

            $this->capture[] = array('name' => $args['name'], 'variable' => $args['assign'], 'append' => $append);
            return '<?php ob_start(); ?>';
        } else {

            $capture = array_pop($this->capture);
            $code = '<?php ';
            $code .= '$this->var[\'tpl\'][\'capture\'][' . $capture['name'] . '] = ob_get_contents(); ob_end_clean();';

            if (!empty($capture['variable'])) {

                $code .= '$this->' . ($capture['append'] == true ? 'append' : 'assign') . '(' . $capture['variable'] . ', $this->var[\'tpl\'][\'capture\'][' . $capture['name'] . ']);';
            }

            $code .= ' ?>';
            return $code;
        }
    }

    /**
     * compiliert das Section Tag
     * 
     * @param  Array $args Argumente
     * @return String
     */
    protected function compileSectionTag(array $args) {

        if (!isset($args['loop'])) {

            throw new TemplateCompilationException('Fehlendes "loop" Attribut im section Tag', $this->templateName, $this->line);
        }

        if (!isset($args['name'])) {

            throw new TemplateCompilationException('Fehlendes "name" Attribut im section Tag', $this->templateName, $this->line);
        }

        if (!isset($args['show'])) {

            $args['show'] = true;
        }

        $item = '$this->var[\'tpl\'][\'section\'][' . $args['name'] . ']';

        $code = '<?php ';

        $code .= 'if(count(' . $args['loop'] . ') > 0) { ';
        $code .= $item . ' = array();';
        $code .= $item . '[\'loop\'] = (is_array(' . $args['loop'] . ') ? count(' . $args['loop'] . ') :max(0, ' . $args['loop'] . '));';
        $code .= $item . '[\'show\'] = ' . $args['show'] . ';';

        if (!isset($args['step'])) {

            $code .= $item . '[\'step\'] = 1;';
        } else {

            $code .= $item . '[\'step\'] = ' . $args['step'] . ';';
        }

        if (!isset($args['max'])) {

            $code .= $item . '[\'max\'] = ' . $item . '[\'loop\'];';
        } else {

            $code .= $item . '[\'max\'] = ((' . $args['max'] . ' < 0) ? ' . $item . '[\'loop\'] : ' . $args['max'] . ');';
        }

        if (!isset($args['start'])) {

            $code .= $item . '[\'start\'] = ((' . $item . '[\'step\'] > 0) ? 0 : ' . $item . '[\'loop\'] - 1);';
        } else {

            $code .= $item . '[\'start\'] = ' . $args['start'] . ';';
            $code .= 'if(' . $item . '[\'start\'] < 0) { ';
            $code .= $item . '[\'start\'] = max(' . $item . '[\'step\'] > 0 ? 0 : -1, ' . $item . '[\'loop\'] + ' . $item . '[\'start\'] - 1);';
            $code .= ' } else { ';
            $code .= $item . '[\'start\'] = min(' . $item . '[\'start\'], ' . $item . '[\'step\'] > 0 ? ' . $item . '[\'loop\'] : ' . $item . '[\'loop\'] - 1);';
            $code .= ' } ';
        }

        if (!isset($args['step']) && !isset($args['max']) && !isset($args['start'])) {

            $code .= $item . '[\'total\'] = ' . $item . '[\'loop\'];';
        } else {

            $code .= $item . '[\'total\'] = min(ceil((' . $item . '[\'step\'] > 0 ? (' . $item . '[\'loop\'] - ' . $item . '[\'start\']) : (' . $item . '[\'start\'] + 1))) / abs(' . $item . '[\'step\']), ' . $item . '[\'max\']);';
        }

        $code .= 'if(' . $item . '[\'total\'] == 0) { ' . $item . '[\'show\'] = false; }';
        $code .= ' } else { ';
        $code .= $item . '[\'total\'] = 0;';
        $code .= $item . '[\'show\'] = false;';
        $code .= ' } ';

        $code .= 'if(' . $item . '[\'show\']) { ';
        $code .= 'for(' . $item . '[\'index\'] = ' . $item . '[\'start\'], ' . $item . '[\'rowNumber\'] = 1;';
        $code .= $item . '[\'rowNumber\'] <= ' . $item . '[\'total\'];';
        $code .= $item . '[\'index\'] += ' . $item . '[\'step\'], ' . $item . '[\'rowNumber\']++) { ';
        $code .= '$this->var[' . $args['name'] . '] = ' . $item . '[\'index\'];';
        $code .= $item . '[\'previousIndex\'] = ' . $item . '[\'index\'] - ' . $item . '[\'step\'];';
        $code .= $item . '[\'nextIndex\'] = ' . $item . '[\'index\'] + ' . $item . '[\'step\'];';
        $code .= $item . '[\'first\'] = (' . $item . '[\'index\'] == 1);';
        $code .= $item . '[\'last\'] = ' . $item . '[\'index\'] == ' . $item . '[\'total\'];';

        $code .= ' ?>';
        return $code;
    }

    /**
     * compiliert das if Tag
     * 
     * @param  String  $args   Bedingungen
     * @param  Boolean $elseif elseif
     * @return String
     */
    protected function compileIfTag($args, $elseif = false) {

        $code = '<?php ';

        if ($elseif === false) {

            $code .= 'if(';
        } else {

            $code .= ' } elseif(';
        }

        $matches = array();
        preg_match_all('#(' . $this->conditionOperatorPattern . ')#', $args, $matches);
        $operators = $matches[1];
        $conditions = preg_split('#(' . $this->conditionOperatorPattern . ')#', $args, -1, PREG_SPLIT_NO_EMPTY);

        $operators = ArrayUtil::trim($operators, false, false);
        $conditions = ArrayUtil::trim($conditions, false, false);

        $lastIsOperator = false;
        for ($i = 0, $j = count($conditions); $i < $j; $i++) {

            if (String::length($conditions[$i]) > 0) {

                $code .= $this->compileVar($conditions[$i]) . ' ';
                $lastIsOperator = false;

                if (isset($operators[$i])) {

                    $code .= $operators[$i] . ' ';
                    $lastIsOperator = true;
                }
            } else {

                throw new TemplateCompilationException('empty comparison operators in an if statement', $this->templateName, $this->line);
            }
        }

        if ($lastIsOperator === true) {

            throw new TemplateCompilationException('empty comparison operators in an if statement', $this->templateName, $this->line);
        }

        $code .= ') { ?>';

        return $code;
    }

}
