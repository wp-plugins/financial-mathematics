<?php   

require_once 'Validate.php';

abstract class CT1_Object {

public function get_valid_options(){ return array(); }

public function get_validation($candidate){
	return Validate::multiple($candidate, $this->get_valid_options());
}

protected function get_values(){ return array(); }
		
					
} // end of class

