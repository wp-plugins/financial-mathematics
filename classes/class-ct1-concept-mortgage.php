<?php

require_once 'class-ct1-mortgage.php';
require_once 'class-ct1-form.php';

class CT1_Concept_Mortgage extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Mortgage();
	parent::__construct($obj);
}

public function get_solution($unused_parameter=NULL){
	return "<p>Mortgage schedule is:</p><pre>" . print_r($this->obj->get_mortgage_schedule(),1) . "</pre></p>";
}
	
public function get_calculator($exclude = array()){
	$out = "<p>Calculate the amount of each level mortgage instalment.</p>" . "\r\n";
	$out.= parent::get_calculator($exclude, 'get_mortgage_instalment', 'Just show me the instalment amount');
	return $out;
}

public function get_controller($_INPUT = array()){
	if (isset($_INPUT[$this->get_prefix() . 'request'])){
		if ('get_mortgage_instalment' == $_INPUT[$this->get_prefix() . 'request']){
			$this->set_mortgage($_INPUT);
			return $this->get_solution();
		}
	}
	else{
		return $this->get_calculator(array("delta"));
	}
}

public function set_mortgage($_INPUT = array()){
	$this->set_received_input($_INPUT);
	$pre = $this->get_prefix();
	return ($this->obj->set_from_input($_INPUT, $pre));
}

} // end of class

$obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
$form = new CT1_Concept_Mortgage($obj);
$html = $form->get_calculator();
echo $html;
