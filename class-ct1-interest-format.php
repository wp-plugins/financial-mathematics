<?php   

class CT1_Interest_Format {

protected $m = 1;
protected $advance = false;

public function __construct( $m=1, $advance=false ){
  $this->set_m($m);
  $this->set_advance($advance);
}

public function get_m(){
	return $this->m;
}

public function set_m($m){
	if (is_numeric($m) && $m > 0) $this->m = $m;
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
