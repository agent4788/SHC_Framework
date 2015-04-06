<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;

/**
 * Auswahlfeld des Protokolls
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ProtocolChooser extends Select {

    public function __construct($name, $protocol = null) {

        //Allgemeine Daten
        $this->setName($name);
        $this->setOptions(array('grouped' => true));

        //Gruppen anmelden
        $values = array(
            'rcswitch-pi' => array(
                'elro_rc' => array('Elro', ($protocol == 'elro_rc' ? 1 : 0)),
            ),
            'pilight' => array(
                'brennenstuhl' => array('Brennenstuhl Comfort (brennenstuhl)', ($protocol == 'brennenstuhl' ? 1 : 0)),
                'byebyestandbye' => array('Bye Bye Standbye Switches (byebyestandbye)', ($protocol == 'byebyestandbye' ? 1 : 0)),
                'clarus_switch' => array('Clarus Switches (clarus_switch)', ($protocol == 'clarus_switch' ? 1 : 0)),
                'cleverwatts ' => array('Cleverwatts Switches (cleverwatts )', ($protocol == 'cleverwatts ' ? 1 : 0)),
                'coco_switch' => array('CoCo Technologies Switches (coco_switch)', ($protocol == 'coco_switch' ? 1 : 0)),
                'cogex' => array('Cogex Switches (cogex)', ($protocol == 'cogex' ? 1 : 0)),
                'dio_switch' => array(' D-IO Switches (dio_switch)', ($protocol == 'dio_switch' ? 1 : 0)),
                'elro_ad' => array('Elro Home Easy Advanced Switches (elro_ad)', ($protocol == 'elro_ad' ? 1 : 0)),
                'elro_hc' => array('Elro Home Control Switches (elro_hc)', ($protocol == 'elro_hc' ? 1 : 0)),
                'elro_he' => array('Elro Home Easy Switches (elro_he)', ($protocol == 'elro_he' ? 1 : 0)),
                'home_easy_old' => array('Old Home Easy Switches (home_easy_old)', ($protocol == 'home_easy_old' ? 1 : 0)),
                'impuls' => array('Impuls Switches (impuls)', ($protocol == 'impuls' ? 1 : 0)),
                'intertechno_old' => array('Old Intertechno Switches (intertechno_old)', ($protocol == 'intertechno_old' ? 1 : 0)),
                'intertechno_switch' => array('Intertechno Switches (intertechno_switch)', ($protocol == 'intertechno_switch' ? 1 : 0)),
                'kaku_switch' => array('Elro (kaku_switch)', ($protocol == 'kaku_switch' ? 1 : 0)),
                'nexa_switch' => array('Old KlikAanKlikUit Switches (nexa_switch)', ($protocol == 'nexa_switch' ? 1 : 0)),
                'kaku_switch_old' => array('Nexa Switches (kaku_switch_old)', ($protocol == 'kaku_switch_old' ? 1 : 0)),
                'pollin' => array('Pollin Switches (pollin)', ($protocol == 'pollin' ? 1 : 0)),
                'quigg_switch' => array('Quigg Switches (quigg_switch)', ($protocol == 'quigg_switch' ? 1 : 0)),
                'raw' => array('Raw Codes (raw)', ($protocol == 'raw' ? 1 : 0)),
                'rev1_switch' => array('Rev Switches v1 (rev1_switch)', ($protocol == 'rev1_switch' ? 1 : 0)),
                'rev2_switch' => array('Rev Switches v2 (rev2_switch)', ($protocol == 'rev2_switch' ? 1 : 0)),
                'rev3_switch' => array('Rev Switches v3 (rev3_switch)', ($protocol == 'rev3_switch' ? 1 : 0)),
                'selectremote' => array('SelectRemote Switches (selectremote)', ($protocol == 'selectremote' ? 1 : 0)),
                'silvercrest' => array('Silvercrest Switches (silvercrest)', ($protocol == 'silvercrest' ? 1 : 0)),
                'unitech' => array('Silvercrest Switches (unitech)', ($protocol == 'unitech' ? 1 : 0)),
                'silvercrest' => array('Unitech Switches (silvercrest)', ($protocol == 'silvercrest' ? 1 : 0)),
                'silvercrest' => array('Silvercrest Switches (silvercrest)', ($protocol == 'silvercrest' ? 1 : 0)),
                'eHome' => array('eHome', ($protocol == 'eHome' ? 1 : 0))
            )

        );
        $this->setValues($values);
    }
}