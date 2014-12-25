<?php

namespace PCC\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerBlockPlugin;
use RWF\Template\TemplateCompiler;

/**
 * Rechtabfrage
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ContentBoxCompilerBlockPlugin implements TemplateCompilerBlockPlugin {
    
    /**
     * wird beim Start Tag aufgerufen
     * 
     * @param  Array                          $args     Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function executeStart(array $args, TemplateCompiler $compiler) {
        
        //Name der Box (Optional)
        $name = '';
        if(isset($args['name'])) {
            
            $name = '<?php echo \\RWF\\Core\\RWF::getLanguage()->get('. $args['name'] .'); ?>';
        }
        
        //Id
        $id = '';
        if(isset($args['id'])) {
            
            $id = ' id='. $args['id'];
        }
        
        //Class
        $class = '';
        if(isset($args['class'])) {
            
            $class = $args['class'];
        }
        
        $html = '<div class="pcc-contentbox ui-tabs ui-widget ui-widget-content ui-corner-all '. $class .'"'. $id .'>';
        $html .= '<div class="pcc-contentbox-header ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">';
        $html .= $name;
        $html .= '</div>';
        $html .= '<div class="pcc-contentbox-body">';
        return $html;
    }

    /**
     * wird beim End Tag aufgerufen
     * 
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function executeEnd(TemplateCompiler $compiler) {
        
        return '</div></div>';
    }
}
