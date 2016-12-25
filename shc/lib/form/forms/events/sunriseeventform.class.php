<?php

namespace SHC\Form\Forms\Events;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Event\AbstractEvent;
use SHC\Event\Events\Sunrise;
use SHC\Event\Events\Sunset;

/**
 * Sonnenaufgang/untergang Ereignis
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SunriseEventForm extends DefaultHtmlForm {

    /**
     * @param AbstractEvent $condition
     */
    public function __construct(AbstractEvent $event = null) {

        //Pruefen ob zulaessiges Objekt uebergeben
        if($event !== null && !$event instanceof Sunrise && !$event instanceof Sunset) {

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