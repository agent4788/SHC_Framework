<?php

namespace MB\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\Select;
use RWF\Form\FormElements\TextField;
use RWF\Form\TabbedHtmlForm;
use MB\Form\FormElements\LanguageChooser;
use MB\Form\FormElements\WebStyleChooser;
use SHC\Sensor\SensorPoint;

/**
 * Einstellungen Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SettingsForm extends TabbedHtmlForm {

    /**
     * @param SensorPoint $sensorPoint
     */
    public function __construct(SensorPoint $sensorPoint = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Allgemeine Einstellungen
        $this->addTab('global', RWF::getLanguage()->get('acp.settings.tabs.global'), RWF::getLanguage()->get('acp.settings.tabs.global.description'));

        //Langzeit Login erlauben
        $longTimeLogin = new OnOffOption('rwf_session_allowLongTimeLogin',RWF::getSetting('rwf.session.allowLongTimeLogin'));
        $longTimeLogin->setTitle(RWF::getLanguage()->get('acp.settings.form.allowLongTimeLogin'));
        $longTimeLogin->setDescription(RWF::getLanguage()->get('acp.settings.form.allowLongTimeLogin.decription'));
        $longTimeLogin->requiredField(true);
        $this->addFormElementToTab('global', $longTimeLogin);

        //Style
        $style = new WebStyleChooser('mb_defaultStyle',RWF::getSetting('mb.defaultStyle'));
        $style->setTitle(RWF::getLanguage()->get('acp.settings.form.defaultStyle'));
        $style->setDescription(RWF::getLanguage()->get('acp.settings.form.defaultStyle.decription'));
        $style->requiredField(true);
        $this->addFormElementToTab('global', $style);

        //Sprache
        $language = new LanguageChooser('rwf_language_defaultLanguage');
        $language->setTitle(RWF::getLanguage()->get('acp.settings.form.defaultLanguage'));
        $language->setDescription(RWF::getLanguage()->get('acp.settings.form.defaultLanguage.decription'));
        $language->requiredField(true);
        $this->addFormElementToTab('global', $language);

        //Datum und Zeit
        $this->addTab('dateTime', RWF::getLanguage()->get('acp.settings.tabs.dateTime'), RWF::getLanguage()->get('acp.settings.tabs.dateTime.description'));

        //Zeitzone
        $timezone = new Select('rwf_date_Timezone');
        $values = array();
        foreach(\DateTimeZone::listAbbreviations() as $orderedTimezone) {

            foreach($orderedTimezone as $timezoneOption) {

                $values[$timezoneOption['timezone_id']] = array($timezoneOption['timezone_id'], ($timezoneOption['timezone_id'] == RWF::getSetting('rwf.date.Timezone') ? 1 : 0));
            }
        }
        $timezone->setValues($values);
        $timezone->setTitle(RWF::getLanguage()->get('acp.settings.form.Timezone'));
        $timezone->setDescription(RWF::getLanguage()->get('acp.settings.form.Timezone.decription'));
        $timezone->requiredField(true);
        $this->addFormElementToTab('dateTime', $timezone);

        //Datumsformat
        $dateFormat = new TextField('rwf_date_defaultDateFormat',RWF::getSetting('rwf.date.defaultDateFormat'), array('maxlength' => 15));
        $dateFormat->setTitle(RWF::getLanguage()->get('acp.settings.form.defaultDateFormat'));
        $dateFormat->setDescription(RWF::getLanguage()->get('acp.settings.form.defaultDateFormat.decription'));
        $dateFormat->requiredField(true);
        $this->addFormElementToTab('dateTime', $dateFormat);

        //Teitformat
        $timeFormat = new TextField('rwf_date_defaultTimeFormat',RWF::getSetting('rwf.date.defaultTimeFormat'), array('maxlength' => 15));
        $timeFormat->setTitle(RWF::getLanguage()->get('acp.settings.form.defaultTimeFormat'));
        $timeFormat->setDescription(RWF::getLanguage()->get('acp.settings.form.defaultTimeFormat.decription'));
        $timeFormat->requiredField(true);
        $this->addFormElementToTab('dateTime', $timeFormat);

        //Timeline
        $useTimeline = new Select('rwf_date_useTimeline');
        $useTimeline->setValues(array(
            1 => array(RWF::getLanguage()->get('acp.settings.tabs.timeline.true'), (RWF::getSetting('rwf.date.useTimeline') == 1 ? 1 : 0)),
            0 => array(RWF::getLanguage()->get('acp.settings.tabs.timeline.false'), (RWF::getSetting('rwf.date.useTimeline') == 0 ? 1 : 0))
        ));
        $useTimeline->setTitle(RWF::getLanguage()->get('acp.settings.form.useTimeline'));
        $useTimeline->setDescription(RWF::getLanguage()->get('acp.settings.form.useTimeline.decription'));
        $useTimeline->requiredField(true);
        $this->addFormElementToTab('dateTime', $useTimeline);

        //Offset Sunenaufgang
        $sunriseOffset = new IntegerInputField('rwf_date_sunriseOffset',RWF::getSetting('rwf.date.sunriseOffset'), array('min' => -90, 'max' => 90));
        $sunriseOffset->setTitle(RWF::getLanguage()->get('acp.settings.form.sunriseOffset'));
        $sunriseOffset->setDescription(RWF::getLanguage()->get('acp.settings.form.sunriseOffset.decription', DateTime::now()->getSunrise()->format('H:i')));
        $sunriseOffset->requiredField(true);
        $this->addFormElementToTab('dateTime', $sunriseOffset);

        //Offset Sonnenuntergang
        $sunsetOffset = new IntegerInputField('rwf_date_sunsetOffset',RWF::getSetting('rwf.date.sunsetOffset'), array('min' => -90, 'max' => 90));
        $sunsetOffset->setTitle(RWF::getLanguage()->get('acp.settings.form.sunsetOffset'));
        $sunsetOffset->setDescription(RWF::getLanguage()->get('acp.settings.form.sunsetOffset.decription', DateTime::now()->getSunset()->format('H:i')));
        $sunsetOffset->requiredField(true);
        $this->addFormElementToTab('dateTime', $sunsetOffset);

        //Latitude
        $latitude = new TextField('rwf_date_Latitude',RWF::getSetting('rwf.date.Latitude'), array('maxlength' => 15));
        $latitude->setTitle(RWF::getLanguage()->get('acp.settings.form.Latitude'));
        $latitude->setDescription(RWF::getLanguage()->get('acp.settings.form.Latitude.decription'));
        $latitude->requiredField(true);
        $this->addFormElementToTab('dateTime', $latitude);

        //Longitude
        $longitude = new TextField('rwf_date_Longitude',RWF::getSetting('rwf.date.Longitude'), array('maxlength' => 15));
        $longitude->setTitle(RWF::getLanguage()->get('acp.settings.form.Longitude'));
        $longitude->setDescription(RWF::getLanguage()->get('acp.settings.form.Longitude.decription'));
        $longitude->requiredField(true);
        $this->addFormElementToTab('dateTime', $longitude);

        //Benutzeroberflaeche
        $this->addTab('ui', RWF::getLanguage()->get('acp.settings.tabs.ui'), RWF::getLanguage()->get('acp.settings.tabs.ui.description'));

        //Titelzeile
        $title = new TextField('mb_title',RWF::getSetting('mb.title'), array('minlength' => 3, 'maxlength' => 35));
        $title->setTitle(RWF::getLanguage()->get('acp.settings.form.title'));
        $title->setDescription(RWF::getLanguage()->get('acp.settings.form.title.decription'));
        $title->requiredField(true);
        $this->addFormElementToTab('ui', $title);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}