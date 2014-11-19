<?php

namespace SHC\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;
use RWF\Util\String;

/**
 * zurueck Button im ACP Bereich
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AcpBackButtonCompilerPlugin implements TemplateCompilerPlugin {

    /**
     * wird beim Compilieren eines unbekannten Tags ausgefuehrt
     *
     * @param  Array                          $args     Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function execute(array $args, TemplateCompiler $compiler) {

        //Plichtangabe Pruefen
        if(!isset($args['location']) && !isset($args['link'])) {

            throw new TemplateCompilationException('missing "location" or "link" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        $randomStr = String::randomStr(64);
        $html = '<button id="'. $randomStr .'" ><?php echo \\RWF\\Core\\RWF::getLanguage()->get(\'global.button.back\'); ?></button>';
        $html .= '<script type="text/javascript">';
        $html .= '$(function() {';

        if(isset($args['location'])) {

            //Loaction
            $html .= '  $(\'#'. $randomStr .'\').click(function() {';
            $html .= '      window.location = '. $args['location'] .';';
            $html .= '  });';
        } elseif(isset($args['link'])) {

            //Inhalt per Ajax holen
            $html .= '  $(\'#'. $randomStr .'\').click(function() {';
            $html .= '      $.get('. $args['link'] .', function(data, textStatus, jqXHR) {';
            $html .= '          $(\'#shc-view-acp-contentBox div.shc-contentbox-body\').html(data);';
            $html .= '      });';
            $html .= '  });';
        }

        $html .= '});';
        $html .= '</script>';
        return $html;
    }
}
