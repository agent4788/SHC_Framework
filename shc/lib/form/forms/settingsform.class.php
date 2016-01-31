<?php

namespace SHC\Form\Forms;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Form\FormElements\FloatInputField;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\OnOffOption;
use RWF\Form\FormElements\Select;
use RWF\Form\FormElements\TextField;
use RWF\Form\TabbedHtmlForm;
use SHC\Form\FormElements\LanguageChooser;
use SHC\Form\FormElements\WebStyleChooser;
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
        $style = new WebStyleChooser('shc_defaultStyle',RWF::getSetting('shc.defaultStyle'));
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
        $title = new TextField('shc_title',RWF::getSetting('shc.title'), array('minlength' => 3, 'maxlength' => 35));
        $title->setTitle(RWF::getLanguage()->get('acp.settings.form.title'));
        $title->setDescription(RWF::getLanguage()->get('acp.settings.form.title.decription'));
        $title->requiredField(true);
        $this->addFormElementToTab('ui', $title);

        //Umleitung aktiv
        $redirectActive = new OnOffOption('shc_ui_redirectActive', RWF::getSetting('shc.ui.redirectActive'));
        $redirectActive->setTitle(RWF::getLanguage()->get('acp.settings.form.redirectActive'));
        $redirectActive->setDescription(RWF::getLanguage()->get('acp.settings.form.redirectActive.decription'));
        $redirectActive->requiredField(true);
        $this->addFormElementToTab('ui', $redirectActive);

        //PC umleiten auf Oberflaeche
        $redirectPc = new Select('shc_ui_redirectPcTo');
        $redirectPc->setValues(array(
                1 => array(RWF::getLanguage()->get('acp.settings.tabs.redirect.pc'), (RWF::getSetting('shc.ui.redirectPcTo') == 1 ? 1 : 0)),
                3 => array(RWF::getLanguage()->get('acp.settings.tabs.redirect.smartphone'), (RWF::getSetting('shc.ui.redirectPcTo') == 3 ? 1 : 0))
        ));
        $redirectPc->setTitle(RWF::getLanguage()->get('acp.settings.form.redirectPcTo'));
        $redirectPc->setDescription(RWF::getLanguage()->get('acp.settings.form.redirectPcTo.decription'));
        $redirectPc->requiredField(true);
        $this->addFormElementToTab('ui', $redirectPc);

        //Tablet umleiten auf Oberflaeche
        $redirectTablet = new Select('shc_ui_redirectTabletTo');
        $redirectTablet->setValues(array(
            1 => array(RWF::getLanguage()->get('acp.settings.tabs.redirect.pc'), (RWF::getSetting('shc.ui.redirectTabletTo') == 1 ? 1 : 0)),
            3 => array(RWF::getLanguage()->get('acp.settings.tabs.redirect.smartphone'), (RWF::getSetting('shc.ui.redirectTabletTo') == 3 ? 1 : 0))
        ));
        $redirectTablet->setTitle(RWF::getLanguage()->get('acp.settings.form.redirectTabletTo'));
        $redirectTablet->setDescription(RWF::getLanguage()->get('acp.settings.form.redirectTabletTo.decription'));
        $redirectTablet->requiredField(true);
        $this->addFormElementToTab('ui', $redirectTablet);

        //Smartphone umleiten auf Oberflaeche
        $redirectSmartphone = new Select('shc_ui_redirectSmartphoneTo');
        $redirectSmartphone->setValues(array(
            1 => array(RWF::getLanguage()->get('acp.settings.tabs.redirect.pc'), (RWF::getSetting('shc.ui.redirectSmartphoneTo') == 1 ? 1 : 0)),
            3 => array(RWF::getLanguage()->get('acp.settings.tabs.redirect.smartphone'), (RWF::getSetting('shc.ui.redirectSmartphoneTo') == 3 ? 1 : 0))
        ));
        $redirectSmartphone->setTitle(RWF::getLanguage()->get('acp.settings.form.redirectSmartphoneTo'));
        $redirectSmartphone->setDescription(RWF::getLanguage()->get('acp.settings.form.redirectSmartphoneTo.decription'));
        $redirectSmartphone->requiredField(true);
        $this->addFormElementToTab('ui', $redirectSmartphone);

        //Wer ist zu Hause Box auf der Startseite anzeigen
        $showUsersAtHome = new OnOffOption('shc_ui_index_showUsersAtHome', RWF::getSetting('shc.ui.index.showUsersAtHome'));
        $showUsersAtHome->setTitle(RWF::getLanguage()->get('acp.settings.form.showUsersAtHome'));
        $showUsersAtHome->setDescription(RWF::getLanguage()->get('acp.settings.form.showUsersAtHome.decription'));
        $showUsersAtHome->requiredField(true);
        $this->addFormElementToTab('ui', $showUsersAtHome);

        //Fritz Box
        $this->addTab('fritzBox', RWF::getLanguage()->get('acp.settings.tabs.fritzBox'), RWF::getLanguage()->get('acp.settings.tabs.fritzBox.description'));

        //FritzBox Adresse
        $fbAddress = new TextField('rwf_fritzBox_address',RWF::getSetting('rwf.fritzBox.address'), array('maxlength' => 25));
        $fbAddress->setTitle(RWF::getLanguage()->get('acp.settings.form.fbAddress'));
        $fbAddress->setDescription(RWF::getLanguage()->get('acp.settings.form.fbAddress.decription'));
        $fbAddress->requiredField(true);
        $this->addFormElementToTab('fritzBox', $fbAddress);

        //hat die Fritz!Box ein 5GHz WLAn
        $_5ghzWlan = new OnOffOption('rwf_fritzBox_has5GHzWlan', RWF::getSetting('rwf.fritzBox.has5GHzWlan'));
        $_5ghzWlan->setYesNoLabel();
        $_5ghzWlan->setTitle(RWF::getLanguage()->get('acp.settings.form.5GHzWlan'));
        $_5ghzWlan->setDescription(RWF::getLanguage()->get('acp.settings.form.5GHzWlan.decription'));
        $_5ghzWlan->requiredField(true);
        $this->addFormElementToTab('fritzBox', $_5ghzWlan);

        //FritzBox Adresse
        $fbUser = new TextField('rwf_fritzBox_user',RWF::getSetting('rwf.fritzBox.user'), array('maxlength' => 25));
        $fbUser->setTitle(RWF::getLanguage()->get('acp.settings.form.fbUser'));
        $fbUser->setDescription(RWF::getLanguage()->get('acp.settings.form.fbUser.decription'));
        $fbUser->requiredField(true);
        $this->addFormElementToTab('fritzBox', $fbUser);

        //FritzBox Adresse
        $fbPassword = new TextField('rwf_fritzBox_password',RWF::getSetting('rwf.fritzBox.password'), array('maxlength' => 25));
        $fbPassword->setTitle(RWF::getLanguage()->get('acp.settings.form.fbPassword'));
        $fbPassword->setDescription(RWF::getLanguage()->get('acp.settings.form.fbPassword.decription'));
        $fbPassword->requiredField(true);
        $this->addFormElementToTab('fritzBox', $fbPassword);

        //Fritz Box
        $this->addTab('energy', RWF::getLanguage()->get('acp.settings.tabs.energy'), RWF::getLanguage()->get('acp.settings.tabs.energy.description'));

        //Strompreis
        $electricityPrice = new FloatInputField('shc_energy_electricityPrice', RWF::getSetting('shc.energy.electricityPrice'), array('min' => 0.0, 'max' => 5.0, 'step' => 0.01));
        $electricityPrice->setTitle(RWF::getLanguage()->get('acp.settings.form.electricityPrice'));
        $electricityPrice->setDescription(RWF::getLanguage()->get('acp.settings.form.electricityPrice.decription'));
        $electricityPrice->requiredField(true);
        $this->addFormElementToTab('energy', $electricityPrice);

        //Wasserpreis
        $waterPrice = new FloatInputField('shc_energy_waterPrice', RWF::getSetting('shc.energy.waterPrice'), array('min' => 0.0, 'max' => 5.0, 'step' => 0.01));
        $waterPrice->setTitle(RWF::getLanguage()->get('acp.settings.form.waterPrice'));
        $waterPrice->setDescription(RWF::getLanguage()->get('acp.settings.form.waterPrice.decription'));
        $waterPrice->requiredField(true);
        $this->addFormElementToTab('energy', $waterPrice);

        //Gaspreis
        $gasPrice = new FloatInputField('shc_energy_gasPrice', RWF::getSetting('shc.energy.gasPrice'), array('min' => 0.0, 'max' => 5.0, 'step' => 0.01));
        $gasPrice->setTitle(RWF::getLanguage()->get('acp.settings.form.gasPrice'));
        $gasPrice->setDescription(RWF::getLanguage()->get('acp.settings.form.gasPrice.decription'));
        $gasPrice->requiredField(true);
        $this->addFormElementToTab('energy', $gasPrice);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}