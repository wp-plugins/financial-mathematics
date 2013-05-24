<?php

require_once 'class-ct1-annuity-escalating.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

class CT1_Concept_Annuity extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Annuity_Escalating();
	parent::__construct($obj);
	$this->set_request( 'get_annuity_escalating' );
}

public function get_solution(){
	$render = new CT1_Render();
	$return = $render->get_render_latex($this->obj->explain_annuity_certain());
	return $return;
}
	
public function get_interest_rate(){
	$render = new CT1_Render();
	$return = $render->get_render_latex($this->obj->explain_interest_rate_for_value());
	return $return;
}

public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=> $this->get_request(), 'submit'=>'Calculate', 'introduction' => 'Calculate an annuity certain.  Enter enter a rate of return (to get the value) or enter a value (and get the rate of return).');
	$c = parent::get_calculator($p);
	$c['values']['value'] = NULL;
	return $c;
}

public function get_controller($_INPUT ){
	if (isset($_INPUT['request'])){
		if ($this->get_request() == $_INPUT['request']){
			if ($this->set_annuity($_INPUT)){
//				echo "<pre>" . print_r( $this->obj->get_values(), 1) . "</pre>";
				if (empty( $_INPUT['value'] ) ){
					return $this->get_solution();
				} else {
					return $this->get_interest_rate();
				}
			} else {
				return "<p>Error setting annuity from:<pre>" . print_r($_INPUT,1) .  "</pre>";
			}
		}
	}
	else{
		$render = new CT1_Render();
		return $render->get_render_form($this->get_calculator(array("delta", "escalation_delta")));
	}
}

public function set_annuity($_INPUT = array()){
	$this->set_received_input($_INPUT);
	$this->obj->set_from_input($_INPUT);
//				echo "<pre> INPUT" . print_r( $_INPUT, 1) . "</pre>";
//				echo "<pre> object" . print_r( $this->obj->get_values(), 1) . "</pre>";
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class

/*
// test case
$obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
$form = new CT1_Concept_Mortgage($obj);
print_r($form->get_calculator(array()));
*/

