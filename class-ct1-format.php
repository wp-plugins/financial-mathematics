<?php   

class CT1_Format{

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
