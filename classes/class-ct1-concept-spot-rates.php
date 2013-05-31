<?php

require_once 'class-ct1-spot-rates.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

class CT1_Concept_Spot_Rates extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Spot_Rates();
	parent::__construct($obj);
	$this->set_request( 'get_spotrates' );
}

public function get_solution_no_detail(){
//echo "<pre> get_solution_no_detail" . __FILE__ .  "</pre>";
	$render = new CT1_Render();
	$rates = $this->obj->get_all_rates();
//echo "<pre> get_solution_no_detail this obj " . print_r( $this->obj, 1) .  "</pre>";
//echo "<pre> get_solution_no_detail rates " . print_r( $rates, 1) .  "</pre>";
	return $render->get_table( $rates['data'], $rates['header'] );
}

public function get_solution(){
echo "<pre> get_solution" . __FILE__ .  "</pre>";
	return;
}

public function get_delete_buttons(){
echo "<pre> get_delete_buttons" . __FILE__ .  "</pre>";
	return;
}

private function add_spot_rate_from_input( $IN ){
//echo "<pre> add_spot_rate_from_input" . __FILE__ .  "</pre>";
	$i_effective = 0; $effective_time = 0;
	if ( isset( $IN['effective_time'] ) )
		$effective_time = (float)$IN['effective_time'];
	if ( isset( $IN['i_effective'] ) )
		$i_effective = (float)$IN['i_effective'];
	$sr = new CT1_Spot_Rate( $i_effective, $effective_time );
	$this->obj->add_object( $sr );
	return;
}

private function get_form_add_spot_rate(){
	$render = new CT1_Render();
//echo "<pre> get_form_add_spot_rate" . __FILE__ .  "</pre>";
	return $render->get_render_form( $this->get_add_spot_rate() );
}
	

public function get_add_spot_rate(){
	$sr = new CT1_Spot_Rate();
	$values = $sr->get_values();
	$form = array();
	$form['method'] = 'GET';
	$form['parameters'] = $sr->get_parameters();
	$form['valid_options'] = $sr->get_valid_options();
	$form['request'] = 'add_spot_rate';
	$form['render'] = 'HTML';
	$form['introduction'] = 'Add a spot_rate.';
	$form['submit'] = 'Add';
	$form['exclude'] = array();
	$form['values'] = $values;
//	$form['hidden'] = $this->get_hidden_spot_rate_fields( $this->obj );
//echo "<pre>" . __FILE__ . print_r($form,1) . "</pre>";
	return $form;
}

public function get_possible_requests(){
	return array( 
		'view_spotrates',
		'add_spot_rate',
		);
}


public function get_controller($_INPUT ){
echo "<pre> GET" . __FILE__ . print_r($_GET,1) . "</pre>";
	try{
	$render = new CT1_Render();
	if (isset($_INPUT['request'])){
		if (isset($_INPUT['spotrates'])){
			if ( $this->set_spotrates( $_INPUT['spotrates'] ) ) {
				;
			} else {
				return "<p>Error setting spotrates from:<pre>" . print_r($_INPUT,1) .  "</pre>";
			}
		}
		if ('add_spot_rate' == $_INPUT['request']){
			$this->add_spot_rate_from_input( $_INPUT );
		}
		if ($this->get_request() == $_INPUT['request']){
			if ( $this->ignore_value( $_INPUT ) ){
				if (isset( $_INPUT['i_effective'] ) ){
					return $this->get_solution( $_INPUT['i_effective'] ) . $this->get_delete_buttons() .  $this->get_form_add_spot_rate()  ;
				} else {
					return $this->get_solution()  . $this->get_delete_buttons() .  $this->get_form_add_spot_rate()  ;
				}
			} else {
				return $this->get_interest_rate_for_value( $_INPUT['value'] ) . $this->get_delete_buttons() .  $this->get_form_add_spot_rate()  ;
			}
		} else {
			$out = $this->get_solution_no_detail() . $this->get_delete_buttons() . $this->get_form_add_spot_rate()  ;
			return $out;
		}
	}
	else{
		if (isset($_INPUT['spotrates'])){
			if ( $this->set_spotrates( $_INPUT['spotrates'] ) ) {
				;
			} else {
				return "<p>Error setting spotrates from:<pre>" . print_r($_INPUT,1) .  "</pre>";
			}
			$hidden = $render->get_form_spot_rate( $this->obj );
			$out = $this->get_solution_no_detail()  . $this->get_delete_buttons() .  $this->get_form_add_spot_rate()  ;
			return $out;
		}
		return $this->get_form_add_spot_rate()  ;
	}
	} catch( Exception $e ){
		return "Exception in " . __FILE__ . print_r($e->getMessage(),1) ;
	}
}

private function set_spotrates( $_INPUT = array() ){
//	$this->obj->set_from_input($_INPUT);
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class

/*
// test case
$obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
$form = new CT1_Concept_Mortgage($obj);
print_r($form->get_calculator(array()));
*/

