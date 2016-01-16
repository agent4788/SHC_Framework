<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;
use SHC\Switchable\Switchables\EdimaxSocket;

/**
 * Auswahlfeld des Event Typs
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EdimaxTypeChooser extends Select {

    public function __construct($name, $selctedType) {

        //Allgemeine Daten
        $this->setName($name);

        //Auswahl
        RWF::getLanguage()->disableAutoHtmlEndocde();
        $values = array(
            EdimaxSocket::EDIMAX_SP_1101W => array(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.type1'), ($selctedType == EdimaxSocket::EDIMAX_SP_1101W ? 1 : 0)),
            EdimaxSocket::EDIMAX_SP_2101W => array(RWF::getLanguage()->get('acp.switchableManagement.form.edimaxSocket.type2'), ($selctedType == EdimaxSocket::EDIMAX_SP_2101W ? 1 : 0))
        );
        RWF::getLanguage()->enableAutoHtmlEndocde();
        $this->setValues($values);
    }
}