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
	$r['value'] = $r['principal'];
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

public function __construct( $m = 1, $advance = false, $delta = 0, $term = 1, $principal = 0 ){
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

public function get_instalment($rounding = 2){
	return $this->instalment($rounding);
}


	public function explain_instalment($rounding = 2){
		$return = array();
		$return[0]['left'] = "\\mbox{Instalment amount }";
		$return[0]['right'] = "\\frac{ \\mbox{Principal}}{ " . $this->get_m() . "  " . $this->label_annuity() . "} ";
		$return[1]['right']['summary'] = "\\frac{ " . number_format( $this->get_principal(), $rounding )  . "}{" . $this->get_m() . " \\times " . $this->explain_format( $this->get_annuity_certain()) . "} ";
		$return[1]['right']['detail'] = $this->explain_annuity_certain();
		$return[2]['right'] = number_format( $this->get_instalment($rounding), $rounding);
		return $return;
	}

	public function get_value(){
		if ( isset( $this->value ) )
			return $this->value;
		else
			return round( $this->get_annuity_certain() * $this->instalment_per_year(), 2 );
	}


private function instalment_per_year(){
	if (0==$this->get_annuity_certain()) return NULL;
	return $this->get_principal() / $this->get_annuity_certain();
}

private function instalment($rounding = 2){
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

	public function get_label(){
		return $this->label_mortgage();
	}

	protected function label_mortgage(){
		return number_format($this->instalment_per_year()) . "\\ " . $this->label_annuity();
	}

	public function get_labels(){
		$labels = parent::get_labels();
		$labels['CT1_Mortgage'] = $this->label_mortgage();
		return $labels;
	}
}


// example
//$m = new CT1_Mortgage(4, true, 0.1, 10, 1000000);
//print_r($m->get_labels());
//print_r($m->explain_instalment());
