<?php

require_once 'ct1_mortgage.php';
class ct1_mortgage_test extends PHPUnit_Framework_TestCase
{
  private $schedule;
  private $mcalc;
  private $acalc;
  private $term;
  private $i;
  private $freq;
  private $adv;
  private $principal;
  private $neg = 0.00001;
  
  public function setup(){
    $this->acalc = new ct1_annuity();
    $this->mcalc = new ct1_mortgage();
    $this->term = 10;
    $this->i = 0.06;
    $this->freq = 12;
    $this->adv = true;
    $this->principal = 1000000;
    $this->schedule = $this->scal();
  }
  public function tearDown(){}
  
  private function aval(){
    return $this->acalc->annuityCertain($this->term, $this->i, $this->freq, $this->adv);
  }

  private function scal(){
    return $this->mcalc->mortgageSchedule($this->term, $this->i, $this->freq, $this->adv, $this->principal);
  }

  public function test_mortgageSchedule()
  {
    $inst = 1/12 * 1000000 * 1/(1.032211 * 7.3601);
    $int = (1000000 - $inst) * 0.058411/12;
    // source of numbers: Formulae and tables 6% p.58   i/d(12), a10, i(12)
    $this->assertEquals( $this->schedule[1]['count'],1 );
    $this->assertEquals( $this->schedule[1]['oldPrincipal'],1000000 );
    $this->assertTrue( abs($this->schedule[1]['instalment']- $inst) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['interest'] - $int ) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['capRepay'] - ($inst - $int) ) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['newPrincipal'] - (1000000 - $inst + $int) ) < 0.1);
    // test half way through
    $remain = $inst * 12 * 1.032211 * 4.2124;
    $int = ($remain - $inst) * 0.058411/12;
    // source of new numbers: Formulae and tables 6% p.58   a5
    $this->assertEquals( $this->schedule[61]['count'],61 );
    $this->assertTrue( abs($this->schedule[61]['oldPrincipal'] - $remain) < 10 );
    $this->assertTrue( abs($this->schedule[61]['instalment']- $inst) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['interest'] - $int ) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['capRepay'] - ($inst - $int) ) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['newPrincipal'] - ($remain - $inst + $int) ) < 10);
  }  
 
  public function test_mortgageScheduleNilInterest()
  {
    $this->i = 0;
    $this->freq = 12;
    $this->principal = 1200000;
    $this->schedule = $this->scal();
    $inst = 10000;
    $int = 0;
    // source of numbers: Formulae and tables 6% p.58   i/d(12), a10, i(12)
    $this->assertEquals( $this->schedule[1]['count'],1 );
    $this->assertEquals( $this->schedule[1]['oldPrincipal'],1200000 );
    $this->assertTrue( abs($this->schedule[1]['instalment']- $inst) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['interest'] - $int ) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['capRepay'] - ($inst - $int) ) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['newPrincipal'] - (1200000 - $inst + $int) ) < 0.1);
    // test half way through
    $remain = 600000;
    // source of new numbers: Formulae and tables 6% p.58   a5
    $this->assertEquals( $this->schedule[61]['count'],61 );
    $this->assertTrue( abs($this->schedule[61]['oldPrincipal'] - $remain) < 10 );
    $this->assertTrue( abs($this->schedule[61]['instalment']- $inst) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['interest'] - $int ) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['capRepay'] - ($inst - $int) ) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['newPrincipal'] - ($remain - $inst + $int) ) < 10);
  }  
  
}
