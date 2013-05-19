<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-annuity-escalating.php';
class CT1_Annuity_Escalating_Test extends PHPUnit_Framework_TestCase
{
  private $debug = true;
  private $acalc;
  private $term = 10;
  private $i = 0.06;
  private $e = 0.13;
  private $freq_escalating = 1;
  private $freq = 12;
  private $adv = true;
  private $neg = 0.00001;
  
  public function setup(){
    $this->acalc = new CT1_Annuity_Escalating();
    $this->reset();
	print_r($this->acalc->get_values());
  }
  public function tearDown(){}
  
  private function reset(){
  	$this->acalc->set_m($this->freq);
  	$this->acalc->set_advance($this->adv);
  	$this->acalc->set_delta(log(1+$this->i));
  	$this->acalc->set_term($this->term);
  	$this->acalc->set_escalation_rate_effective($this->e);
  	$this->acalc->set_escalation_frequency($this->freq_escalating);
  }
  	
  private function aval(){
  	$this->reset();
    return $this->acalc->get_annuity_certain();
  }

  public function test_rate()
  {
    if ($this->debug) $this->assertEquals( $this->aval() , 1);
    $this->assertTrue( abs($this->aval() - 1 ) < $this->neg );
    // source of numbers
  }  
 

}
