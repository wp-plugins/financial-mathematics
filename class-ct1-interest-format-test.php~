<?php

require_once 'class-ct1-interest.php';
class CT1_Interest_test extends PHPUnit_Framework_TestCase
{
  private $icalc;
  private $i;
  private $freq;
  
  public function setup(){
    $this->icalc = new CT1_Interest();
    $this->i = 0.06;
    $this->freq = 1;
    $this->icalc->set_m($this->freq);
    $this->icalc->set_i($this->i);
  }
  public function tearDown(){}
  
  public function test_iconverti()
  {
    $t = new CT1_Interest();
    $t->set_m(12);
    $t->set_i(0);
    $this->assertTrue( abs($this->icalc->getI($t)-  0.058411) < 0.000001);
    // source of numbers: Formulae and tables 6% p.58  i(12)
  }  

  public function test_iconvertd()
  {
    $t = new CT1_Interest();
    $t->set_m(12);
    $t->set_d(0);
    $this->assertTrue( abs($this->icalc->getI($t)-  0.06 / 1.032211) < 0.000001);
    // source of numbers: Formulae and tables 6% p.58   i/d(12)
  }  

  public function test_iconvertdel()
  {
    $t = new CT1_Interest();
    $t->set_m(367); // anything greater than 366 is treated as continuous
    $t->set_d(0);
    $this->assertTrue( abs($this->icalc->getI($t)-  0.058269) < 0.000001);
    // source of numbers: Formulae and tables 6% p.58   delta
  }  


  public function test_getAll()
  {
    $this->icalc->set_m(13);
    $this->icalc->set_i(0.04321);
    $g = $this->icalc->getAll();
    $this->assertEquals( $g['i'], 0.04321);
    $this->assertEquals( $g['m'], 13);
    $this->assertEquals( $g['adv'], false);

    $this->icalc->set_d(0.04321);
    $g = $this->icalc->getAll();
    $this->assertEquals( $g['adv'], true);
  }  

}
