<?php   

require_once 'class-ct1-annuity.php';

class CT1_Mortgage extends CT1_Annuity{

protected $principal;

public function get_valid_options(){ 
	$r = parent::get_valid_options();
	$r['principal'] = array(
						'type'=>'number',
						'decimal'=>'.',
					);
	return $r; 
}

public function get_parameters(){ 
	$r = parent::get_parameters();
	$r['principal'] = array(
			'name'=>'principal',
			'label'=>'Principal',
			);
	return $r; 
}

public function get_values(){ 
	$r = parent::get_values();
	$r['principal'] = $this->get_principal();
	return $r; 
}

public function __construct( $m = 1, $advance = false, $delta = 0, $term = 0, $principal = 0 ){
	parent::__construct( $m, $advance, $delta, $term);
	$this->set_principal($principal);
}

public function set_principal($p){
  $candidate = array('principal'=>$p);
  $valid = $this->get_validation($candidate);
	if ($valid['principal']) $this->principal = $p;
}

public function get_principal(){
	return $this->principal;
}

private function instalment_per_year(){
	if (0==$this->get_annuity_certain()) return NULL;
	return $this->get_principal() / $this->get_annuity_certain();
}

private function instalment($rounding){
	if ($this->is_continuous()) throw new Exception("Can't get instalments for continuously paid mortgage");
	return round($this->instalment_per_year() / $this->get_m(), $rounding);
}

private function interest_per_period(){
 	return exp($this->get_delta() / $this->get_m()) - 1;
}

public function get_mortgage_schedule(){
	if ($this->is_continuous()) throw new Exception("Can't get mortgage schedule for continuously paid mortgage");
	$rounding = 2;
 	$_principal = $this->get_principal();
	$_inst = $this->instalment($rounding);
	for ($i = 1, $ii = $this->get_m() * $this->get_term(); $i <= $ii; $i++){
		$oldPrincipal = $_principal;
		if ($this->get_advance()) $_principal = $_principal - $_inst;
		$int = $this->interest_per_period() * $_principal;
		if (!$this->get_advance()) $_principal = $_principal - $_inst;
		$_principal = $_principal + $int;
		$capRepay = $oldPrincipal - $_principal;
		$schedule[$i] = array(	'count' =>$i, 
														'oldPrincipal'=>$oldPrincipal, 
														'interest'=>$int, 
														'capRepay'=>$capRepay, 
														'newPrincipal' => $_principal, 
														'instalment'=>$_inst,
														);
    }
  return $schedule;
}

public function set_from_input($_INPUT = array(), $pre = ''){
	try{
		if (parent::set_from_input($_INPUT, $pre)){
			$this->set_principal( $_INPUT[$pre. 'principal']);	
			return true;
		}
		else return false;
	}
	catch( Exception $e ){ 
		return false; 
	}
}

}


