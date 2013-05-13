<?php   
        /* 
        Plugin Name: FM16
        Plugin URI: http://bdmfst.com/ct1
        Description: Draft online quizes in financial mathematics for actuarial students
        Author: O.Kellie-Smith
        Version: 1.7 
        Author URI: http://www.bdmfst.com
        Licence: GPL2
*/  

require 'classes/class-ct1-concept-mortgage.php';
add_shortcode( 'concept_mortgage', 'concept_mortgage_proc' ); 
add_shortcode( 'annuityCertain', 'annuityCertain_proc' ); // DISPLAY

function annuityCertain_proc($attr){
  return "<p>annuityCertain in FM16 </p>  ";
}

function concept_mortgage_proc($attr){
	try{
		$m = new CT1_Concept_Mortgage();
		return $m->get_controller($_GET);
	}
	catch (Exception $e){
		return "Exception " . $e->getMessage();
	}
}
