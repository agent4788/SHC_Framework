<?php

namespace SHC\Form\Forms\Events;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Core\Exception\AssertException;
use SHC\Core\SHC;
use SHC\Event\AbstractEvent;
use SHC\Event\Events\InputHigh;
use SHC\Event\Events\InputLow;
use SHC\Form\FormElements\InputChooser;

/**
 * Eingangs Ereignis
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InputEventForm extends DefaultHtmlForm {

    /**
     * @param AbstractEvent $condition
     */
    public function __construct(AbstractEvent $event = null) {

        //Pruefen ob zulaessiges Objekt uebergeben
        if($event !== null && !$event instanceof InputHigh && !$event instanceof InputLow) {

            throw new \Exception('ungültiges Ereignis übergeben', 1513);
        }

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Name der Aktivitaet
        $name = new TextField('name', ($event !== null ? $event->getName() : ''), array('minlength' => 3, 'maxlength' => 25));
        $name->setTitle(RWF::getLanguage()->get('acp.eventsManagement.form.event.name'));
        $name->setDescription(RWF::getLanguage()->get('acp.eventsManagement.form.event.name.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Eingaenge
        $sensors = new InputChooser('inputs', ($event !== null ? explode(',', $event->getData()['inputs']) : array()));
        $sensors->setTitle(RWF::getLanguage()->get('acp.eventsManagement.form.event.inputs'));
        $sensors->setDescription(RWF::getLanguage()->get('acp.eventsManagement.form.event.inputs.description'));
        $sensors->requiredField(true);
        $this->addFormElement($sensors);

        //Intervall
        switch(SHC::getSetting('shc.shedulerDaemon.performanceProfile')) {

            case 1:

                //fast
                $min = 5;
                break;
            case 2:

                //default
                $min = 10;
                break;
            case 3:

                //slow
                $min = 30;
                break;
            default:

                throw new AssertException("Die Einstellung 'shc.shedulerDaemon.performanceProfile' ist Fehlerhaft");
        }
        $name = new IntegerInputField('interval', ($event !== null ? $event->getData()['interval'] : 30), array('min' => $min, 'max' => 3600));
        $name->setTitle(RWF::getLanguage()->get('acp.eventsManagement.form.event.interval'));
        $name->setDescription(RWF::getLanguage()->get('acp.eventsManagement.form.event.interval.description'));
        $name->requiredField(true);
        $this->addFormElement($name);

        //Aktiv/Inaktiv
        $enabled = new OnOffOption('enabled', ($event !== null ? $event->isEnabled() : true));
        $enabled->setActiveInactiveLabel();
        $enabled->setTitle(RWF::getLanguage()->get('acp.eventsManagement.form.event.active'));
        $enabled->setDescription(RWF::getLanguage()->get('acp.eventsManagement.form.event.active.description'));
        $enabled->requiredField(true);
        $this->addFormElement($enabled);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}