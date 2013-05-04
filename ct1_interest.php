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

public function sameForm(ct1_interest $c){
  $thisAll = $this->getAll();
  $cAll = $c->getAll();
  if( $thisAll['m']==$cAll['m'] && $thisAll['adv']==$cAll['adv'] ) return true;
  return false;
}

public function getDescription(){
  if ($this->advance) return "discount";
  return "interest";
}


public function getAdvance(){
  return $this->advance;
}

public function setAdvance($b){
  $this->advance = $b;
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

public function getLabel(){
  if ($this->isContinuous()) return "\\delta";
  if ($this->advance) $out="d";
  else $out="i";
  if (1!=$this->m) $out.="^{(" . $this->m . ")}";
  return $out;
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
//  echo "set_m" . gettype($m);
  if (is_numeric($m)){
    if ($m > 0){
      $this->m = $m;
    }
    else{
      throw new Exception('attempt to set non-positive frequencyi ' . $m);
    }
  }
  else{
    throw new Exception('attempt to set non-numeric frequency ' . gettype($m));
  }
}

protected function getIEffective(){
  if ($this->isContinuous()) return exp( $this->i) - 1;
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
  $years = "year";
  $out = "For " . $this->getDescription() . " rate \$" . $this->getLabel() . " = ". $this->i . "\$, ";
  if ($this->isContinuous()){
    $out.= " after 1 year \$1\$ accumulates to \$\$ \exp\\left( " . $this->getLabel() . " \\right) = " . (1 + $this->getIEffective()) . ".\$\$";
    return $out;
  }
  if (1==$this->m) { $term = "1"; $im = $this->i;}
  else {$term = "\\frac{1}{" . $this->m . "}"; $im = "\\frac{" . $this->i . "}{" . $this->m . "}";}
  if (!$this->advance){
    $out.= "\$1\$ accumulates to \$1 + $im\$ after a term of \$$term\$ $years.";
    if (1!=$this->m) $out.="  So after 1 year, \$1\$ accumulates to \$\$ \\left( 1 + $im \\right)^{" . $this->m . "} = " . (1 + $this->getIEffective()) . ".\$\$"; 
//    else $out.="  So after 1 year, \$1\$ accumulates to \$\$ \\left( 1 + $im \\right) = " . (1 + $this->getIEffective()) . ".\$\$"; 
  }
  else{
    $out.= "\$1\$ payable after a term of \$$term\$ $years has present value \$1 - $im\$, so \$1\$ payable now will accumulate to \$ \\dfrac{1}{1 - $im} \$ after \$$term\$ $years.";
    if (1!=$this->m) $out.="  So after 1 year, \$1\$ accumulates to \$\$ \\left( \\dfrac{1}{1 - $im} \\right)^{" . $this->m . "} = " . (1 + $this->getIEffective()) . ".\$\$"; 
    else $out.="  So after 1 year, \$1\$ accumulates to \$\$ \\left( \\dfrac{1}{1 - $im} \\right) = " . (1 + $this->getIEffective()) . ".\$\$"; 
  }
  return $out;
}

public function getI(ct1_interest $t){
    if ($this->isValid()){ 
       if ($t->isContinuous()){
         $val = log(1 + $this->getIEffective());
       }
       else{
         if ($t->getAdvance()){
           $v_m = pow(1.0 + $this->getIEffective(),(-1/$t->m));
           $val = (1 - $v_m) * $t->m;
         }
         else{
           $ip1_m = pow(1.0 +$this->getIEffective(),(1/$t->m));
           $val = ($ip1_m - 1) * $t->m;
         }
       }
      return $val;
    }
    else{
      throw new Exception('attempt to get invalid interest rate');
    }
}

public function showI(ct1_interest $t){
    return array('value'=>$this->getI($t), 'logo'=>$t->getLabel(), 'explanation'=>$this->getILatex($t));
}

public function getILatex(ct1_interest $t){
       $out = "<p>First, get annual effective growth factor.</p>
              " . $this->getIEffectiveLatex() . "\r\n";
       $out.= "<p>Second, convert annual effective growth factor.</p>
              \\begin{align*}
       ";
       if ($t->isContinuous()){
         $out.= $t->getLabel() . " & = \\log(" . (1 + $this->getIEffective()) . ") \\\\
         ";
       }
       else{
         if (1!=$t->m){ $mtimes = $t->m . " \\times "; } 
         if ($t->advance){ 
           if (1==$t->m){ $minv = "-1"; } 
           else         { $minv = "\\frac{-1}{" . $t->m . "}"; } 
           $out.= $t->getLabel() . " & = " . $mtimes . " \\left(1 - " . (1 + $this->getIEffective()) . "^{ $minv } \\right) \\\\
         ";
         }
         else{
           if (1==$t->m){ $mpow = ""; } 
           else         { $mpow = "^{\\frac{1}{" . $t->m . "}}"; } 
           $out.= $t->getLabel() . " & = " . $mtimes . " \\left(" . (1 + $this->getIEffective()) . "$mpow - 1 \\right) \\\\
         ";
         }
       }
       $out.= "& = " . $this->getI($t) . ".
       ";
       $out.= "\\end{align*}
       ";
       return $out;
 }


} // end of class ct1_interest

// test
/*
$i = new ct1_interest();
echo "i4 " . print_r($i->getI(4),1) . "\r\n";
//$i->set_d('alpha');
echo "i365" . print_r($i->getI(365),1) . "\r\n";
echo "d365" . print_r($i->getD(365),1) . "\r\n";
echo "i1365" . print_r($i->getI(1365),1) . "\r\n";
echo "d1365" . print_r($i->getD(1365),1) . "\r\n";
$i->set_m(12);
$i->set_i(0.1);
echo "i1" . print_r($i->showI(1,0),1) . "\r\n";
echo "d1" . print_r($i->showI(1,1),1) . "\r\n";
echo "i4" . print_r($i->showI(4,0),1) . "\r\n";
echo "d2" . print_r($i->showI(2,1),1) . "\r\n";
*/
?>
