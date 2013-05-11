<?php   

//require_once 'class-ct1-marker.php';
require_once 'class-ct1-interest-format.php';
//require_once 'interface-ct1-concept.php';

class CT1_Interest extends CT1_Interest_Format  {

protected $delta = 0;

public function get_valid_options(){ 
	$r = parent::get_valid_options();
	$r['delta'] = array(
						'type'=>'number',
						'decimal'=>'.',
					);
	$r['i_effective'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min'=>'-0.99',
					);
	return $r; 
}

public function get_parameters(){ 
	$r = parent::get_parameters();
	$r['delta'] = array(
			'name'=>'delta',
			'label'=>'Interest rate per year (continuously compounded)',
			);
	$r['i'] = array(
			'name'=>'i_effective',
			'label'=>'Interest rate per year (annual effective rate)',
			);
	return $r; 
}

protected function get_values(){ 
	$r = parent::get_values();
	$r['delta'] = $this->get_delta();
	return $r; 
}

public function __construct( $m = 1, $advance = false, $delta = 0){
	parent::__construct( $m, $advance);
	$this->set_delta($delta);
}

public function get_delta(){
	return $this->delta;
}

public function set_delta($d){
  $candidate = array('delta'=>$d);
  $valid = $this->get_validation($candidate);
	if ($valid['delta']) $this->delta = $d;
}

public function get_i_effective(){
	return exp($this->delta)-1;
}

public function set_i_effective($i){
  $candidate = array('i_effective'=>$i);
  $valid = $this->get_validation($candidate);
	if ($valid['i_effective']) $this->set_delta(log(1+$i));
}

public function get_rate_in_form($f){
	if ($f->is_continuous()) return $this->delta; 
	else{
		$i_m = $f->get_m() * (      exp($this->get_delta() /  $f->get_m()) - 1 );
		$d_m = $f->get_m() * ( 1 -  exp($this->get_delta() / -$f->get_m())     );
	}
	if ($f->get_advance())	return $d_m;
	else	return $i_m;
}

} // end of class CT1_Interest

