<?php

namespace SHC\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;
use RWF\Util\String;

/**
 * erzeugt einen loeschen button
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteLinkCompilerPlugin implements TemplateCompilerPlugin {

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

        if(!isset($args['id'])) {

            throw new TemplateCompilationException('missing "id" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        $safetyPromptText = '';
        if(isset($args['prompt'])) {

            $safetyPromptText = '<?php echo \\RWF\\Core\\RWF::getLanguage()->get('. $args['prompt'] .'); ?>';
        }
        $title = '';
        if(isset($args['title'])) {

            $title = '<?php echo \\RWF\\Core\\RWF::getLanguage()->get('. $args['title'] .'); ?>';
        }

        $randomStr = String::randomStr(64);
        $randomStr2 = String::randomStr(64);
        $link = str_replace('\'', '', $args['link']) .'<?php echo '. $args['id'] .'; ?>';
        $html = '<a href="#"  id="'. $randomStr .'" class="shc-view-buttons-delete" title="<?php echo \\RWF\\Core\\RWF::getLanguage()->get(\'global.button.delete\'); ?>"></a>';
        if($safetyPromptText != '') {

            $html .= '<div id="'. $randomStr2 .'" '. ($title != '' ? 'title="'. $title .'" ' : '') .'style="display: none">'. $safetyPromptText .'</div>';
        }
        $html .= '<script type="text/javascript">';
        $html .= '$(function() {';
        $html .= '  $(\'.shc-view-buttons-delete\').tooltip({';
        $html .= '      track: false,';
        $html .= '      position: {';
        $html .= '          my: "left+15 center",';
        $html .= '          at: "right center"';
        $html .= '      }';
        $html .= '  });';
        $html .= '  $(\'#'. $randomStr .'\').click(function() {;';
        $html .= '      $(\'#'. $randomStr2 .'\').dialog({';
        $html .= '          modal: true,';
        $html .= '          resizable: false,';
        $html .= '          position: {my: "center top", at: "center bottom", of: $(\'#shc-headline\')},';
        $html .= '          buttons: {';
        $html .= '              \'<?php echo \\RWF\\Core\\RWF::getLanguage()->get(\'global.button.yes\'); ?>\': function() {';
        $html .= '                   $.get(\''. $link .'\', function(data, textStatus, jqXHR) {;';
        $html .= '                       $(\'#shc-view-acp-contentBox div.shc-contentbox-body\').html(data);';
        $html .= '                  });';
        $html .= '                  $(\'#'. $randomStr2 .'\').dialog(\'close\');';
        $html .= '               },';
        $html .= '            \'<?php echo \\RWF\\Core\\RWF::getLanguage()->get(\'global.button.no\'); ?>\': function() {';
        $html .= '                  $(\'#'. $randomStr2 .'\').dialog(\'close\');';
        $html .= '             }';
        $html .= '           }';
        $html .= '      });';
        $html .= '   });';
        $html .= '});';
        $html .= '</script>';
        return $html;
    }
}
