<?php

require_once 'class-ct1-mortgage.php';
require_once 'class-ct1-form.php';

class CT1_Concept_Mortgage extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Mortgage();
	parent::__construct($obj);
}

public function get_solution($_INPUT = array()){
	$this->set_received_input($_INPUT);
	$pre = $this->get_prefix();
	$m = new CT1_Mortgage( $_INPUT[$pre. 'frequency'], 
			$_INPUT[$pre. 'advance'],
			$_INPUT[$pre. 'delta'],
			$_INPUT[$pre. 'term'],
			$_INPUT[$pre. 'principal']);	
	if (isset($_INPUT[$pre . 'i_effective'])){
		 $m->set_i_effective($_INPUT[$pre . 'i_effective']);
	} 
	return "<p>Mortgage schedule is " . print_r($m->get_mortgage_schedule(),1) . ".</p>";
}
	
public function get_calculator($exclude = array()){
	$out = "<p>Calculate the amount of each level mortgage instalment.</p>" . "\r\n";
	$out.= parent::get_calculator($exclude, 'get_mortgage_instalment', 'Just show me the instalment amount');
	return $out;
}
} // end of class

/*
$obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
$form = new CT1_Concept_Mortgage($obj);
$html = $form->get_calculator(array("delta"));
echo $html;
*/
$IN = array('CT1_frequency'=>'12','CT1_i_effective'=>'0.06', 'CT1_advance'=>'on', 'CT1_principal'=>'1000000','CT1_term'=>10);
$obj = new CT1_Concept_Mortgage();
print_r($obj->get_solution($IN));
