<?php
// load helper functions (autoloader)
require_once 'functions.php';
$path_to_class = dirname( __FILE__ ) . "/classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_class);

require_once 'class-ct1-spot-rates.php';
require_once 'class-ct1-forward-rate.php';
$sda = new CT1_Spot_Rate( 0.1, 3);
$sdb = new CT1_Spot_Rate( 0.05, 2);
//$sdc = new CT1_Forward_Rate( 0.02, 0, 1);
$sr = new CT1_Spot_Rates();
$sr->add_object( $sda );
$sr->add_object( $sdb );
//$sr->add_object( $sdc );
//print_r( "\r\n" . $sda->get_vn() . "\r\n" );
print_r( "\r\n" . $sr . "\r\n" );
//print_r( "\r\n" . print_r($sr->get_one_year_forward_deltas(), 1) . "\r\n" );
print_r( "\r\n" . print_r($sr->get_one_year_forward_rates(), 1) . "\r\n" );
//echo "<pre><" . "?" . "php print_r( $" . "_GET ) ?" . "></pre>";
?>
