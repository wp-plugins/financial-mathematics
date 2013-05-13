<?php   
        /* 
        Plugin Name: Financial Mathematics
        Plugin URI: http://bdmfst.com/ct1
        Description: Provides online quizes in financial mathematics for actuarial students
        Author: O.Kellie-Smith
        Version: 1.7 
        Author URI: http://www.bdmfst.com
        Licence: GPL2
*/  

require 'classes/class-ct1-concept-mortgage.php';
add_shortcode( 'concept_mortgage', 'concept_mortgage_proc' ); // GET
function concept_mortgage_proc($attr){
	try{
		$m = new CT1_Concept_Mortgage();
		return $m->get_controller($_GET);
	}
	catch (Exception $e){
		return "Exception " . $e->getMessage();
	}
}



/*
session_start();
include 'ct1-admin-menu.php';
require_once 'class-ct1-convert.php';
require_once 'class-ct1-annuity.php';
require_once 'class-ct1-mortgage.php';
require_once 'class-ct1-format.php';
global $ct1_db_version;
$ct1_db_version = "1.1";
add_shortcode( 'annuityCertain', 'annuityCertain_proc' ); // DISPLAY
add_shortcode( 'mortgage', 'mortgage_proc' ); // DISPLAY
add_shortcode( 'convertInt', 'convert_proc' ); // DISPLAY
add_action('after_setup_theme', 'acceptPOST'); // $_POST
register_activation_hook( __FILE__, 'ct1_install' ); // SETUP


add_action('wp_head', 'callbackToAddCSS');
// source: http://stackoverflow.com/questions/5805888/how-can-i-add-meta-data-to-the-head-section-of-the-page-from-a-wordpress-plugin
function callbackToAddCSS(){
  echo "\t  <link rel='stylesheet' type='text/css' href='" . plugin_dir_url(__FILE__) ."ct1.css'> \n";
}


function annuityCertain_proc($attr){
  try{
   $pairs = array( 'question'=>1, 'answer' => 1);
   $a = shortcode_atts( $pairs, $attr  );
  $ac = new CT1_Annuity();
  return $ac->annuityCertain_func($a['question'], $a['answer']);
  }
  catch (Exception $e){
    return "Exception " . $e->getMessage();
  }
}

function mortgage_proc($attr){
  try{
  $m = new CT1_Mortgage();
  return $m->mortgage_func($attr);
  }
  catch (Exception $e){
    return "Exception " . $e->getMessage();
  }
}

function convert_proc($attr){
  try{
  $c = new CT1_Convert();
    return $c->convert_func($attr);
  }
  catch (Exception $e){
    return "Exception " . $e->getMessage();
  }
}

function acceptPOST(){
try{
  session_start();
//  $_SESSION['REQUEST'] = $_REQUEST;
  $_action = $_POST['ct1_action'];
  $_value = htmlentities($_POST['ct1_value']);
  $_term = $_POST['ct1_term'];
  $_frequency = $_POST['ct1_frequency'];
  $_interest = $_POST['ct1_interest'];
  $_advance = $_POST['ct1_advance'];
  $_principal = $_POST['ct1_principal'];
  if ( 'convert' == $_action || 'annuityCertain' == $_action || 'mortgage' == $_action){  
  if ( 'convert' == $_action ){  
    $source = new CT1_Interest();
    $target = new CT1_Interest();
    $_value = (float)$_REQUEST['ct1_value'];
    $source->set_m((float)$_REQUEST['ct1_frequency']);
    if ($_REQUEST['ct1_advance']) $source->set_d((float)$_REQUEST['ct1_interest']);
    else $source->set_i((float)$_REQUEST['ct1_interest']);
    $target->set_m((float)$_REQUEST['ct1_frequency_target']);
    if ($_REQUEST['ct1_advance_target']) $target->set_d(0);
    else $target->set_i(0);
    $marker = new CT1_Marker();
//
    $sol = $source->showI($target);
    $solution = $sol['value'];
    $_questionType = 3; // say for rate conversion
    $_scoreRes = $marker->score($solution, $_value);
//    echo "<pre> score res" . print_r($_scoreRes,1) . "</pre>";
//
    $redirect = current_page_url() . "&ct1_interest=" . $_interest;
    $redirect.= "&ct1_frequency=" . $_frequency . "&ct1_advance=" . $_advance ;
    $redirect.= "&ct1_frequency_target=" . $_POST['ct1_frequency_target'] . "&ct1_advance_target=" . $_POST['advance_target'] ;
    $redirect.= "&ct1_action=getConversion";
    $_SESSION['REQUEST'] = $_REQUEST;
  }
  if ( 'annuityCertain' == $_action || 'mortgage' == $_action){  
    $a = new ct1_annuity();
    $marker = new CT1_Marker();
    $_SESSION['REQUEST'] = $_REQUEST;
    $_ann = $a->annuityCertain($_term, $_interest, $_frequency, $_advance);
    $redirect = current_page_url() . "&ct1_term=" . $_term . "&ct1_interest=" . $_interest;
    $redirect.= "&ct1_frequency=" . $_frequency . "&ct1_advance=" . $_advance ;
    if ('annuityCertain' == $_action ){  
      $_questionType = 1; // say for level annuity certain
      $_scoreRes = $marker->score($_ann, $_value);
      $redirect.= "&ct1_action=getAnnuityCertain";
    }
    if ( 'mortgage' == $_action  ){  
      $_questionType = 2; // say for mortgage
      $_inst_per_year = $_principal / $_ann;
      $_inst = round($_inst_per_year / $_frequency, 2);
      $_scoreRes = $marker->score($_inst, $_value);
      $redirect.= "&ct1_principal=" . $_principal;
      $redirect.= "&ct1_action=getMortgage";
    }
  }
  if ( is_user_logged_in() ) { 
//    echo "<pre> score2 res" . print_r($_scoreRes,1) . "</pre>";
    $_score = $_scoreRes['credit'];
    $_available = $_scoreRes['available'];
    $marker->insert_mark( $_questionType, $_score, $_available);
  } 
//    $_SESSION['Redirect'] =  $redirect;
//echo "redirect" . $redirect;
  wp_redirect($redirect);
  exit;
  }
  }
  catch (Exception $e){
    echo "Exception " . $e->getMessage();
  }
}

function random_float ($min,$max) {
   // returns random number uniformly distributed between $min and $max
   return ($min+lcg_value()*(abs($max-$min)));
}
    

// INSTALLATION
// Source: http://codex.wordpress.org/Creating_Tables_with_Plugins .  Accessed 25-April-2013

function ct1_install() {
   global $wpdb;
   global $ct1_db_version;

   $table_name = $wpdb->prefix . "ct1";
      
   $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  userid bigint(20) NOT NULL,
  questionid mediumint(9) NOT NULL,
  credit mediumint(9) NOT NULL,
  available mediumint(9) NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "ct1_db_version", $ct1_db_version );
}

function ct1_insert_mark( $questionid, $credit, $available) {
   global $wpdb;
   $table_name = $wpdb->prefix . "ct1";

   global $current_user;
   $current_user = wp_get_current_user();
   $userID = $current_user->ID;
   $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'userid' => $userID, 'questionid' => $questionid, 'credit' => $credit, 'available' => $available ) );
}

//source: http://wordpress.org/support/topic/current-page-url-1
function current_page_url() {
	$pageURL = 'http';
	if( isset($_SERVER["HTTPS"]) ) {
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}
*/
