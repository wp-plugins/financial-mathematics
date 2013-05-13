<?php

require_once 'class-ct1-mortgage.php';
require_once 'class-ct1-form.php';

class CT1_Concept_Mortgage extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Mortgage();
	parent::__construct($obj);
}

public function get_solution($unused_parameter=NULL){
	return "<p>Mortgage schedule is " . print_r($this->obj->get_mortgage_schedule(),1) . ".</p>";
}
	
public function get_calculator($exclude = array()){
	$out = "<p>Calculate the amount of each level mortgage instalment.</p>" . "\r\n";
	$out.= parent::get_calculator($exclude, 'get_mortgage_instalment', 'Just show me the instalment amount');
	return $out;
}

public function set_mortgage($_INPUT = array()){
	$this->set_received_input($_INPUT);
	$pre = $this->get_prefix();
	return ($this->obj->set_from_input($_INPUT, $pre));
}

} // end of class

$IN = array('CT1_m'=>'12','CT1_i_effective'=>'0.06', 'CT1_advance'=>'on', 'CT1_principal'=>'1000000','CT1_term'=>10);
//$IN = array();
$concept = new CT1_Concept_Mortgage();
$concept->set_mortgage($IN);
$html = $concept->get_calculator(array("delta"));
echo $html;
print_r($concept->get_solution());
