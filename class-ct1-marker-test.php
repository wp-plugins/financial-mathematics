<?php

require_once 'class-ct1-marker.php';
class CT1_Marker_Test extends PHPUnit_Framework_TestCase
{
  private $marker;
  
  public function setup(){
    $this->marker = new CT1_Marker();
  }
  public function tearDown(){}
  
  public function test_score()
  {
    $actual = 12.345678;
    $guess = 12.346;
    $score = $this->marker->score($actual, $guess);
    $this->assertEquals( $score['credit'], 5); // 5 correct sig fig
    $this->assertEquals( $score['available'], 8); // 8 sig fig

    $guess = 12.345;
    $score = $this->marker->score($actual, $guess);
    $this->assertEquals( $score['credit'], 4); // 4 correct sig fig
    
    $actual = -12.3456;
    $guess = -12.3;
    $score = $this->marker->score($actual, $guess);
    $this->assertEquals( $score['credit'], 3); // 5 correct sig fig
    $this->assertEquals( $score['available'], 6); // 8 sig fig

    $actual = -12.345678;
    $guess = 12.346;
    $score = $this->marker->score($actual, $guess);
    $this->assertEquals( $score['credit'], 0); // 0 correct sig fig
    $this->assertEquals( $score['available'], 8); // 8 sig fig

  }  


}
