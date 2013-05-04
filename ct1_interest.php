<?php   

require_once 'ct1_format.php';
require_once 'ct1_marker.php';

class ct1_interest{

protected $m_cont = 366; // any frequency > $m_cont will be treatead as continuous;
protected $i = 0;
protected $m = 1;
protected $advance = false;
protected $upToDate;

protected function isValid(){
  if ($this->m < 0) { return false;}
  if (!is_numeric($this->m)) {  return false;}
  if (!is_numeric($this->i)) {  return false;}
  return true;
}

protected function mContinuous($m){
  if ($m > $this->m_cont) return true;
  return false;
}


protected function isContinuous(){
  if ($this->m > $this->m_cont) return true;
  return false;
}

public function getAll(){
  return array('i'=>$this->i, 'm'=> $this->m, 'adv'=> $this->advance);
}

public function getDescription(){
  if ($this->advance) return "discount";
  return "interest";
}


public function getRate(){
  $m = new ct1_marker();
  $d = $m->no_dps(100*$this->i);
  return sprintf('%.' . ($d) . 'f%%', 100 * $this->i);
}

public function getFrequency(){
  if (1==$this->m) return "";
  if (2==$this->m) return " convertible half-yearly";
  if (4==$this->m) return " convertible quarterly";
  if (12==$this->m) return " convertible monthly";
  if ($this->isContinuous()) return " convertible continuously";
  return " convertible " . $this->m . " times per year";
}


public function set_i($i){
  if (is_numeric($i)){
    $this->i = $i;
    $this->advance = false;
  }
  else{
    throw new Exception('attempt to set non-numeric interest rate');
  }
}

public function set_d($d){
  if (is_numeric($d)){
    $this->i = $d;
    $this->advance = true;
  }
  else{
    throw new Exception('attempt to set non-numeric interest rate');
  }
}

public function set_m($m){
  if (is_numeric($m)){
    if ($m > 0){
      $this->m = $m;
    }
    else{
      throw new Exception('attempt to set non-positive frequency');
    }
  }
  else{
    throw new Exception('attempt to set non-numeric frequency');
  }
}

protected function getIEffective(){
  if (!$this->advance){
    $v_m = 1.0 / (1 + $this->i / $this->m );
  }
  else{
    $v_m = 1.0 - $this->i / $this->m ;
  }
  $ip = pow($v_m,(-$this->m));
  return $ip - 1;
}

protected function getIEffectiveLatex(){
  $out = ""; $years = "year";
  if (1==$this->m) { $term = "1"; $im = $this->i;}
  else {$term = "\\frac{1}{" . $this->m . "}"; $im = "\\frac{" . $this->i . "}{" . $this->m . "}";}
  if (!$this->advance){
    $out = "\$1\$ accumulates to \$1 + $im\$ after a term of \$$term\$ $years.";
  }
  else{
    $out = "\$1\$ payable after a term of $term $years has present value \$1 - $im\$.";
  }
  return $out;
}

public function getD($m = 1){
    if ($this->isValid()){
      if ($this->mContinuous($m)){
        $val =  log(1 + $this->getIEffective());
        $logo = "\\delta";
      }
      else{
        $v_m = pow(1.0 + $this->getIEffective(),(-1/$m));
        $val = (1 - $v_m) * $m;
        $logo = "d^{" . $m . "}";
        if (1==$m) $logo="d";
      }
      return array('value'=>$val, 'logo'=>$logo);
    }
    else{
      throw new Exception('attempt to get invalid interest rate');
    }
}

public function getI($m = 1){
    if ($this->isValid()){ 
       if ($this->mContinuous($m)){
         $val = log(1 + $this->getIEffective());
         $logo = "\\delta";
       }
       else{
         $ip1_m = pow(1.0 +$this->getIEffective(),(1/$m));
         $logo = "i^{" . $m . "}";
         if (1==$m) $logo="i";
         $val = ($ip1_m - 1) * $m;
       }
       return array('value'=>$val, 'logo'=>$logo);
    }
    else{
      throw new Exception('attempt to get invalid interest rate');
    }
}

public function temp(){
   return $this->getIEffectiveLatex();
}

} // end of class ct1_interest

// test
$i = new ct1_interest();
$i->set_m(2);
$i->set_d(0.2);
/*
echo "i4 " . print_r($i->getI(4),1) . "\r\n";
//$i->set_d('alpha');
echo "d4" . print_r($i->getD(4),1) . "\r\n";
echo "i365" . print_r($i->getI(365),1) . "\r\n";
echo "d365" . print_r($i->getD(365),1) . "\r\n";
echo "i1365" . print_r($i->getI(1365),1) . "\r\n";
echo "d1365" . print_r($i->getD(1365),1) . "\r\n";
*/
echo $i->temp();
?>
