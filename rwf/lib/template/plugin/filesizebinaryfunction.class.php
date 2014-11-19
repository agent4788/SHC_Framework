<?php

namespace RWF\Template\Plugin;

//Imports
use RWF\Template\TemplateFunction;
use RWF\Template\Template;
use RWF\Util\FileUtil;

/**
 * formatiert einen Bytewert in eine lesbare angabe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FilesizeBinaryFunction implements TemplateFunction {

    /**
     * Template Funktion
     *
     * @param  Array                  $value Werte
     * @param  \RWF\Template\Template $tpl   Template Objekt
     * @return String
     */
    public static function execute(array $value, Template $tpl) {

        return FileUtil::formatBytesBinary($value[0]);
    }

}