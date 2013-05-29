<?php   
require_once 'class-ct1-spot-delta.php';
require_once 'class-ct1-object.php';

class CT1_Spot_Rates extends CT1_Object {

	private $spot_deltas;

 
	public function get_clone_this(){
		$a_calc = new CT1_Spot_Rates();
		$a_calc->set_spot_deltas( $this->get_spot_deltas() );
		return $a_calc;
	}

	public function get_spot_deltas(){
		return $this->spot_deltas;
	}

	public function __toString()
	{
		$return = array();
		if ( count( $this->get_spot_deltas() ) > 0 ) {
			foreach ( $this->get_spot_deltas() as $s ){
				$return[ $s->get_effective_time() ] = exp( $s->get_delta() ) - 1 ;
			}
		}
		return print_r( $return, 1);
	}
			
	private function set_spot_deltas( $spot_delta_array ){
		$this->spot_deltas = $spot_delta_array;
	}

	public function add_spot_delta( CT1_Spot_Delta $c ){
		$this->spot_deltas[ $c->get_effective_time() ] = $c;
	}

	public function remove_spot_delta( CT1_Spor_Delta $c ){
		unset( $this->spot_deltas[ $c->get_effective_time() ] );
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
		if ( 0 < count( $this->get_spot_deltas() ) ){
			foreach ($this->get_spot_deltas() as $c ) {
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
		if ( 0 < count( $this->get_spot_deltas() ) ){
			foreach ($this->get_spot_deltas() as $c ) {
				if ( $i == $c->get_effective_time() )
					return true;
			}
		}
		return false;
	}


}

