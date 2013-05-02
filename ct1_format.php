<?php   

class ct1_format{

  public function mycurrency($amount){
   return money_format('%i',$amount);
  }

  public function mynumber($number){
   return number_format($number);
  }

  public function locale(){
   return 'en_GB';
  }

}
?>
