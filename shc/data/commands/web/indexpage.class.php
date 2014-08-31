<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\PageCommand;

/**
 * Startseite
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class IndexPage extends PageCommand {
    
    /**
     * Template
     * 
     * @var String
     */
    protected $template = 'test.html';

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $form = new \RWF\Form\DefaultHtmlForm('test');
        $form->setAction('index.php?app=shc');
        $form->addFormElement((new \RWF\Form\FormElements\TextField('test', 'test123', array('minlength' => 3, 'maxlength' => 15)))->setTitle('Test 1'));
        $form->addFormElement((new \RWF\Form\FormElements\PasswordField('test1', '', array('minlength' => 5, 'maxlength' => 15)))->setTitle('Test 2'));
        $form->addFormElement((new \RWF\Form\FormElements\TextArea('test3', '', array('minlength' => 5, 'maxlength' => 250)))->setTitle('Test 3'));
        $form->addFormElement((new \RWF\Form\FormElements\IntegerInputField('test4', '512', array('min' => -100, 'max' => 1000, 'step' => 50)))->setTitle('Test 4'));
        $form->addFormElement((new \RWF\Form\FormElements\FloatInputField('test5', '27.5', array('min' => -10.0, 'max' => 50.0, 'step' => 0.5)))->setTitle('Test 5'));
        $form->addFormElement((new \RWF\Form\FormElements\OnOffOption('test6', true))->setYesNoLabel()->setTitle('Test 6'));
        $form->addFormElement((new \RWF\Form\FormElements\CheckBoxes('test7', array(1 => 'Test 1', 2 => 'Test 2')))->setTitle('Test 7')->requiredField(true));
        $form->addFormElement((new \RWF\Form\FormElements\RadioButtons('test8', array(1 => array('Test 1', 1), 2 => 'Test 2')))->setTitle('Test 8')->requiredField(true));
        $form->addFormElement((new \RWF\Form\FormElements\Select('test9', array(1 => 'Test 1', 2 => array('Test 2', 1))))->setTitle('Test 9'));
        $form->addFormElement((new \RWF\Form\FormElements\SelectWithEmptyElement('test10', array(1 => 'Test 1', 2 => array('Test 2', 1))))->setTitle('Test 10'));
        $form->addFormElement((new \RWF\Form\FormElements\SelectMultiple('test11', array(1 => array('Test 1', 1), 2 => array('Test 2', 1))))->setTitle('Test 11'));
        $form->addFormElement((new \RWF\Form\FormElements\SelectMultipleWithEmptyElement('test12', array(1 => array('Test 1', 1), 2 => array('Test 2', 1))))->setTitle('Test 12'));
        $form->addFormElement((new \RWF\Form\FormElements\Slider('test13', '512', array('min' => -100, 'max' => 1000, 'step' => 1)))->setTitle('Test 13'));
        if($form->isSubmitted()) $form->validate();
        $this->templateObject->assign('form', $form);
    }
}
