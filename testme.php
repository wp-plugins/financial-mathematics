<html>
<?php
// load helper functions (autoloader)
require_once 'functions.php';
$path_to_class = dirname( __FILE__ ) . "/classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_class);

require_once 'class-ct1-concept-cashflows.php';
require_once 'class-ct1-cashflows.php';
// test case
$cfs = new CT1_Cashflows();
$cfa = new CT1_Cashflow( 100, 1 );
//$cfc = new CT1_Cashflow( 98765, 2 );
$a = new CT1_Annuity(1, true, 0.001, 1);
//$cfb = new CT1_Cashflow( 100, 3 );
//$b = new CT1_Annuity_Escalating(2, false, 0.123, 5);
//$cfb->set_annuity( $b );
//$c = new CT1_Annuity_Increasing(999, false, 0.0, 3, true);
$cfa->set_annuity( $a );
//$cfc->set_annuity( $c );
$cfs->add_cashflow( $cfa );
//$cfs->add_cashflow( $cfb );
//$cfs->add_cashflow( $cfc );
//$cfs->set_delta(0.1);
print_r( $cfs->get_delta_for_value( 90) );
/*
$con = new CT1_Concept_Cashflows();
$IN = array();
$IN['request'] = 'get_cashflows';
$IN['cashflows'] = $cfs->get_values();
*/
//print_r( $cfs );
//print_r( $IN );
//$control = $con->get_controller( $IN ) ;
//print_r ($l );
/*
include 'class-ct1-mortgage.php';
$c = new CT1_Mortgage(1, false, 0.1, 20, 100000);
$sol = $c->explain_instalment();
print_r($sol);
$l = $r->get_render_latex( $sol );
print_r ($l );
*/
echo "<pre><" . "?" . "php print_r( $" . "_GET ) ?" . "></pre>";
?>
</html>
