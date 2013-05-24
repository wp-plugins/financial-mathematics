<?php
// load helper functions (autoloader)
require_once 'functions.php';
$path_to_class = dirname( __FILE__ ) . "/classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_class);

include 'class-ct1-concept-all.php';
// test case
$form = new CT1_Concept_All();
print_r( $form->get_controller( array( 'page_id' => 999, 'concept'=>'concept_mortgage' ) ) );
