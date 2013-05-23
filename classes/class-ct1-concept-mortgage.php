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
		$render = new CT1_Render();
	$return = $render->get_render_latex($this->obj->explain_instalment());
	return $return;
}

public function get_interest_rate(){
	$render = new CT1_Render();
	$return = $render->get_render_latex($this->obj->explain_interest_rate_for_instalment());
	return $return;
}

	
public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>'get_mortgage_instalment', 'submit'=>'Calculate', 'introduction' => 'Calculate  a level mortgage.  Either enter the interest rate (to calculate the amount of each instalment) or enter the instalment amount (to get the effective interest rate).');
	return parent::get_calculator($p);
}

public function get_controller($_INPUT ){
	if (isset($_INPUT['request'])){
		if ('get_mortgage_instalment' == $_INPUT['request']){
			if ($this->set_mortgage($_INPUT))
				if (empty( $_INPUT['instalment'] ) ){
					return $this->get_solution();
				} else {
					return $this->get_interest_rate();
				}
			else
				return "<p>Error setting mortgage from:<pre>" . print_r($_INPUT,1) .  "</pre>";
		}
	}
	else{
		$render = new CT1_Render();
		return $render->get_render_form($this->get_calculator(array("delta", "value")));
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

