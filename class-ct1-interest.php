<?php   

require_once 'class-ct1-marker.php';
require_once 'class-ct1-interest-format.php';
require_once 'interface-ct1-concept.php';

class CT1_Interest extends CT1_Interest_Format  {

protected $delta = 0;

public function __construct( $m = 1, $advance = false, $delta = 0){
	parent::__construct( $m, $advance);
	$this->set_delta($delta);
}

public function get_delta(){
	return $this->delta;
}

public function set_delta($d){
	if (is_numeric($d)) $this->delta = $d;
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

