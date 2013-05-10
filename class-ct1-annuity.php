<?php   
require_once 'class-ct1-interest.php';

class CT1_Annuity extends CT1_Interest{

protected $term;

public function __construct( $m = 1, $advance = false, $delta = 0, $term = 0 ){
	parent::__construct( $m, $advance, $delta);
	$this->set_term($term);
}

public function set_term($n){
	if (is_numeric($n) && $n>=0 ){
		$this->term = $n;
	}
}

public function get_term(){
	return $this->term;
}

public function get_annuity_certain_approx(){
	return $this->term / (1.0 + 0.5 * $this->term * (exp($this->delta)-1) );
}
 
public function get_annuity_certain(){
	if (0==$this->get_delta()) return $this->get_term();
	$vn = exp($this->delta * -$this->term);
	return (1 - $vn) / $this->get_rate_in_form($this);
}

}
