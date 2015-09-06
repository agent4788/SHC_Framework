<?php

namespace SHC\Form\Forms\Events;

//Imports
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\FloatInputField;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\TextField;
use SHC\Core\Exception\AssertException;
use SHC\Core\SHC;
use SHC\Event\AbstractEvent;
use SHC\Event\Events\FileCreate;
use SHC\Event\Events\FileDelete;
use SHC\Event\Events\HumidityClimbOver;
use SHC\Event\Events\HumidityFallsBelow;
use SHC\Form\FormElements\SensorChooser;

/**
 * Luftfeuchte Ereignis
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FileEventForm extends DefaultHtmlForm {

    /**
     * @param AbstractEvent $condition
     */
    public function __construct(AbstractEvent $event = null) {

        //Pruefen ob zulaessiges Objekt uebergeben
        if($event !== null && !$event instanceof FileCreate && !$event instanceof FileDelete) {

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

        //Ueberwachte Datei
        $file = new TextField('file', ($event !== null ? $event->getData()['file'] : ''), array('minlength' => 2, 'maxlength' => 100));
        $file->setTitle(RWF::getLanguage()->get('acp.eventsManagement.form.event.file'));
        $file->setDescription(RWF::getLanguage()->get('acp.eventsManagement.form.event.file.description'));
        $file->requiredField(true);
        $this->addFormElement($file);

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