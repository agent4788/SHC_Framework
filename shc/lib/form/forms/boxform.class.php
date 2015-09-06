<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\TextField;
use SHC\Form\FormElements\ElementsForBoxChooser;
use SHC\Form\FormElements\SingleRoomChooser;
use SHC\View\Room\ViewHelperBox;

/**
 * Benutzergruppen Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class BoxForm extends DefaultHtmlForm {

    /**
     * @param ViewHelperBox $box
     */
    public function __construct(ViewHelperBox $box = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Box Name
        $name = new TextField('name', ($box instanceof ViewHelperBox ? $box->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.box.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.box.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Raum Auswahl nur bei neu erstellter Box
        if(!$box instanceof ViewHelperBox) {

            //Raum
            $room = new SingleRoomChooser('room', ($box instanceof ViewHelperBox ? $box->getRoomId() : null));
            $room->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.box.room'));
            $room->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.box.room.description'));
            $room->requiredField(true);
            $this->addFormElement($room);
        }

        //Auswahl der Elemente nur im Edit Modus
        if($box instanceof ViewHelperBox) {

            //Auswahliste der verfügbaren Elemente
            $elementList = new ElementsForBoxChooser('elements', $box);
            $elementList->setTitle(RWF::getLanguage()->get('acp.switchableManagement.form.box.elements'));
            $elementList->setDescription(RWF::getLanguage()->get('acp.switchableManagement.form.box.elements.description'));
            $elementList->requiredField(true);
            $this->addFormElement($elementList);
        }

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}