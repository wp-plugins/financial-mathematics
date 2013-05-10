<?php
//include_once "Validate/FR.php";
require_once "Validate.php";
/*
test(Validate::creditCard('6762195515061813'), true);
// 4
test(Validate::creditCard('6762195515061814'), false);
// 5
*/
/*
function rib($aCodeBanque, $aCodeGuichet='', $aNoCompte='', $aKey='')
function number($number, $decimal = null, $dec_prec = null, $min = null, $max = null)
*/
$values = array(
    'amountxxx'=> '999',
    'name'  => 'www.example.com',
    'mail'  => 'foo@example',
    );
$opts = array(
    'amountxxx'=> array('type'=>'number','decimal'=>',.','dec_prec'=>null,'min'=>1,'max'=>32000),
    'name'  => array('type'=>'email','check_domain'=>false),
    'mail'  => array('type'=>'email'),
    );

$result = Validate::multiple($values, $opts);
print_r($values);
print_r($opts);

print_r($result);

?>
