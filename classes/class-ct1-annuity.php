<?php   
require_once 'class-ct1-interest.php';

class CT1_Annuity extends CT1_Interest{

	protected $term;

	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['term'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min'=>0,
					);
		return $r; 
	}

	public function get_parameters(){ 
		$r = parent::get_parameters();
		$r['term'] = array(
			'name'=>'term',
			'label'=>'Term (years)',
			);
		return $r; 
	}

	public function get_values(){ 
		$r = parent::get_values();
		$r['term'] = $this->get_term();
		return $r; 
	}

	public function __construct( $m = 1, $advance = false, $delta = 0, $term = 1 ){
		parent::__construct( $m, $advance, $delta);
		$this->set_term($term);
	}

	public function set_term($n){
		$candidate = array('term'=>$n);
		$valid = $this->get_validation($candidate);
		if ($valid['term']){
			if ( $this->is_valid_term_vs_frequency( $n ) ){ 
				$this->term = $n;
			} else {
				throw new Exception("Attempt to set term where frquency * term = number of instalments isn't an integer.  Annuity payment frequency is " . $this->get_m() . ", attempted term is " . $n . ".");
			}
		}
	}

	private function is_valid_term_vs_frequency( $n ){
		// valid if continuous or $n * m integer
		if ( $this->is_continuous() ) 
			return true;
		$close_enough = 0.00001;
		$trial = $n * $this->get_m();
		if ( $close_enough > abs( intval( $trial ) - $trial ) ) 
			return true;
		return false;
	}

	public function get_term(){
		return $this->term;
	}

	public function get_annuity_certain_approx(){
		return $this->term / (1.0 + 0.5 * $this->term * (exp($this->delta)-1) );
	}
 
	public function get_annuity_certain(){
		if (0==$this->get_delta()) return $this->get_term();
		$vn = exp($this->delta * -$this->term);
		return (1 - $vn) / $this->get_rate_in_form($this);
	}

	public function explain_annuity_certain(){
		$return = array();
		$return[0]['left'] = $this->label_annuity();
		if (0==$this->get_delta()){
			$return[0]['right'] =  "n";
			$return[1]['right'] =  $this->get_term();
		} else {
			$return[0]['right'] =  "\\frac{ 1 - \\exp{( -\\delta n) } }{ " . $this->label_interest_format() . " } ";
			$return[1]['right']['summary'] =  "\\frac{ 1 - \\exp{ (" . $this->explain_format( -$this->get_delta() ) . " \\times " . $this->get_term() . ") } }{ " . $this->explain_format( $this->get_rate_in_form( $this ) ) . " } ";
			$return[1]['right']['detail'] = $this->explain_rate_in_form( $this );
			$return[2]['right'] = $this->explain_format( $this->get_annuity_certain() ) ;
		}
		return $return;
	}


	public function set_from_input($_INPUT = array(), $pre = ''){
		try{
			if (parent::set_from_input($_INPUT, $pre)){
				$this->set_term(	$_INPUT[$pre. 'term'] );
				return true;
			}
			else{
				return false;
			}
		}
		catch( Exception $e ){ 
			return false; 
		}
	}

	public function get_label(){
		return $this->label_annuity();
	}

	protected function top_corner($n){
		return  "\\overline{" . $n . "|}";
	}
	
	protected function sub_n(){
		return "_{ " . $this->top_corner( $this->get_term() ) . "}";
	}


	protected function label_annuity(){
		if ($this->is_continuous()) $return = "\\bar{a}";
		else{
			if ($this->advance) $out="\\ddot{a}";
			else $out="a";
			if (1!=$this->m) $out.="^{(" . $this->m . ")}";
			$return = $out;
		}
		$return .= $this->sub_n();
		return $return;
	}

	public function get_labels(){
		$labels = parent::get_labels();
		$labels['CT1_Annuity'] = $this->label_annuity();
		return $labels;
	}

}

// example 
//$a = new CT1_Annuity(2, true, 0.1, 13);
//print_r($a->get_labels());
//print_r($a->explain_annuity_certain());

