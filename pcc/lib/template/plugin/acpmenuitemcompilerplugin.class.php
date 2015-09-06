<?php

namespace PCC\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;
use RWF\Util\String;

/**
 * erstellt einen Menueeintrag im ACP Hauptmenue
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AcpMenuItemCompilerPlugin implements TemplateCompilerPlugin {

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
        if(!isset($args['text'])) {

            throw new TemplateCompilationException('missing "text" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        if(!isset($args['icon'])) {

            throw new TemplateCompilationException('missing "icon" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        if(!isset($args['link'])) {

            throw new TemplateCompilationException('missing "icon" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        $premission = '';
        if(isset($args['premission'])) {

            $premission = '\\RWF\Core\\RWF::getVisitor()->checkPermission('. $args['premission'] .')';
        }

        $setting = '';
        if(isset($args['setting'])) {

            $setting = '\\RWF\Core\\RWF::getSetting('. $args['setting'] .')';
        }

        $html = '';
        $randomStr = String::randomStr(64);
        //Bedinguneg
        if($premission != '' && $setting != '') {

            $html .= '<?php if('. $premission .' && '. $setting .') { ?>';
        } elseif($premission != '' || $setting != '') {

            $html .= '<?php if('. $premission .' '. $setting .') { ?>';
        }

        //HTML
        $html .= '<div class="pcc-view-acp-menuItem" id="a'. $randomStr .'">';
        $html .= '<span class="pcc-view-acp-menuItem-icon" id='. $args['icon'] .'></span>';
        $html .= '<span class="pcc-view-acp-menuItem-text"><?php echo \\RWF\\Core\\RWF::getLanguage()->get('. $args['text'] .'); ?></span>';
        $html .= '</div>';
        $html .= '<script type="text/javascript">';
        $html .= '$(function() {';
        $html .= '$(\'#a'. $randomStr .'\').click(function() {';
        $html .= 'window.location = '. $args['link'] .';';
        $html .= '});';
        $html .= '});';
        $html .= '</script>';;

        //Bedingung abschliesen
        if($premission != '' || $setting != '') {

            $html .= '<?php } ?>';
        }
        return $html;
    }
}