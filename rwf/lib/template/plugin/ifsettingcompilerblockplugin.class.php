<?php

namespace RWF\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerBlockPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Template\Exception\TemplateCompilationException;

/**
 * EInstellung abfragen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class IfSettingCompilerBlockPlugin implements TemplateCompilerBlockPlugin {
    
    /**
     * wird beim Start Tag aufgerufen
     * 
     * @param  Array                          $args     Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function executeStart(array $args, TemplateCompiler $compiler) {
        
        //Plichtangabe Pruefen
        if(!isset($args['name'])) {
            
            throw new TemplateCompilationException('missing "name" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        return '<?php if(\\RWF\\Core\\RWF::getSetting('. $args['name'] .') == true) { ?>';
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