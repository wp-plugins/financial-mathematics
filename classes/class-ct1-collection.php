<?php   
require_once 'class-ct1-object.php';

abstract class CT1_Collection extends CT1_Object {

	protected $objects;
	protected $class; // read-only, set by first object;

	protected function is_acceptable_class( $c ){
		return true;
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

	public function get_values(){ 
		$r = array();
		$o = $this->get_objects;
		if ( 0 < $this->get_count() ){
			foreach ( array_keys( $o ) as $key ){
				$r[ $key ] = $o[ $key ]->get_values();
			}
		}
		return $r; 
	}
	
	public function get_count(){
		return count( $this->get_objects() );
	}

	public function get_objects(){
		return $this->objects;
	}

	public function set_objects( $array ){
		$this->objects = $array;
	}

	public function add_object( CT1_Object $c, $duplicates_allowed = false ){
		if( !$this->is_acceptable_class( $c ) ){
			throw new Exception( __FILE__ . "Object of class " . get_class( $c ) . " can't be added to collection of class" .  get_class( $this ) );
		}
		if( 0 == $this->get_count() ){
			$this->class = get_class( $c );
		}
		if( get_class( $c ) != $this->class ){
			throw new Exception( __FILE__ . "Object of class " . get_class( $c ) . " can't be added to collection of objects of class" .  $this->class );
		}
		if ( !$duplicates_allowed ) {
			$this->remove_object( $c );
		}
		if ( method_exists( $c, 'get_index' ) ){
			$this->objects[ $c->get_index() ] = $c;
		} else {
			$this->objects[] = $c;
		}
	}

	public function remove_object( CT1_Object $c, $remove_all = false ){
		if ( 0 < $this->get_count() ){
			$i = 0;
			foreach ( $this->get_objects() as $g ){
				if ( $c == $g ){
					unset( $this->objects[ $i ] );
					if ( !$remove_all )
						return;
				}
				$i++;
			}
		}
	}
	

}


