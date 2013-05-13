<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-concept-mortgage.php';

class CT1_Form_Test extends PHPUnit_Framework_TestCase
{
  private $debug = false;
  private $form;
  private $html;
  private $obj;
  
  public function setup(){
    $this->obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
    $this->form = new CT1_Concept_Mortgage($this->obj);
    $this->html = $this->form->get_calculator();
  }
  public function tearDown(){}
  
  public function test_form_mortgage()
  {
  
  	$expected = file_get_contents("test-form-mortgage.html");
	  $this->assertEquals( $expected, $this->html ) ;
  }  

  public function test_form_mortgage_no_delta()
  {
  
    $this->html = $this->form->get_calculator(array("delta"));
  	$expected = file_get_contents("test-form-mortgage-no-delta.html");
	  $this->assertEquals( $expected, $this->html ) ;
  }  
  
}
