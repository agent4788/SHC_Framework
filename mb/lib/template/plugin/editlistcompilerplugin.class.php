<?php

namespace MB\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;

/**
 * erzeugt einen bearbeiten Button
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditListCompilerPlugin implements TemplateCompilerPlugin {

    /**
     * wird beim Compilieren eines unbekannten Tags ausgefuehrt
     *
     * @param  Array $args Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     * @throws TemplateCompilationException
     */
    public function execute(array $args, TemplateCompiler $compiler) {

        //Plichtangabe Pruefen
        if(!isset($args['link'])) {

            throw new TemplateCompilationException('missing "link" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        if(!isset($args['id'])) {

            throw new TemplateCompilationException('missing "id" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        $link = str_replace(array('"', "'"), '', $args['link']) .'<?php echo '. $args['id'] .'; ?>';
        $html  = '<?php $randomId = RWF\Util\String::randomStr(64); ?>';
        $html .= '<a href="#"  id="<?php echo $randomId; ?>" class="mb-view-buttons-editlist" title="<?php echo \\RWF\\Core\\RWF::getLanguage()->get(\'global.button.editlist\'); ?>"></a>';
        $html .= '<script type="text/javascript">';
        $html .= '$(function() {';
        $html .= '  $(\'.mb-view-buttons-edit\').tooltip({';
        $html .= '      track: false,';
        $html .= '      position: {';
        $html .= '          my: "left+15 center",';
        $html .= '          at: "right center"';
        $html .= '      }';
        $html .= '  });';
        $html .= '  $(\'#<?php echo $randomId; ?>\').click(function() {';
        $html .= '      window.location = \''. $link .'\';';
        $html .= '  });';
        $html .= '});';
        $html .= '</script>';
        return $html;
    }
}