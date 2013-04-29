<?php   
require_once 'ct1_format.php';
require_once 'ct1_marker.php';

class ct1_annuity{

function annuityDecimalPLaces(){
    return 5; // HARDCODE
}

function im($_i, $_frequency){ 
  return $_frequency * ( pow(1 + $_i,(1.0 / $_frequency)) - 1);
}


function acceptPOST(){
  session_start();
  $_action = $_POST['ct1_action'];
  $_value = htmlentities($_POST['ct1_value']);
  $_term = $_POST['ct1_term'];
  $_frequency = $_POST['ct1_frequency'];
  $_interest = $_POST['ct1_interest'];
  $_advance = $_POST['ct1_advance'];
  $_principal = $_POST['ct1_principal'];
  if ($_action == 'annuityCertain' || $_action == 'mortgage'){  
    $marker = new ct1_marker();
    $_SESSION['REQUEST'] = $_REQUEST;
    $_ann = annuityCertain($_term, $_interest, $_frequency, $_advance);
    $redirect = current_page_url() . "&ct1_term=" . $_term . "&ct1_interest=" . $_interest;
    $redirect.= "&ct1_frequency=" . $_frequency . "&ct1_advance=" . $_advance ;
    if ($_action == 'annuityCertain' ){  
      $_questionType = 1; // say for level annuity certain
      $_scoreRes = $marker->score($_ann, $_value);
      $redirect.= "&ct1_action=getAnnuityCertain";
    }
    if ($_action == 'mortgage' ){  
      $_questionType = 2; // say for mortgage
      $_inst_per_year = $_principal / $_ann;
      $_inst = round($_inst_per_year / $_frequency, 2);
      $_scoreRes = $marker->score($_inst, $_value);
      $redirect.= "&ct1_principal=" . $_principal;
      $redirect.= "&ct1_action=getMortgage";
    }
    if ( is_user_logged_in() ) { 
      $_score = $_scoreRes['credit'];
      $_available = $_scoreRes['available'];
      ct1_insert_mark( $_questionType, $_score, $_available);
    } 
//    $_SESSION['Redirect'] =  $redirect;
    wp_redirect($redirect);
    exit;
  }
}



function problem($_term, $_interest, $_frequency, $_advance){
  $adv = "in arrears";
  $inst = "instalments";
  if ($_frequency ==1) $inst = "instalment";
  if ($_advance) $adv = "in advance";
  $out = "present value of annuity paying 1 per year, ";
  if ($_frequency=='continuous'){
    $out.= "paid continuously for a total term of $_term years ";
  }
  else{
    $out.= "paid in $_frequency $inst per year $adv for a total term of $_term years ";
  }
  $out.= "at effective rate of interest " . 100* $_interest . "%  per year.";
  return $out;
}

function form($_term, $_interest, $_frequency, $_advance){
  $out = "<form method = 'POST'>
          <p>Calculate the " . $this->problem($_term, $_interest, $_frequency, $_advance) . "</p>";
  $out.= $this->formBottom($_term, $_interest, $_frequency, $_advance, 'annuityCertain');
  return $out;
}



function formBottom($_term, $_interest, $_frequency, $_advance, $_action){
  $out = "<p>
            <label>Value
              <input name = 'ct1_value'>
            </label>
          </p>
          <input type = 'hidden' name='ct1_term' value='$_term'>
          <input type = 'hidden' name='ct1_frequency' value='$_frequency'>
          <input type = 'hidden' name='ct1_advance' value='$_advance'>
          <input type = 'hidden' name='ct1_interest' value='$_interest'>
          <input type = 'hidden' name='ct1_action' value='$_action'>
          <input type = 'submit' value = 'submit'>
          </form> ";
  return $out;
}



function annuityCertainApprox($_term, $_interest){
  return round($_term / (1.0 + 0.5 * $_term * $_interest),1);
}
 

function annuityCertain($_term, $_interest, $_frequency, $_advance){
  $_i = $_interest;

  // calculate annuity
  $_vn = pow((1 + $_i),-$_term);
  if ($_i ==0){
    $_ann = $_term;
  }
  elseif ($_frequency == 'continuous') {
    $_ann = (1 - $_vn) / log(1+$_i);
  }
  elseif ($_frequency == 1) {
    $_ann = (1 - $_vn) / $_i;
  }
  else{
    $_im = $_frequency * ( pow(1 + $_i,(1.0 / $_frequency)) - 1);
    $_ann = (1 - $_vn) / $_im;
  }
  if ($_advance){
    $_ann = $_ann * pow(1 + $_i,(1.0 / $_frequency)) ;
  }
  $_ann = round($_ann, $this->annuityDecimalPLaces());
  return $_ann;
}




function latexAnnuity($_term, $_i, $_frequency, $_advance){
   $a = "a";
   if ($_frequency == 'continuous') $a = "\\overline{a}";
   else{
     if ($_advance) $a = "\\ddot{a}";
     if ($_frequency <> 1){
       $f = "^{(" . $_frequency . ")}";
     }
   }
   $term = "_{\\overline{" . $_term . "|}}";
   return $a . $f . $term;
}



function latexApprox($_term, $_i){
  if ($_i != 0) {
    $out = "<p>";
    $out.= "$$\\begin{align*} " . $this->latexAnnuity($_term, $_i, 'continuous', false);
    $out.= " & \\approx  \dfrac{n}{1 + i \  n/2} \\\\ & = ";
    $out.= " \dfrac{ " .  $_term . "}{1 + " . $_i . " \\times " . $_term . " / 2}  \\\\ &  = ";
    $out.= $this->annuityCertainApprox($_term, $_i) . ". \\end{align*}$$";
  }
  return $out;
}



function latex($_term, $_i, $_frequency, $_advance, $_ann){
  if ($_i == 0) {
    $out = "<p>$$" . $this->latexAnnuity($_term, $_i, $_frequency, $_advance) . " =  n = " . $_ann . ". $$</p>";
  }
  elseif ($_frequency == 'continuous') {
    $out.= "\\begin{align*} " . $this->latexAnnuity($_term, $_i, $_frequency, $_advance) . " & = \dfrac{1 - v^n}{\\delta} \\\\ ";
    $out.= "& = ";
    $out.= "\dfrac{1 - " . (1 + $_i) . "^{ - " . $_term . "}}{ \\ln(1 + " . $_i . ")}  \\\\";
    $out.= " &  = ";
    $out.= $_ann . ". \\end{align*}";
    $out.= "</p>";
  }
  elseif ($_frequency == 1 ) {
    $out = "<p>";
    if ($_advance){
      $_advL = "\\left( 1 + i \\right)";
      $_advN = 1 + $_i . " \\times ";
    }
    $out.= "\\begin{align*} " . $this->latexAnnuity($_term, $_i, $_frequency, $_advance) . " & = $_advL \dfrac{1 - v^n}{i} \\\\";
    $out.= " & = ";
    $out.= "$_advN \dfrac{1 - " . (1 + $_i) . "^{ - " . $_term . "}}{" . $_i . "}  \\\\";
    $out.= " &  = ";
    $out.= $_ann . ". \\end{align*}";
    $out.= "</p>";
  }
  else{
    $out = "<p>";
    if ($_advance){
      $_advL = "\\left( 1 + i \\right)^{\\frac{1}{{$_frequency}}}";
      $_advN = 1 + $_i  . "^{\\frac{1}{{$_frequency}}} \\times ";
    }
    $out.= "\\begin{align*} " . $this->latexAnnuity($_term, $_i, $_frequency, $_advance) . " & = $_advL \\frac{1 - v^n}{i^{(" . $_frequency . ")}}  \\\\";
    $out.= " & =  ";
    $out.= "$_advN \\frac{1}{" . $_frequency . "} \\times \\frac{1 - " . (1 + $_i) . "^{ - " . $_term . "}}{ " . (1 + $_i) . "^{\\frac{1}{" . $_frequency . "}} - 1}  \\\\";
    $out.= " & = " . $_ann . ". \\end{align*}";
    $out.= "</p>";
  }
  return $out;
}

function random_float ($min,$max) {
   // returns random number uniformly distributed between $min and $max
   return ($min+lcg_value()*(abs($max-$min)));
}
    


public function annuityCertain_func( $atts ){
//  echo "<pre>SESSION" . print_r($_SESSION,1) . "</pre>";
  if ($_SESSION['REQUEST']['ct1_action']=='annuityCertain'){
      $_REQUEST = $_SESSION['REQUEST'];
      $_SESSION['REQUEST']['ct1_action']='';
  }
  $_action = $_REQUEST['ct1_action'];
  $_value = htmlentities($_REQUEST['ct1_value']);
  $_term = $_REQUEST['ct1_term'];
  $_frequency = $_REQUEST['ct1_frequency'];
  $_interest = $_REQUEST['ct1_interest'];
  $_advance = $_REQUEST['ct1_advance'];

//  $_i = (1 + $_interest)/(1 + $_escalation) - 1;
  $_ann = $this->annuityCertain($_term, $_interest, $_frequency, $_advance);

if ($_action == 'annuityCertain'){  
    $out = "<p>Problem was to calculate the " . $this->problem($_term, $_interest, $_frequency, $_advance) . "</p>";
    $out .= "<p>You say present value  = $_value.</p>";
    $out .= "<p>I say present value = $_ann.</p>";
    $out .= $this->latex($_term, $_interest, $_frequency, $_advance, $_ann);
    if ($_interest !=0){
      $out .= "<p>Approximation: </p>" . $this->latexApprox($_term, $_interest);
    }
    $marker = new ct1_marker();
    $_scoreRes = $marker->score($_ann, $_value);
    $out.= $marker->yourscore($_scoreRes['credit'], $_scoreRes['available']);
  }
  elseif ($_action == 'getAnnuityCertain'){  
    $_questionType = 1; // say for level annuity certain
    $out = "<p>To calculate the " . $this->problem($_term, $_interest, $_frequency, $_advance) . "</p>";
    $out .= "<p>Present value = $_ann.</p>";
    $out .= $this->latex($_term, $_interest, $_frequency, $_advance, $_ann);
    if ($_interest !=0){
      $out .= "<p>Approximation: </p>" . $this->latexApprox($_term, $_interest);
    }
  }
  else {
	// NEW QUESTRION
    $_term = round(random_float(1,20),0);
    $_interest = round(random_float(0.01, 0.10),3);
    $af = array(1,2,4,12,'continuous');
    $_frequency = $af[rand(0,3)];
    $_advance = rand(0,1)==0;  
    $out = $this->form($_term, $_interest, $_frequency, $_advance);
  }
  return $out;
}


}
?>
