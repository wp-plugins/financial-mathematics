<?php

require_once 'class-ct1-annuity.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

class CT1_Concept_Annuity extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Annuity();
	parent::__construct($obj);
}

public function get_solution(){
	$render = new CT1_Render();
	$return = $render->get_render_latex($this->obj->explain_annuity_certain());
	return $return;
}
	
public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>'get_annuity', 'submit'=>'Just show me the annuity value', 'introduction' => 'Calculate the present value of an annuity certain.');
	return parent::get_calculator($p);
}

public function get_controller($_INPUT ){
	if (isset($_INPUT['request'])){
		if ('get_annuity' == $_INPUT['request']){
			if ($this->set_annuity($_INPUT))
				return $this->get_solution();
			else
				return "<p>Error setting annuity from:<pre>" . print_r($_INPUT,1) .  "</pre>";
		}
	}
	else{
		$render = new CT1_Render();
		return $render->get_render_form($this->get_calculator(array("delta")));
	}
}

public function set_annuity($_INPUT = array()){
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

