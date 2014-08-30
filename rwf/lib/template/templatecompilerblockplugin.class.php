<?php

namespace RWF\Template;

/**
 * wird beim Compilieren eines unbekannten Blocks ausgefuehrt
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface TemplateCompilerBlockPlugin {

    /**
     * wird beim Start Tag aufgerufen
     * 
     * @param  Array                          $args     Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function executeStart(array $args, TemplateCompiler $compiler);

    /**
     * wird beim End Tag aufgerufen
     * 
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function executeEnd(TemplateCompiler $compiler);
}
