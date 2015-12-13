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
                'beamish_switch' => array('Beamish 4-AE4 (beamish_switch)', ($protocol == 'beamish_switch' ? 1 : 0)),
                'byebyestandbye' => array('Bye Bye Standbye Switches (byebyestandbye)', ($protocol == 'byebyestandbye' ? 1 : 0)),
                'brennenstuhl' => array('Brennenstuhl Comfort (brennenstuhl)', ($protocol == 'brennenstuhl' ? 1 : 0)),
                'clarus_switch' => array('Clarus Switches (clarus_switch)', ($protocol == 'clarus_switch' ? 1 : 0)),
                'cleverwatts ' => array('Cleverwatts Switches (cleverwatts )', ($protocol == 'cleverwatts ' ? 1 : 0)),
                'coco_switch' => array('CoCo Technologies Switches (coco_switch)', ($protocol == 'coco_switch' ? 1 : 0)),
                'cogex' => array('Cogex Switches (cogex)', ($protocol == 'cogex' ? 1 : 0)),
                'dio_switch' => array(' D-IO Switches (dio_switch)', ($protocol == 'dio_switch' ? 1 : 0)),
                'elro_300' => array('Elro 300 Series (elro_300)', ($protocol == 'elro_300' ? 1 : 0)),
                'elro_300_switch' => array('Elro 300 Series (elro_300_switch) Pilight 7', ($protocol == 'elro_300_switch' ? 1 : 0)),
                'elro_400' => array('Elro 400 Series (elro_400)', ($protocol == 'elro_400' ? 1 : 0)),
                'elro_400_switch' => array('Elro 400 Series (elro_400) Pilight 7', ($protocol == 'elro_400_switch' ? 1 : 0)),
                'elro_800_switch' => array('Elro 800 Series (elro_800_switch)', ($protocol == 'elro_800_switch' ? 1 : 0)),
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
                'silvercrest' => array('Silvercrest Switches oder Unitech Switches  (silvercrest)', ($protocol == 'silvercrest' ? 1 : 0)),
                'eHome' => array('eHome (eHome)', ($protocol == 'eHome' ? 1 : 0)),
                'rsl366' => array('RSL366 (rsl366)', ($protocol == 'rsl366' ? 1 : 0)),
                'promax' => array('PROmax (promax)', ($protocol == 'promax' ? 1 : 0)),
                'rc101' => array('RC101 (rc101)', ($protocol == 'rc101' ? 1 : 0)),
                'rc102' => array('RC102 (rc102)', ($protocol == 'rc102' ? 1 : 0)),
                'duwi' => array('Düwi Terminal (duwi)', ($protocol == 'duwi' ? 1 : 0)),
                'logilink-switch' => array('Logilink EC0002, EC0004, EC0005 and EC0006 (logilink-switch)', ($protocol == 'logilink-switch' ? 1 : 0)),
                'techlico_switch' => array('TechLiCo (techlico_switch)', ($protocol == 'techlico_switch' ? 1 : 0))
            )

        );
        $this->setValues($values);
    }
}