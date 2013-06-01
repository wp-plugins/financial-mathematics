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

private function get_explanation_par( $_INPUT ){
	if ( isset($_INPUT['par_term'])  ){
		$pys = $this->obj->get_par_yields();
		// find par yield
		if ( $pys->get_count()  > 0 ) {
			foreach ( $pys->get_objects() as $p ){
				if ($p->get_term() == $_INPUT['par_term'] ){
					$render = new CT1_Render();
					return $render->get_render_latex( $this->obj->explain_par_yield( $p ) ) . $this->get_solution_no_detail();
				}
			}
		}
	}
	return $this->get_solution_no_detail();
}

private function get_explanation_forward( $_INPUT ){
	if ( isset($_INPUT['forward_start_time']) && isset( $_INPUT['forward_end_time'])  ){
		$frs = $this->obj->get_forward_rates();
		// find forward rate
		if ( $frs->get_count()  > 0 ) {
			foreach ( $frs->get_objects() as $f ){
				if ($f->get_start_time() == $_INPUT['forward_start_time'] && $f->get_end_time() ==  $_INPUT['forward_end_time']){
					$render = new CT1_Render();
					return $render->get_render_latex( $this->obj->explain_forward_rate( $f ) ) . $this->get_solution_no_detail();
				}
			}
		}
	}
	return $this->get_solution_no_detail();
}


public function get_solution_no_detail(){
//echo "<pre> get_solution_no_detail" . __FILE__ .  "</pre>";
	$render = new CT1_Render();
	$rates = $this->obj->get_all_rates();
	$hidden = $this->obj->get_values_as_array( get_class($this->obj) );
	$link = "?page_id=" . $_GET['page_id'] . $render->get_link($hidden);
//echo "<pre> get_solution_no_detail hidden " . print_r( $render->get_link($hidden), 1) .  "</pre>";
	for ( $i = 0, $ii = count( $rates['data'] ); $i < $ii; $i++ ){
		$f = $rates['objects'][$i]['CT1_Forward_Rate'];
		$flink = "";
		$p = $rates['objects'][$i]['CT1_Par_Yield'];
		$plink = "";
		if ( is_object( $f ) ){
			$rates['data'][$i][3]  = "<a href='" . $link . "&request=explain_forward&forward_start_time=" . $f->get_start_time() . "&forward_end_time=" . $f->get_end_time() . "'>" . $rates['data'][$i][3] . "</a>";
		}
		if ( is_object( $p ) ){
			$rates['data'][$i][5]  = "<a href='" . $link . "&request=explain_par&par_term=" . $p->get_term() . "'>" . $rates['data'][$i][5] . "</a>";
		}
	}
//echo "<pre> get_solution_no_detail rates " . print_r( $rates, 1) .  "</pre>";
	return $render->get_table( $rates['data'], $rates['header'] );
}

public function get_solution(){
	throw new Exception("get_solution called in " . __FILE__ );
}

public function get_delete_buttons(){
//echo "<pre> get_delete_buttons" . __FILE__ .  "</pre>";
	return parent::get_delete_buttons('view_spotrates');
}

private function add_spot_rate_from_input( $IN ){
//echo "<pre> add_spot_rate_from_input" . __FILE__ .  "</pre>";
	$i_effective = 0; $effective_time = 0;
	if ( isset( $IN['effective_time'] ) )
		$effective_time = (float)$IN['effective_time'];
	if ( isset( $IN['i_effective'] ) )
		$i_effective = (float)$IN['i_effective'];
	$sr = new CT1_Spot_Rate( $i_effective, $effective_time );
//echo "<pre> " . print_r( $this->obj, 1 ) .  "</pre>";
//echo "<pre> " . print_r( $sr, 1 ) .  "</pre>";
	$this->obj->add_object( $sr, false, true );
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
//	$form['introduction'] = 'Add a spot_rate.';
	$form['introduction'] = '';
	$form['submit'] = 'Add';
	$form['exclude'] = array();
	$form['values'] = $values;
	$form['hidden'] = $this->obj->get_values_as_array( get_class($this->obj) );
//echo "<pre>" . __FILE__ . print_r($form,1) . "</pre>";
/*
echo "<pre>" . __FILE__ . print_r($this->obj,1) . "</pre>";
echo "<pre> value" . __FILE__ . print_r($this->obj->get_values(),1) . "</pre>";
*/
	return $form;
}

public function get_possible_requests(){
	return array( 
		'explain_forward',
		'explain_par',
		'view_spotrates',
		'add_spot_rate',
		);
}


public function get_controller($_INPUT ){
//echo "<pre> GET" . __FILE__ . print_r($_GET,1) . "</pre>";
	try{
	$render = new CT1_Render();
	if (isset($_INPUT['request'])){
		if (isset($_INPUT[get_class( $this->obj )])){
			if ( $this->set_spotrates( $_INPUT[ get_class( $this->obj )] ) ) {
				;
			} else {
				return "<p>Error setting spotrates from:<pre>" . print_r($_INPUT,1) .  "</pre>";
			}
		}
		if ('add_spot_rate' == $_INPUT['request']){
			$this->add_spot_rate_from_input( $_INPUT );
		}
		if ('explain_forward' == $_INPUT['request'] ){
			$out = $this->get_explanation_forward( $_INPUT ) . $this->get_delete_buttons() . $this->get_form_add_spot_rate()  ;
		} else {
			if ( 'explain_par' == $_INPUT['request'] ){
				$out = $this->get_explanation_par( $_INPUT ) . $this->get_delete_buttons() . $this->get_form_add_spot_rate()  ;
			} else {
				$out = $this->get_solution_no_detail() . $this->get_delete_buttons() . $this->get_form_add_spot_rate()  ;
			}
		}
		return $out;
	}
	else{
		if (isset($_INPUT[get_class( $this->obj )])){
			if ( $this->set_spotrates( $_INPUT[ get_class( $this->obj )] ) ) {
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
	try{
		$this->obj->set_from_input($_INPUT);
		sort( $this->obj );
		return true;
	} catch( Exception $e ){
		return false;
	}
}

} // end of class

/*
// test case
$obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
$form = new CT1_Concept_Mortgage($obj);
print_r($form->get_calculator(array()));
*/

