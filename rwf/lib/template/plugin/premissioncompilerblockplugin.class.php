<?php

namespace RWF\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerBlockPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;

/**
 * Rechtabfrage
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class PremissionCompilerBlockPlugin implements TemplateCompilerBlockPlugin {
    
    /**
     * wird beim Start Tag aufgerufen
     * 
     * @param  Array                          $args     Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function executeStart(array $args, TemplateCompiler $compiler) {
        
        //Plichtangabe Pruefen
        if(!isset($args['premission'])) {
            
            throw new TemplateCompilationException('missing "premission" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }
        
        $setting = '';
        if(isset($args['setting'])) {
            
            $setting = '&& \\RWF\\Core\\RWF::getSetting('. $args['setting'] .') == true';
        }
        return '<?php if(\\RWF\Core\\RWF::getVisitor()->checkPermission('. $args['premission'] .') == true '. $setting .') { ?>';
    }

    /**
     * wird beim End Tag aufgerufen
     * 
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function executeEnd(TemplateCompiler $compiler) {
        
        return '<?php } ?>';
    }
}
