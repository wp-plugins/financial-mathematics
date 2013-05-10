<?php   

require_once 'class-ct1-object.php';

class CT1_Interest_Format extends CT1_Object{

protected $m = 1;
protected $advance = false;

public function get_valid_options(){ 
	$r = parent::get_valid_options();
	$r['m'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min'=>0.00001,
					);
	return $r; 
}

protected function get_values(){ 
	$r = parent::get_values();
	$r['m'] = $this->get_m();
	$r['advance'] = $this->get_advance();
	return $r; 
}
		
public function __construct( $m=1, $advance=false ){
  $this->set_m($m);
  $this->set_advance($advance);
}

public function get_m(){
	return $this->m;
}

public function set_m($m){
  $candidate = array('m'=>$m);
  $valid = $this->get_validation($candidate);
	if ($valid['m']) $this->m = $m;
}

public function get_advance(){
	return $this->advance;
}
 
public function set_advance($b){
	if (is_bool($b)) $this->advance = $b;
}

public function equals($f){
	if(!($f instanceof CT1_Interest_Format))        return false;
	if( $f->get_m()       != $this->get_m()       ) return false;
	if( $f->get_advance() != $this->get_advance() ) return false;
	return true;
}

public function get_description(){
	if ($this->isContinuous()) return "interest rate continuously compounded";
	if ($this->advance) $out =  "discount rate";
	else $out = "interest rate";
	if (1!=$this->m) $out.=" convertible " . $this->m . " times per year";
	return $out;
}

public function get_label(){
	if ($this->isContinuous()) return "\\delta";
	if ($this->advance) $out="d";
	else $out="i";
	if (1!=$this->m) $out.="^{(" . $this->m . ")}";
	return $out;
}

protected function is_continuous(){
	$m_continuous = 366;
	if ($this->m > $m_continuous) return true;
	return false;
}

  
} // end of class


/*
$ir = new CT1_Interest_Format();
  $values = array('m'=>-1);
	print_r(Validate::multiple($values, $ir->valid_options));
*/
