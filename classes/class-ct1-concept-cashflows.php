<?php

require_once 'class-ct1-cashflows.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

class CT1_Concept_Cashflows extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Cashflows();
	parent::__construct($obj);
	$this->set_request( 'get_cashflows' );
}

private function get_solution_no_detail(){
	$render = new CT1_Render();
	$return = $render->get_render_latex($this->obj->explain_discounted_value(false));
	return $return;
}

public function get_solution( $new_i_effective = 0 ){
	$this->obj->set_i_effective( $new_i_effective );	
	$render = new CT1_Render();
	$return = $render->get_render_latex($this->obj->explain_discounted_value());
	return $return;
}
	
private function get_delete_buttons(){
	$out = "";
	if ( count( $this->obj->get_cashflow_indices() ) > 0 ){
		$render = new CT1_Render();
		$cfs = $this->obj->get_cashflows();
		foreach ( $this->obj->get_cashflow_indices() as $i ) {
			$clone = $this->obj->get_clone_this();
			$cf = $cfs[$i];
			$rate = $cf->get_rate_per_year();
			$label = "\\begin{equation*}" . $cf->get_label() . "\\end{equation*}";
			$clone->remove_cashflow_index($i);
			$button = $render->get_form_cashflow( $clone, 'Delete ' . $rate );
			$out .= $label . $button;
		}
	}
	return $out;
}

private function get_interest_rate_for_value( $v = 0 ){
	$render = new CT1_Render();
//	$return = __FILE__ . "render->get_render_latex(this->obj->explain_interest_rate_for_value())";
	$return = $render->get_render_latex($this->obj->explain_interest_rate_for_value( $v ));
	return $return;
}

private function add_cashflow_from_input( $IN ){
	$rate_per_year = 0; $effective_time = 0;
	$m = 1; $advance = false; $term = 1; $i_effective = 0; $escalation_rate_effective = 0; $escalation_frequency = 1; $increasing = false; 
	if ( isset( $IN['rate_per_year'] ) )
		$rate_per_year = $IN['rate_per_year'];
	if ( isset( $IN['effective_time'] ) )
		$effective_time = $IN['effective_time'];
	if ( isset( $IN['m'] ) )
		$m = $IN['m'];
	if ( isset( $IN['advance'] ) )
		$advance = $IN['advance'];
	if ( isset( $IN['i_effective'] ) )
		$i_effective = $IN['i_effective'];
	if ( isset( $IN['term'] ) )
		$term = $IN['term'];
	if ( isset( $IN['escalation_rate_effective'] ) )
		$escalation_rate_effective = $IN['escalation_rate_effective'];
	if ( isset( $IN['escalation_frequency'] ) )
		$escalation_frequency = $IN['escalation_frequency'];
	if ( isset( $IN['increasing'] ) )
		$increasing = $IN['increasing'];
	if ( isset( $IN['single_payment'] ) ){
		$a = new CT1_Annuity(1, true, 0, 1);
		$a->set_i_effective( $i_effective );
	} else {
		if ( isset( $IN['consider_increasing'] ) ){
			$a = new CT1_Annuity_Increasing();
			$a->set_increasing( $increasing );
		} else {
			$a = new CT1_Annuity_Escalating();
			$a->set_escalation_rate_effective( $escalation_rate_effective );
			$a->set_escalation_frequency( $escalation_frequency );
		}
		$a->set_term( $term );
		$a->set_i_effective( $i_effective );
		$a->set_m( $m );
		$a->set_advance( $advance );
	}
	$cf = new CT1_Cashflow( $rate_per_year, $effective_time, $a );
//echo "<pre> new cf" . __FILE__ . print_r($cf,1) . "</pre>";
	$this->obj->add_cashflow( $cf );
	return;
}

private function get_form_add_cashflow(){
	$render = new CT1_Render();
	return $render->get_render_form( $this->get_add_cashflow() );
}
	

private function get_hidden_cashflow_fields( CT1_Cashflows $cf ){
	$hidden = array();
	if ( count( $cf->get_values() ) > 0 ) {
		$i = 0;
		foreach ($cf->get_values() as $v ){
			if ( is_array( $v ) ){
//echo "<pre> v in get_hidden_cashflow_fiels" . __FILE__ . print_r($v,1) . "</pre>";
				foreach (array_keys( $v ) as $key){
					$name = "cashflows[" . $i . "][" . $key . "]";
					$value = $v[ $key ];
					$hidden[ $name ] = $value;
				}
				$i++;
			}
		}
	}
	return $hidden;
	
}

public function get_add_cashflow(){
	$a_e = new CT1_Annuity_Escalating();
	$a_i = new CT1_Annuity_Increasing();
	$c_e = new CT1_Cashflow( 0, 0,  $a_e );
	$c_i = new CT1_Cashflow( 0, 0, $a_i );
//	$parameters = $c_e->get_parameters();
//	$valid_options = $c_e->get_valid_options();
	$parameters = array();
	$parameters['single_payment'] = array(
		'name'=> 'single_payment',
		'label' => 'Treat as single payment (ignore parameters apart from rate, effective time and interest)?',
		);
	$parameters_c = array_merge( $c_e->get_parameters(), $c_i->get_parameters() );
	$parameters = array_merge( $parameters, $parameters_c );
	$valid_options = array_merge( $c_e->get_valid_options(), $c_i->get_valid_options() );
	$valid_options['single_payment'] = array( 'type' => boolean );
	$valid_options['consider_increasing'] = array( 'type' => boolean );
	$parameters['consider_increasing'] = array(
		'name'=> 'consider_increasing',
		'label' => 'Treat as increasing / decreasing (stepped) annuity?',
		);
	foreach ( array('value','delta', 'escalation_delta') as $p ){
		unset( $parameters[ $p ] );
		unset( $valid_options[ $p ] );
	}
	$values = array_merge( $c_e->get_values(), $c_i->get_values() );
	$form = array();
	$form['method'] = 'GET';
	$form['parameters'] = $parameters;
	$form['valid_options'] = $valid_options;
	$form['request'] = 'add_cashflow';
	$form['render'] = 'HTML';
	$form['introduction'] = 'Add a cashflow.';
	$form['submit'] = 'Add';
	$form['exclude'] = array( "i_effective" );
	$form['values'] = $values;
	$form['hidden'] = $this->get_hidden_cashflow_fields( $this->obj );
//echo "<pre>" . __FILE__ . print_r($form,1) . "</pre>";
	return $form;
}

private function get_form_valuation(){
	$calc = $this->get_calculator( $unused );
	$render = new CT1_Render();
	return $render->get_render_form( $calc );
}


public function get_calculator($parameters){
	$parameters['i_effective'] = array(
		'name'=> 'i_effective',
		'label' => 'Effective annual rate of return',
		);
	$parameters['value'] = array(
		'name'=> 'value',
		'label' => 'Total present (discounted) value (leave blank if you want the value for a particular rate of return)',
		);
	$valid_options = array();
	$valid_options['i_effective'] = array(
					'type'=>'number',
					'decimal'=>'.',
					'min' => -0.99,
				);
	$valid_options['value'] = array(
					'type'=>'number',
					'decimal'=>'.',
				);
	$values = array();
	$form = array();
	$form['method'] = 'GET';
	$form['parameters'] = $parameters;
	$form['valid_options'] = $valid_options;
	$form['request'] = $this->get_request();
	$form['render'] = 'HTML';
	$form['introduction'] = 'Value cashflows.  Enter an effective annual rate of return (to get a present value) or a present value (to get an implicit rate of return, if one exists.)';
	$form['submit'] = 'Calculate';
	$form['exclude'] = array();
	$form['values'] = $values;
	$form['hidden'] = $this->get_hidden_cashflow_fields( $this->obj );
//echo "<pre>" . __FILE__ . print_r($form,1) . "</pre>";
	return $form;
}

public function get_request(){
	return "value_cashflows";
}

public function get_possible_requests(){
	return array( 
		'view_cashflows',
		'add_cashflow',
		$this->get_request(),
		);
}

private function ignore_value( $_INPUT ){
	if (!isset( $_INPUT['value'] ) )
		return true;
	if (!is_numeric( $_INPUT['value'] ) )
		return true;
	return false;
}


public function get_controller($_INPUT ){
//echo "<pre> GET" . __FILE__ . print_r($_GET,1) . "</pre>";
	$render = new CT1_Render();
	if (isset($_INPUT['request'])){
		if (isset($_INPUT['cashflows'])){
			if ( $this->set_cashflows( $_INPUT['cashflows'] ) ) {
				;
			} else {
				return "<p>Error setting cashflows from:<pre>" . print_r($_INPUT,1) .  "</pre>";
			}
		}
		if ('add_cashflow' == $_INPUT['request']){
			$this->add_cashflow_from_input( $_INPUT );
		}
		if ($this->get_request() == $_INPUT['request']){
			if ( $this->ignore_value( $_INPUT ) ){
				if (isset( $_INPUT['i_effective'] ) ){
					return $this->get_solution( $_INPUT['i_effective'] ) .  $this->get_form_valuation() . $this->get_delete_buttons() .  $this->get_form_add_cashflow()  ;
				} else {
					return $this->get_solution() .  $this->get_form_valuation() . $this->get_delete_buttons() .  $this->get_form_add_cashflow()  ;
				}
			} else {
				return $this->get_interest_rate_for_value( $_INPUT['value'] ) .  $this->get_form_valuation() . $this->get_delete_buttons() .  $this->get_form_add_cashflow()  ;
			}
		} else {
			$out = $this->get_solution_no_detail() .  $this->get_form_valuation() . $this->get_delete_buttons() . $this->get_form_add_cashflow()  ;
			return $out;
		}
	}
	else{
		if (isset($_INPUT['cashflows'])){
			if ( $this->set_cashflows( $_INPUT['cashflows'] ) ) {
				;
			} else {
				return "<p>Error setting cashflows from:<pre>" . print_r($_INPUT,1) .  "</pre>";
			}
			$hidden = $render->get_form_cashflow( $this->obj );
			$out = $this->get_solution_no_detail() .  $this->get_form_valuation() . $this->get_delete_buttons() .  $this->get_form_add_cashflow()  ;
			return $out;
		}
		return $this->get_form_add_cashflow()  ;
	}
}

private function set_cashflows( $_INPUT = array() ){
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

