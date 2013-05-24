<?php

require_once 'class-ct1-concept-mortgage.php';
require_once 'class-ct1-concept-annuity.php';
require_once 'class-ct1-concept-annuity-increasing.php';
require_once 'class-ct1-concept-interest.php';

class CT1_Concept_All {

private $concepts;

public function __construct(CT1_Object $obj=null){
	$this->set_concepts();
}

private function set_concepts(){
	$this->concepts = array( 
				'concept_annuity'=>new CT1_Concept_Annuity(), 
				'concept_mortgage'=>new CT1_Concept_Mortgage(), 
				'concept_annuity_increasing'=>new CT1_Concept_Annuity_Increasing(), 
				'concept_interest'=>new CT1_Concept_Interest(),
				 );
}

private function get_concept_labels(){
	return array( 
				'concept_interest'=>'Interest rate format',
				'concept_annuity'=>'Annuity (escalating or level)', 
				'concept_mortgage'=>'Mortgage (level)', 
				'concept_annuity_increasing'=> 'Annuity (increasing or decreasing)', 
				 );
}


public function get_calculator( $unused ){
	$p = array('method'=> 'GET', 'submit'=>'Get calculator', 'introduction' => 'Select a calculator.');
	$p['select-options'] = $this->get_concept_labels() ;
	$p['select-name'] = 'concept';
	return $p;
}

	
public function get_controller($_INPUT ){
	try{

	if (isset($_INPUT['request'])){
		foreach( $this->concepts AS $c ){
			if ($c->get_request() == $_INPUT['request']){
				return $c->get_controller( $_INPUT );
			}
		}
	}
	if (isset($_INPUT['concept'])){
		if ( isset( $this->concepts[ $_INPUT['concept'] ] ) ){
			$c = $this->concepts[ $_INPUT['concept'] ];
			return $c->get_controller( $_INPUT );
		}
	}
	$render = new CT1_Render();
	return $render->get_select_form( $this->get_calculator( NULL ) );
	}
	catch( Exception $e ){
		return $e->getMessage();
	}
}

} // end of class

