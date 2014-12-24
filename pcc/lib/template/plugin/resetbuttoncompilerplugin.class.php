<?php

namespace PCC\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;
use RWF\Util\String;

/**
 * Formular zuruecksetzen Burron
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ResetButtonCompilerPlugin implements TemplateCompilerPlugin {

    /**
     * wird beim Compilieren eines unbekannten Tags ausgefuehrt
     *
     * @param  Array                          $args     Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function execute(array $args, TemplateCompiler $compiler) {

        //Plichtangabe Pruefen
        if(!isset($args['form'])) {

            throw new TemplateCompilationException('missing "form" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        $randomStr = String::randomStr(64);
        $html = '<button id="'. $randomStr .'" ><?php echo \\RWF\\Core\\RWF::getLanguage()->get(\'form.button.reset\'); ?></button>';
        $html .= '<script type="text/javascript">';
        $html .= '$(function() {';
        $html .= '  $(\'#'. $randomStr .'\').click(function() {';
        $html .= '      $('. $args['form'] .').each(function () {';
        $html .= '          this.reset();';
        $html .= '      });';
        $html .= '  });';
        $html .= '});';
        $html .= '</script>';
        return $html;
    }
}