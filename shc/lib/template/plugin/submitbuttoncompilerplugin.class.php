<?php

namespace SHC\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;
use RWF\Util\String;

/**
 * Formular senden Button
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SubmitButtonCompilerPlugin implements TemplateCompilerPlugin {

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

        if(!isset($args['action'])) {

            throw new TemplateCompilationException('missing "action" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        $id = '';
        if(isset($args['id'])) {

            $id = '&id=<?php echo '. $args['id'] .'; ?>';
        }

        $randomStr = String::randomStr(64);
        $html = '<button id="'. $randomStr .'" ><?php echo \\RWF\\Core\\RWF::getLanguage()->get(\'form.button.submit\'); ?></button>';
        $html .= '<script type="text/javascript">';
        $html .= '$(function() {';
        $html .= '  $(\'#'. $randomStr .'\').click(function() {';
        $html .= '      var $form = $('. $args['form'] .');';
        $html .= '      var $inputs = $form.find("input, select, button, textarea");';
        $html .= '      var serializedData = $form.serialize();';
        $html .= '      $inputs.prop("disabled", true);';
        $html .= '      request = $.ajax({';
        $html .= '          url: "'. str_replace(array('"', "'"), '', $args['action']) .''. $id .'",';
        $html .= '          type: "post",';
        $html .= '          data: serializedData';
        $html .= '      });';
        $html .= '      request.done(function(response, textStatus, jqXHR) {';
        $html .= '          $(\'#shc-view-acp-contentBox div.shc-contentbox-inner\').html(response);';
        $html .= '      });';
        $html .= '      request.always(function() {';
        $html .= '          $inputs.prop("disabled", false);';
        $html .= '      });';
        $html .= '  });';
        $html .= '});';
        $html .= '</script>';
        return $html;
    }
}