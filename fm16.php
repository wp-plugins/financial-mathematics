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

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// plugin provides shortcodes (only)
add_shortcode( 'concept_mortgage', 'concept_mortgage_proc' ); 
add_shortcode( 'annuityCertain', 'concept_annuity_proc' ); 
add_shortcode( 'convertInt', 'concept_interest_proc' ); 
add_shortcode( 'annuity_increasing', 'concept_annuity_increasing_proc' ); 


// what shortcodes do
function concept_interest_proc($attr){
	try{
		CT1_autoloader('CT1_Concept_Interest', 'class-ct1-concept-interest.php');
		$m = new CT1_Concept_Interest();
		return $m->get_controller($_GET);
	}
	catch (Exception $e){
		return "Exception " . $e->getMessage();
	}
}

function concept_annuity_increasing_proc($attr){
	try{
		CT1_autoloader('CT1_Concept_Annuity_Increasing', 'class-ct1-concept-annuity-increasing.php');
		$m = new CT1_Concept_Annuity_Increasing();
		return $m->get_controller($_GET);
	}
	catch (Exception $e){
		return "Exception " . $e->getMessage();
	}
}

function concept_annuity_proc($attr){
	try{
		CT1_autoloader('CT1_Concept_Annuity', 'class-ct1-concept-annuity.php');
		$m = new CT1_Concept_Annuity();
		return $m->get_controller($_GET);
	}
	catch (Exception $e){
		return "Exception " . $e->getMessage();
	}
}

function concept_mortgage_proc($attr){
	try{
		CT1_autoloader('CT1_Concept_Mortgage', 'class-ct1-concept-mortgage.php');
		$m = new CT1_Concept_Mortgage();
		return $m->get_controller($_GET);
	}
	catch (Exception $e){
		return "Exception " . $e->getMessage();
	}
}

// load helper functions (autoloader)
require_once 'functions.php';
$path_to_class = dirname( __FILE__ ) . "/classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_class);

// load css for plugin
add_action('wp_head', 'callbackToAddCSS');
// source: http://stackoverflow.com/questions/5805888/
function callbackToAddCSS(){
  echo "\t  <link rel='stylesheet' type='text/css' href='" . plugin_dir_url(__FILE__) ."ct1.css'> \n";
}

