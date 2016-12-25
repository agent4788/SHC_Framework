<?php

namespace SHC\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;
use RWF\Util\StringUtils;

/**
 * Button im ACP Bereich
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AcpButtonCompilerPlugin implements TemplateCompilerPlugin {

    /**
     * wird beim Compilieren eines unbekannten Tags ausgefuehrt
     *
     * @param  Array                          $args     Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function execute(array $args, TemplateCompiler $compiler) {

        //Plichtangabe Pruefen
        if(!isset($args['link'])) {

            throw new TemplateCompilationException('missing "link" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        if(!isset($args['text'])) {

            throw new TemplateCompilationException('missing "text" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        $randomStr = StringUtils::randomStr(64);
        $html = '<button id="'. $randomStr .'" ><?php echo \\RWF\\Core\\RWF::getLanguage()->get('. $args['text'] .'); ?></button>';
        $html .= '<script type="text/javascript">';
        $html .= '$(function() {';
        //Inhalt per Ajax holen
        $html .= '  $(\'#'. $randomStr .'\').click(function() {';
        $html .= '      $.get('. $args['link'] .', function(data, textStatus, jqXHR) {';
        $html .= '          $(\'#shc-view-acp-contentBox div.shc-contentbox-inner\').html(data);';
        $html .= '      });';
        $html .= '  });';
        $html .= '});';
        $html .= '</script>';
        return $html;
    }
}
