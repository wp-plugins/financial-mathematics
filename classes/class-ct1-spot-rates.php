<?php   
require_once 'class-ct1-spot-rate.php';
require_once 'class-ct1-collection.php';

class CT1_Spot_Rates extends CT1_Collection {

	protected function is_acceptable_class( $c ){
		if ( 'CT1_Spot_Rate' == get_class( $c ) )
			return true;
		return false;
	}

	public function __toString()
	{
		$return = array();
		if ( $this->get_count() > 0 ) {
			$o = $this->get_objects();
			foreach ( array_keys( $o ) as $key ){
				$return[ $key ] = print_r( $o[ $key ], 1 );
			}
		}
		return print_r( $return, 1);
	}


	public function get_one_year_forward_rates(){
		$r = array();
		$d = $this->get_one_year_forward_deltas();
		for ($i = 1, $ii = count( $d ); $i <= $ii; $i++){
			$r[ $i ] = exp( $d[ $i ] ) -1;
		}
		return $r;
	}
	
	public function get_one_year_forward_deltas(){
		$f = array();
		for ($i = 1, $ii = $this->maximum_contiguous_term(); $i <= $ii; $i++){
			if ( 1 == $i ){
				$f[1] = $this->get_spot_delta(1);
			} else {
				$f[ $i ] = $i * $this->get_spot_delta( $i ) - ( $i - 1 ) * $this->get_spot_delta( $i -1 );
			}
		}
		return $f;
	}

			
	
	private function get_spot_delta( $i ){
		$r = $this->get_spot($i );
		if (!is_null( $r ) ){
			return $r->get_delta();
		}
		return;
	}

	private function get_spot( $i ){
		if ( 0 < $this->get_count() ){
			foreach ($this->get_objects() as $c ) {
				if ( $i == $c->get_effective_time()  )
					return $c;
			}
		}
		return;
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

