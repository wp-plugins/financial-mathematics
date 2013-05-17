<?php

require_once 'class-ct1-mortgage.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

class CT1_Concept_Mortgage extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Mortgage();
	parent::__construct($obj);
}

public function get_solution(){
	$return = $this->obj->get_mortgage_schedule();
	$return['introduction'] = "Mortgage schedule";
	return $return;
}
	
public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>'get_mortgage_instalment', 'submit'=>'Just show me the instalment amount', 'introduction' => 'Calculate the amount of each level mortgage instalment.');
	return parent::get_calculator($p);
}

public function get_controller($_INPUT ){
	if (isset($_INPUT['request'])){
		if ('get_mortgage_instalment' == $_INPUT['request']){
			if ($this->set_mortgage($_INPUT))
				return "<pre>" . print_r($this->get_solution(),1) .  "</pre>";
			else
				return "<p>Error setting mortgage from:<pre>" . print_r($_INPUT,1) .  "</pre>";
		}
	}
	else{
		$render = new CT1_Render();
		return $render->get_render_form($this->get_calculator(array("delta")));
	}
}

public function set_mortgage($_INPUT = array()){
	$this->set_received_input($_INPUT);
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class

/*
// test case
$obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
$form = new CT1_Concept_Mortgage($obj);
print_r($form->get_calculator(array()));
*/

