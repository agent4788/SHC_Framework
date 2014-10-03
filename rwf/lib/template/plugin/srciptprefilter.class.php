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

        $content = @preg_replace('#\{js\}(.*?)\{/js\}#ies', '$this->scriptJS(\'$1\')', $content);
        $content = @preg_replace('#\{js\s+src=(?:"|\')([^\s]*)(?:"|\')\}#ies', '$this->scriptJSSrc(\'$1\')', $content);
        $content = @preg_replace('#\{css\}(.*?)\{/css\}#ies', '$this->css(\'$1\')', $content);
        $content = @preg_replace('#\{css\s+src=(?:"|\')([^\s]*)(?:"|\')\}#ies', '$this->cssSrc(\'$1\')', $content);

        return $content;
    }

    /**
     * erstellt ein Script Tag
     * 
     * @param  String $content Script Code
     * @return String          Code
     */
    protected function scriptJS($content) {

        return '<script type="text/javascript">{literal}' . $content . '{/literal}</script>';
    }

    /**
     * erstellt ein Include Script Tag
     * 
     * @param  String $src Quelle
     * @return String      Code
     */
    protected function scriptJSSrc($src) {

        return '<script type="text/javascript" src="' . $src . '"></script>';
    }

    /**
     * erstellt ein Style Tag
     * 
     * @param  String $content CSS Code
     * @return String          Code
     */
    protected function css($content) {

        return '<style type="text/css">{literal}' . $content . '{/literal}</style>';
    }

    /**
     * erstellt ein Link Tag
     * 
     * @param  String $src Quelle
     * @return String      Code
     */
    protected function cssSrc($src) {

        return '<link rel="stylesheet" type="text/css" href="' . $src . '" />';
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
