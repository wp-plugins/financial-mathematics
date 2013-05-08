<?php   

require_once 'class-ct1-annuity.php';
require_once 'class-ct1-format.php';

class CT1_Mortgage{

function im($_i, $_frequency){ 
  return $_frequency * ( pow(1 + $_i,(1.0 / $_frequency)) - 1);
}

function problemMortgage($_term, $_interest, $_frequency, $_advance, $_principal){
  setlocale(LC_MONETARY, CT1_Format::locale()); // HARDCODE!
  $adv = "in arrears";
  $inst = "instalments";
  if ($_frequency ==1) $inst = "instalment";
  if ($_advance) $adv = "in advance";
  $out = "amount of each mortgage instalment payment for a repayment mortgage ";
  $out.= "on a loan of " . CT1_Format::mycurrency($_principal) . " "  ;
  if ($_frequency=='continuous'){
    $out.= "repaid continuously for a total term of $_term years ";
  }
  else{
    $out.= "repaid in $_frequency $inst per year $adv for a total term of $_term years ";
  }
  $out.= "at effective rate of interest " . 100* $_interest . "%  per year.";
  return $out;
}


function formMortgage($_term, $_interest, $_frequency, $_advance, $_principal){
  $out = "<form class='ct1_form' method = 'POST'>";
  $out.= "<input type = 'hidden' name='ct1_principal' value='$_principal'>";
  $out.= "<p>Calculate the " . $this->problemMortgage($_term, $_interest, $_frequency, $_advance, $_principal) . "</p>";
  $out.= $this->formBottom($_term, $_interest, $_frequency, $_advance, 'mortgage');
  return $out;
}

function formMortgageGet($_term, $_interest, $_frequency, $_advance, $_principal){
  $out = "<form class='ct1_form' method = 'POST'>";
  $out.= "<p><label>Principal<input type = 'text' name='ct1_principal' value='$_principal'></label>";
  $out.= $this->formBottomGet($_term, $_interest, $_frequency, $_advance, 'getMortgage');
  return $out;
}

function formBottomGet($_term, $_interest, $_frequency, $_advance, $_action){
  if ($_advance) $_checked = " CHECKED ";
  $out = "
   <p><label>Term<input type = 'text' name='ct1_term' value='$_term'></label</p>
   <p><label>Frequency <input type = 'text' name='ct1_frequency' value='$_frequency'></label></p>
   <p><label>In advance <input type = 'checkbox' " . $_checked . " name='ct1_advance' value='$_advance'></label></p>
   <p><label>Interest rate pa <input type = 'text' name='ct1_interest' value='$_interest'></label></p>
          <input type = 'hidden' name='ct1_action' value='$_action'>
          <input type = 'submit' value = 'Just tell me the instalment amount'>
          </form> ";
  return $out;
}


function formBottom($_term, $_interest, $_frequency, $_advance, $_action){
  $out = "<p>
            <label>Instalment amount
              <input name = 'ct1_value'>
            </label>
          </p>
          <input type = 'hidden' name='ct1_term' value='$_term'>
          <input type = 'hidden' name='ct1_frequency' value='$_frequency'>
          <input type = 'hidden' name='ct1_advance' value='$_advance'>
          <input type = 'hidden' name='ct1_interest' value='$_interest'>
          <input type = 'hidden' name='ct1_action' value='$_action'>
          <input type = 'submit' value = 'Check my instalment amount'>
          </form> ";
  return $out;
}

function mortgageSchedule($_term, $_interest, $_frequency, $_advance, $_principal){
    $a = new CT1_Annuity();
    $_ann = $a->annuityCertain($_term, $_interest, $_frequency, $_advance);
    $_inst_per_year = $_principal / $_ann;
    $_inst = round($_inst_per_year / $_frequency, 2);
    $intPerPeriod  = $a->im($_interest, $_frequency) / $_frequency;
    for ($i = 1, $ii = $_frequency * $_term; $i <= $ii; $i++){
        $oldPrincipal = $_principal;
        if ($_advance) $_principal = $_principal - $_inst;
        $int = $intPerPeriod * $_principal;
        if (!$_advance) $_principal = $_principal - $_inst;
        $_principal = $_principal + $int;
        $capRepay = $oldPrincipal - $_principal;
	$schedule[$i] = array('count' =>$i, 'oldPrincipal'=>$oldPrincipal, 'interest'=>$int, 'capRepay'=>$capRepay, 'newPrincipal' => $_principal, 'instalment'=>$_inst);
    }
  return $schedule;
}


function getMortgageSchedule($_term, $_interest, $_frequency, $_advance, $_principal){
   $out = "";
    $a = new CT1_Annuity();
     if (1==$_frequency) {
        $termj = $_term . " - j + 1"; 
        $il = "i"; 
        $iexpl = "i = " . $_interest; 
     }
     else {
        $mult = $_frequency;
        $termj = $_term . " - \\frac{j-1}{" . $_frequency . "}"; 
        $il = " \\frac{i^{(" . $_frequency . ")}}{" . $_frequency . "}";
        $p_i = 1 + $_interest;
        $im_m = $this->im($_interest, $_frequency) / $_frequency; 
        $im_m = round($im_m,8);  // HARD CODED ROUNDING (FOR DISPLAY ONLY)
        $iexpl = $il . " = " . $p_i . "^{\\frac{1}{ " . $_frequency . "}} -1 = " . $im_m;
     }
     if ($_advance) $int = $il . " \\left( P_j - A \\right)";
     else           $int = $il . "        P_j ";
   $an = $a->latexAnnuity($termj, $_interest, $_frequency, $_advance);
   $s = $this->mortgageSchedule($_term, $_interest, $_frequency, $_advance, $_principal);
   if (count($s)>0){
       $out.= "<p>Repayment schedule:</p>";
       $out.= "<p>$ " . $iexpl . " $</p>";
       $out.= "<table class='schedule'>";
       $out.= "<thead>";
       $out.= "<tr>";
       $out.= "<th>Instalment counter $$ j $$ </th>";
       $out.= "<th>Principal \\begin{align*} P_j  & = P_{j-1} - R_{j-1} \\\\  \\\\ & = $mult A\\ " . $an . " \\end{align*}  </th>";
       $out.= "<th>Interest $$ I_j  = " . $int . "$$ </th>";
       $out.= "<th>Capital Repayment $$ R_j = A - I_j $$ </th>";
       $out.= "<th>Instalment amount $$ A $$ </th>";
       $out.= "</tr>";
       $out.= "</thead>";
       $out.= "<tbody>";
       $totalCap = 0;
       $totalInt = 0;
       $totalInst = 0;
       foreach ($s AS $i){
         $out.= "<tr>";
         $out.= "<td>" . CT1_Format::mynumber($i['count']) . "</td>";
         if (0!=$_frequency) $tt = $_term - ($i['count'] -1) / $_frequency;
         $link = current_page_url() . "&ct1_term=" . $tt . "&ct1_frequency=" . $_frequency . "&ct1_interest=" . $_interest . "&ct1_advance=" . $_advance . "&ct1_action=getAnnuityCertain";
         $out.= "<td><a href='" . $link . "'>" . CT1_Format::mynumber($i['oldPrincipal']) . "</a></td>";
         $out.= "<td>" . CT1_Format::mynumber($i['interest']) . "</td>";
         $out.= "<td>" . CT1_Format::mynumber($i['capRepay']) . "</td>";
         $out.= "<td>" . CT1_Format::mynumber($i['instalment']) . "</td>";
         $out.= "</tr>";
         $totalCap += $i['capRepay'];
         $totalInt += $i['interest'];
         $totalInst += $i['instalment'];
       }
       $out.= "<tr class='sum'>";
       $out.= "<td></td>";
       $out.= "<td>Sum</td>";
       $out.= "<td>" . CT1_Format::mynumber($totalInt) . "</td>";
       $out.= "<td>" . CT1_Format::mynumber($totalCap) . "</td>";
       $out.= "<td>" . CT1_Format::mynumber($totalInst) . "</td>";
       $out.= "</tr>";
       $out.= "</tbody>";
       $out.= "</table>";
   }
   return $out;
}


function roundingForApproxMortgage(){
    return -2; // HARD CODE
}

function latexApproxMortgage($_term, $_i, $_principal){
  $a = new CT1_Annuity();
  if ($_i != 0) {
    $out = "<p>";
    $out.= "\\begin{align*} \\dfrac{" . CT1_Format::mynumber($_principal) . "}{" . $a->latexAnnuity($_term, $_i, 'continuous', false) . "} ";
    $out.= "& \\approx  \dfrac{ ". CT1_Format::mynumber($_principal) ."}{n} \\left(1 + i \  n/2 \\right) \\\\ ";
    $out.= "& = ";
    $out.= " \dfrac{ " .  CT1_Format::mynumber($_principal) . "}{" . $_term . "} \\times \\left(1 + " . $_i . " \\times " . $_term . " / 2 \\right)   \\\\";
    $out.= " &  \\approx ";
    $out.= CT1_Format::mynumber(round($_principal / $a->annuityCertainApprox($_term, $_i), $this->roundingForApproxMortgage())) . ".";
    $out.= " \\end{align*}"; 
  }
  return $out;
}

function latexMortgage($_term, $_i, $_frequency, $_advance, $_ann, $_principal, $_instalment){
    $a = new CT1_Annuity();
    $out = "<p>Repayment per year ";
    $out.= "= $$ \\dfrac{" . CT1_Format::mynumber($_principal) . "}{" . $a->latexAnnuity($_term, $_i, $_frequency, $_advance) . "} ";
    $out.= "= \dfrac{" . CT1_Format::mynumber($_principal) . "}{" . $_ann . "} ";
    $out.= "= " . CT1_Format::mynumber(round($_principal / $_ann, 0)) .  ".$$";
    $out.= "</p>";
    return $out;
}


function random_float ($min,$max) {
   // returns random number uniformly distributed between $min and $max
   return ($min+lcg_value()*(abs($max-$min)));
}
    

function mortgage_func( $atts ){
//  echo "<pre>SESSION" . print_r($_SESSION,1) . "</pre>";
  $a = new CT1_Annuity();
  $marker = new ct1_marker();
  setlocale(LC_MONETARY, CT1_Format::locale()); // HARDCODE!
  if ($_SESSION['REQUEST']['ct1_action']=='mortgage'){
      $_REQUEST = $_SESSION['REQUEST'];
      $_SESSION['REQUEST']['ct1_action']='';
  }
  $_action = $_REQUEST['ct1_action'];
if ($_action == 'getAnnuityCertain'){  
  $ac = new CT1_Annuity();
  return $ac->annuityCertain_func();
  }
  $_value = htmlentities($_REQUEST['ct1_value']);
  $_term = $_REQUEST['ct1_term'];
  $_frequency = $_REQUEST['ct1_frequency'];
  $_interest = $_REQUEST['ct1_interest'];
  $_advance = $_REQUEST['ct1_advance'];
  $_principal = $_REQUEST['ct1_principal'];

//  $_i = (1 + $_interest)/(1 + $_escalation) - 1;
  $_ann = $a->annuityCertain($_term, $_interest, $_frequency, $_advance);
  $_inst_per_year = $_principal / $_ann;
  $_inst = round($_inst_per_year / $_frequency, 2);

if ($_action == 'mortgage'){  
    $out = "<p>Problem was to calculate the " . $this->problemMortgage($_term, $_interest, $_frequency, $_advance, $_principal) . "</p>";
    $out .= "<p>You say instalment amount is  ". CT1_Format::mycurrency($_value) . ".</p>";
    $out .= "<p>I say instalment amount is  " . CT1_Format::mycurrency($_inst) . " so that repayment per year is " . CT1_Format::mycurrency($_frequency * $_inst) . ".</p>";
    $out .= $this->latexMortgage($_term, $_interest, $_frequency, $_advance, $_ann, $_principal, $_inst);
    $out .= $a->latex($_term, $_interest, $_frequency, $_advance, $_ann);
    if ($_interest !=0){
      $out .= "<p>Approximate repayment per year: </p>" . $this->latexApproxMortgage($_term, $_interest, $_principal);
    }
    $_scoreRes = $marker->score($_inst, $_value);
    $out.= $marker->yourscore($_scoreRes['credit'], $_scoreRes['available']);
    $out.= $this->getMortgageSchedule($_term, $_interest, $_frequency, $_advance, $_principal);
  }
  elseif ($_action == 'getMortgage'){  
    $out = "<p>To calculate the " . $this->problemMortgage($_term, $_interest, $_frequency, $_advance, $_principal) . "</p>";
    $out .= "<p>Instalment amount is " . CT1_Format::mycurrency($_inst) . " so that repayment per year is " . CT1_Format::mycurrency($_frequency * $_inst) . ".</p>";
    $out .= $this->latexMortgage($_term, $_interest, $_frequency, $_advance, $_ann, $_principal, $_inst);
    $out .= $a->latex($_term, $_interest, $_frequency, $_advance, $_ann);
    if ($_interest !=0){
      $out .= "<p>Approximate repayment per year: </p>" . $this->latexApproxMortgage($_term, $_interest, $_principal);
    }
    $out.= $this->getMortgageSchedule($_term, $_interest, $_frequency, $_advance, $_principal);
  }
  else {
    // make up new question
    $_term = round(random_float(10,30),0);
    $_interest = round(random_float(0.01, 0.10),3);
    $af = array(1,2,4,12); // no continuous, say
    $_frequency = $af[rand(0,2)];
    $_advance = rand(0,1)==0;  
    $_principal = round(random_float(100000, 1000000),-3);
     
     $pairs = array( 'question'=>1, 'answer' => 1);
     $a = shortcode_atts( $pairs, $atts  );
     if ($a['question']) $out = $this->formMortgage($_term, $_interest, $_frequency, $_advance, $_principal);
     if ($a['answer']) $out.= "<hr/><p>" . $this->formMortgageGet($_term, $_interest, $_frequency, $_advance, $_principal) . "</p>";
  }
  return $out;
}

}
