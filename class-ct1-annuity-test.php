<?php

require_once 'class-ct1-annuity.php';
class CT1_Annuity_Test extends PHPUnit_Framework_TestCase
{
  private $acalc;
  private $term;
  private $i;
  private $freq;
  private $adv;
  private $neg = 0.00001;
  
  public function setup(){
    $this->acalc = new CT1_Annuity();
    $this->term = 10;
    $this->i = 0.06;
    $this->freq = 12;
    $this->adv = true;
  }
  public function tearDown(){}
  
  private function aval(){
    return $this->acalc->annuityCertain($this->term, $this->i, $this->freq, $this->adv);
  }

  private function ival(){
    return $this->acalc->im($this->i, $this->freq);
  }

  private function an(){
    if (0==$this->i) return $this->term;
    return (1 - pow((1 + $this->i), -$this->term))/$this->i;
  }

  public function test_an()
  {
    $this->assertTrue( abs($this->an() - 7.3601) < 0.0001 );
    // source of numbers: Formulae and tables 6% p.58 an 
  }  
 
  public function test_annuityValueAdvance()
  {
    $this->assertTrue( abs($this->aval() - $this->an()*1.032211) < $this->neg );
    // source of numbers: Formulae and tables 6% p.58  i/d(12)
  }  

  public function test_annuityValueContinuous()
  {
    $this->freq = 'continuous';
//    $this->assertEquals( $this->aval() , $this->an()*1.029709);
    $this->assertTrue( abs($this->aval() - $this->an()*1.029709) < $this->neg );
    // source of numbers: Formulae and tables 6% p.58   i/delta
  }  
  
  public function test_annuityValueArrears()
  {
    $this->adv = false;
    $this->assertTrue( abs($this->aval() - $this->an()*1.027211) < $this->neg );
    // source of numbers: Formulae and tables 6% p.58  i/i(12)
  }  

  public function test_annuityValueNilInt()
  {
    $this->i = 0;
    $this->assertEquals( $this->aval(), 10) ;
  }  

  public function test_annuityValueNilTerm()
  {
    $this->term = 0;
    $this->assertEquals( $this->aval(), 0) ;
  }  
  
  public function test_im()
  {
    $this->adv = false;
    $this->assertTrue( abs($this->ival() - 0.058411) < 0.000001 );
    // source of numbers: Formulae and tables 6% p.58  i(12)
  }  

  public function test_delta()
  {
    $this->freq = 'continuous';
    $this->assertTrue( abs($this->ival() - 0.058269) < 0.000001 );
    // source of numbers: Formulae and tables 6% p.58   delta
  }  

}