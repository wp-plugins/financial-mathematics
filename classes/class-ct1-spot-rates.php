<?php   
require_once 'class-ct1-spot-rate.php';
require_once 'class-ct1-forward-rates.php';
require_once 'class-ct1-par-yields.php';
require_once 'class-ct1-collection.php';

class CT1_Spot_Rates extends CT1_Collection {

protected $explanation_forward_rates;
protected $explanation_par_yields;


	protected function is_acceptable_class( $c ){
		if ( 'CT1_Spot_Rate' == get_class( $c ) )
			return true;
		return false;
	}

	private function get_sorted_terms(){
		$terms = array_keys( $this->get_objects() );
		sort( $terms );
		return $terms;
	}

	public function explain_forward_rate( CT1_Forward_Rate $f ){
		if ( !$this->get_forward_rates()->is_in_collection( $f ) ){
			throw new Exception( __FILE__ . " Can't explain forward rate " . $f . " as it is not in collection." );
		}
		return $this->explanation_forward_rates[ $f->get_end_time() ];
	}

	public function get_forward_rates(){
		$spot_rates = $this->get_objects();
		$terms = $this->get_sorted_terms();
		$fs = new CT1_Forward_Rates();
		for ($i = 0, $ii = $this->get_count(); $i < $ii; $i++){
			$end = $terms[ $i ]; 	
			if ( 0 == $i ){
				$start = 0; $i = $spot_rates[ $end ]->get_i_effective();	
				$f = new CT1_Forward_Rate( $i, $start, $end );
				$explanation_algebra = $spot_rates[ $end ]->get_label();
				$exp[0]['right'] = $explanation_algebra;
				$exp[1]['right'] = $f->get_i_effective();
			} else {
				$start = $terms[ $i - 1 ]; 
				$phi = $spot_rates[ $end ]->get_delta() * $end - $spot_rates[ $start ]->get_delta() * $start;	
				$phi = $phi / ( $end - $start );
				$f = new CT1_Forward_Rate( exp( $phi ) - 1, $start, $end );
				$explanation_top = "\\left( 1 + " . $spot_rates[ $end ]->get_label() .  " \\right)^{" . $end . "}";
				$explanation_top_n = (1 + $spot_rates[ $end ]->get_i_effective() ) . "^{" . $end . "}";
				$explanation_bot = "\\left( 1 + " . $spot_rates[ $start ]->get_label() . " \\right)^{" . $start . "}";
				$explanation_bot_n = (1 + $spot_rates[ $start ]->get_i_effective()) . "^{" . $start . "}";
				$explanation = "\\frac{ " . $explanation_top . "}{" . $explanation_bot . "}";
				$explanation_n = "\\frac{ " . $explanation_top_n . "}{" . $explanation_bot_n . "}";
				$explanation_algebra = "\\left[ " . $explanation . "\\right]^{\\frac{1}{" . $end . "-" . $start . "}} - 1";
				$explanation_numbers = "\\left[ " . $explanation_n . "\\right]^{\\frac{1}{" . $end . "-" . $start . "}} - 1";
				$exp[0]['right'] = $explanation_algebra;
				$exp[1]['right'] = $explanation_numbers;
				$exp[2]['right'] = $f->get_i_effective();
			}
			$exp[0]['left'] = $f->get_label();
			$this->explanation_forward_rates[ $f->get_end_time() ] = $exp;
			$fs->add_object( $f );
		}
		return $fs;
	}

	public function get_par_yields(){
		$spot_rates = $this->get_objects();
		$terms = $this->get_sorted_terms();
		$ps = new CT1_Par_Yields();
		for ($i = 0, $ii = $this->maximum_contiguous_term(); $i < $ii; $i++){
			$end = $terms[ $i ]; 	
			$c = (1 - $spot_rates[ $end ]->get_vn() ) / $this->annuity_value( $end );
			$p = new CT1_Par_Yield( $c, $end );
			$ps->add_object( $p );
		}
		return $ps;
	}

	private function annuity_value( $term ){
		// returns discounted value of 1 payable at terms 1, 2, .. $term
		// provided spot rates exist for terms 1, 2, ... $term
		if ( $term > $this->maximum_contiguous_term() ){
			throw new Exception ( __FILE__ . " annuity_value sought for term " . $term . " though maximum_contiguous_term is " . $this->maximum_contiguous_term()  );
		}
		$spot_rates = $this->get_objects();
		$terms = $this->get_sorted_terms();
		$value = 0;
		for ($i = 1, $ii = $term; $i <= $ii; $i++){
			$value += $spot_rates[ $terms[ $i-1 ] ]->get_vn();
		}
		return $value;
	}

		
	private function maximum_contiguous_term(){
		$i = 1;
		while ($this->term_is_set( $i )){
			$i++;
		}
		$i--;
		return $i;
	}
	
	private function term_is_set( $i ){
		if ( 0 < $this->get_count() ){
			foreach ($this->get_objects() as $c ) {
				if ( $i == $c->get_effective_time() )
					return true;
			}
		}
		return false;
	}
			

}

