<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-concept-mortgage.php';

class CT1_Form_Test extends PHPUnit_Framework_TestCase
{
  private $debug = false;
  private $form;
  private $calculator;
  private $obj;
  
  public function setup(){
    $this->obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
    $this->form = new CT1_Concept_Mortgage($this->obj);
	$empty = array();
    $this->calculator = $this->form->get_calculator( $empty );
  }
  public function tearDown(){}
  
  public function test_form_mortgage()
  {
  
  
  $expected = array
(
    'name' => 'CT1_calculator',
    'method' => 'GET',
    'parameters' => array
        (
            'm' => array
                (
                    'name' => 'm',
                    'label' => 'Instalment frequency per year',
                ),

            'advance' => array
                (
                    'name' => 'advance',
                    'label' => 'Paid in advance',
                ),

            'delta' => array
                (
                    'name' => 'delta',
                    'label' => 'Interest rate per year (continuously compounded)',
                ),

            'i_effective' => array
                (
                    'name' => 'i_effective',
                    'label' => 'Interest rate per year (annual effective rate)',
                ),

            'term' => array
                (
                    'name' => 'term',
                    'label' => 'Term (years)',
                ),

            'principal' => array
                (
                    'name' => 'principal',
                    'label' => 'Principal',
                ),

        ),

    'valid_options' => array
        (
            'm' => array
                (
                    'type' => 'number',
                    'decimal' => '.',
                    'min' => '1.0E-5',
                ),

            'advance' => array
                (
                    'type' => 'boolean',
                ),

            'delta' => array
                (
                    'type' => 'number',
                    'decimal' => '.',
                ),

            'i_effective' => array
                (
                    'type' => 'number',
                    'decimal' => '.',
                    'min' => '-0.99',
                ),

            'term' => array
                (
                    'type' => 'number',
                    'decimal' => '.',
                    'min' => '0',
                ),

            'principal' => array
                (
                    'type' => 'number',
                    'decimal' => '.',
                ),

        ),

    'values' => array
        (
            'm' => '12',
            'advance' => '1',
            'delta' => '0.058268908123976',
            'i_effective' => '0.06',
            'term' => '10',
            'principal' => '1000000',
        ),

    'request' => 'get_mortgage_instalment',
    'submit' => 'Just show me the instalment amount',
    'type' => '',
    'special_input' => '', 
    'action' => '',
    'exclude' => array
        (
        ),

    'render' => 'HTML',
    'introduction' => 'Calculate the amount of each level mortgage instalment.',
);

	  $this->assertEquals( $expected, $this->calculator ) ;
  }  

  
}
