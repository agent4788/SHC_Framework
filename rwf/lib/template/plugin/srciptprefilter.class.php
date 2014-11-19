<?php

namespace RWF\Template\Plugin;

//Imports
use RWF\Template\TemplatePrefilter;
use RWF\Template\TemplateCompiler;

/**
 * JavaScript und CSS vor dem Compilieren umdwandeln
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SrciptPrefilter implements TemplatePrefilter {

    /**
     * wird vor dem Compilieren ausgefuehrt
     * 
     * @param  \RWF\Template\TemplateCompiler $compiler Compilierobjekt
     * @param  String                         $content  Content
     * @return String
     */
    public function execute(TemplateCompiler $compiler, $content) {

        $content = @preg_replace('#\{js\}(.*?)\{/js\}#ies', 'self::scriptJS', $content);
        $content = @preg_replace('#\{js\s+src=(?:"|\')([^\s]*)(?:"|\')\}#ies', 'self::scriptJSSrc', $content);
        $content = @preg_replace('#\{css\}(.*?)\{/css\}#ies', 'self::css', $content);
        $content = @preg_replace('#\{css\s+src=(?:"|\')([^\s]*)(?:"|\')\}#ies', 'self::cssSrc', $content);

        return $content;
    }

    /**
     * erstellt ein Script Tag
     * 
     * @param  String $content Script Code
     * @return String          Code
     */
    protected function scriptJS($matches) {

        return '<script type="text/javascript">{literal}' . $matches[1] . '\{/literal}</script>';
    }

    /**
     * erstellt ein Include Script Tag
     * 
     * @param  String $src Quelle
     * @return String      Code
     */
    protected function scriptJSSrc($matches) {

        return '<script type="text/javascript" src="' . $matches[1] . '"></script>';
    }

    /**
     * erstellt ein Style Tag
     * 
     * @param  String $content CSS Code
     * @return String          Code
     */
    protected function css($matches) {

        return '<style type="text/css">{literal}' . $matches[1] . '{/literal}</style>';
    }

    /**
     * erstellt ein Link Tag
     * 
     * @param  String $src Quelle
     * @return String      Code
     */
    protected function cssSrc($matches) {

        return '<link rel="stylesheet" type="text/css" href="' . $matches[1] . '" />';
    }

    /**
     * Prioritaet wann der Filter ausgefuehrt wird
     * 
     * @return Integer
     */
    public function getPriority() {

        return 10;
    }

}
