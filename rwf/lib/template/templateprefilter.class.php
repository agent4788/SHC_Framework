<?php

namespace RWF\Template;

/**
 * Template Vorfilter (wird vor dem Compilieren ausgefuehrt)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface TemplatePrefilter {

    /**
     * wird vor dem Compilieren ausgefuehrt
     * 
     * @param  \RWF\Template\TemplateCompiler $compiler Compilierobjekt
     * @param  String                         $content  Content
     * @return String
     */
    public function execute(TemplateCompiler $compiler, $content);

    /**
     * Prioritaet wann der Filter ausgefuehrt wird
     * 
     * @return Integer
     */
    public function getPriority();
}
