<?php   
require_once 'ct1_interest.php';
require_once 'ct1_marker.php';

class ct1_convert{

private function problem(ct1_interest $source, ct1_interest $target){
  $out = "the amount of the annual " . $target->getDescription() . " rate " . $target->getFrequency();
  $out.= " which is equivalent to an annual " . $source->getDescription() . " rate of " . $source->getRate() . $source->getFrequency();
  return $out;
}

private function form(ct1_interest $source, ct1_interest $target){
  $out = "<form class='ct1_form' method = 'POST'>
          <p>Calculate " . $this->problem( $source,  $target) . "</p>
          ";
  $out.= "<p>
            <label>Interest rate
              <input name = 'ct1_value'>
            </label>
          </p>
          ";
  $out.= $this->formBottom( $source,  $target, 'convert', 'Check my value');
  return $out;
}


function formBottom(ct1_interest $source, ct1_interest $target, $_action, $_submit){
  $s = $source->getAll();
  $t = $target->getAll();
  $out = "<input type = 'hidden' name='ct1_frequency' value='" . $s['m'] . "'>
          <input type = 'hidden' name='ct1_advance' value='" . $s['adv'] . "'>
          <input type = 'hidden' name='page_id' value='" . $_REQUEST['page_id'] . "'>
          <input type = 'hidden' name='ct1_interest' value='" . $s['i'] . "'>
          <input type = 'hidden' name='ct1_frequency_target' value='" . $t['m'] . "'>
          <input type = 'hidden' name='ct1_advance_target' value='" . $t['adv'] . "'>
          <input type = 'hidden' name='ct1_action' value='$_action'>
          <input type = 'submit' value = '$_submit'>
          </form> 
          ";
  return $out;
}


function formGetConversion(ct1_interest $source, ct1_interest $target){
  $out = "<form class='ct1_form'  method = 'GET'>
         ";
  $out.= "<p>Calculate " . $this->problem( $source,  $target) . "</p>
         ";
  $out.= $this->formBottom( $source,  $target, 'getConversion', 'Just show me the rate');
  return $out;
}


function random_float ($min,$max) {
   // returns random number uniformly distributed between $min and $max
   return ($min+lcg_value()*(abs($max-$min)));
}
    

public function convert_func( $atts ){
  $source = new ct1_interest();
  $target = new ct1_interest();
//  if ('convert'==$_SESSION['REQUEST']['ct1_action'] && 'getConversion'!=$_REQUEST['ct1_action']){
  if ('convert'==$_SESSION['REQUEST']['ct1_action'] ){
    $_REQUEST = $_SESSION['REQUEST'];
    $_action = 'convert';
    $_SESSION['REQUEST']['ct1_action']='';
  }
  if ('getConversion'==$_REQUEST['ct1_action'] && 'convert'!=$_action){ $_action='getConversion'; }
  if ('convert'==$_action || 'getConversion'==$_action){
    $_value = (float)$_REQUEST['ct1_value'];
    $source->set_m((float)$_REQUEST['ct1_frequency']);
    if ($_REQUEST['ct1_advance']) $source->set_d((float)$_REQUEST['ct1_interest']);
    else $source->set_i((float)$_REQUEST['ct1_interest']);
    $target->set_m((float)$_REQUEST['ct1_frequency_target']);
    if ($_REQUEST['ct1_advance_target']) $target->set_d(0);
    else $target->set_i(0);
    $sol = $source->showI($target);
    $solution = $sol['value'];
    if ('convert' == $_action){  
      $out = "<p>Problem was to calculate " . $this->problem($source, $target) . "</p>";
      $out .= "<p>You say rate  = $_value.</p>";
      $out .= "<p>I say rate = $solution.</p>";
      $marker = new ct1_marker();
      $_scoreRes = $marker->score($solution, $_value);
      $out.= $marker->yourscore($_scoreRes['credit'], $_scoreRes['available']);
      $out.= $sol['explanation'];
    }
    elseif ('getConversion' ==$_action ){  
      $out = "<p>To calculate " . $this->problem($source, $target) . "</p>";
      $out .= "<p>Rate = $solution.</p>";
      $out.= $sol['explanation'];
    }
  }
  else {
	// NEW QUESTION
     $_interest = round($this->random_float(0.01, 0.10),3);  
     $af = array(1,2,4,12,999);
     $source->set_m((int)$af[rand(0,4)]);
     if (rand(0,1)==0) $source->set_d($_interest );
     else $source->set_i($_interest );
     $target->set_m((int)$af[rand(0,4)]);
     if (rand(0,1)==0) $target->set_d(0);
     else $target->set_i(0);
     $out = "";
     $pairs = array( 'question'=>1, 'answer' => 1);
//    $a = shortcode_atts( $pairs, $attr  );
     $a = $pairs;
     if ($source->sameForm($target)) $target->setAdvance(!($target->getAdvance));
     if ($a['question']) $out.= $this->form($source, $target);
     if ($a['answer']) $out.= "<hr/><p>" . $this->formGetConversion($source, $target) . "</p>";
   }
   return $out;
}


} // end ct1_convert

//$c = new ct1_convert();
/*
$s = new ct1_interest();
$d = new ct1_interest();
$s->set_i(0.0512);
$s->set_m(2);
$d->set_d(0);
$d->set_m(999);
//echo $c->problem($s, $d) . "\r\n";
//echo $c->form($s, $d) . "\r\n";
*/
//echo $c->convert_func(array());
?>
