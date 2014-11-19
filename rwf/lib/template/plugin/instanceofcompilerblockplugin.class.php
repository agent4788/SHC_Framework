<?php

namespace RWF\Template\Plugin;

//Imports
use RWF\Template\TemplateCompilerBlockPlugin;
use RWF\Template\TemplateCompiler;
use RWF\Template\Exception\TemplateCompilationException;

/**
 * Instanceof Abfrage
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InstanceofCompilerBlockPlugin implements TemplateCompilerBlockPlugin {

    /**
     * wird beim Start Tag aufgerufen
     *
     * @param  Array                          $args     Argumente
     * @param  \RWF\Template\TemplateCompiler $compiler Compiler Objekt
     * @return String
     */
    public function executeStart(array $args, TemplateCompiler $compiler) {

        //Plichtangabe Pruefen
        if(!isset($args['element'])) {

            throw new TemplateCompilationException('missing "element" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }
        if(!isset($args['class'])) {

            throw new TemplateCompilationException('missing "class" attribute in premission tag', $compiler->getTemplateName(), $compiler->getCurrentLine());
        }

        return '<?php if('. $args['element'] .' instanceof '. str_replace(array('"', "'"), '', $args['class']) .') { ?>';
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