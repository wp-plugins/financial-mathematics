<?php   
require_once 'class-ct1-interest.php';

class CT1_Annuity extends CT1_Interest{

protected $term;

public function get_valid_options(){ 
	$r = parent::get_valid_options();
	$r['term'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min'=>0,
					);
	return $r; 
}

public function get_parameters(){ 
	$r = parent::get_parameters();
	$r['term'] = array(
			'name'=>'term',
			'label'=>'Term (years)',
			);
	return $r; 
}

public function get_values(){ 
	$r = parent::get_values();
	$r['term'] = $this->get_term();
	return $r; 
}

public function __construct( $m = 1, $advance = false, $delta = 0, $term = 0 ){
	parent::__construct( $m, $advance, $delta);
	$this->set_term($term);
}

public function set_term($n){
  $candidate = array('term'=>$n);
  $valid = $this->get_validation($candidate);
	if ($valid['term']) $this->term = $n;
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

public function set_from_input($_INPUT = array(), $pre = ''){
	try{
		if (parent::set_from_input($_INPUT, $pre)){
			$this->set_term(	$_INPUT[$pre. 'term'] );
			return true;
		}
		else{
			return false;
		}
	}
	catch( Exception $e ){ 
		return false; 
	}
}

}
