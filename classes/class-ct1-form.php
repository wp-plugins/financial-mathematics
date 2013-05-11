<?php   

require_once 'class-ct1-mortgage.php';

class CT1_Form{

private $prefix = "CT1_";
private $obj;

public function __construct(CT1_Object $obj){
	$this->set_obj($obj);
}

public function set_obj(CT1_Object $obj){
	$this->obj = $obj;
}

public function get_calculator($exclude = array()){
	$out = ""; 
	$parameters = $this->obj->get_parameters();
	$valid_options = $this->obj->get_valid_options();
	$values = $this->obj->get_values();
	if (count($parameters) > 0){
		foreach(array_keys($parameters) as $key){
			if (!in_array($key, $exclude)){
				$parameter = $parameters[$key];
				$valid_option = array();
				if (array_key_exists($key,$valid_options)){
					$valid_option = $valid_options[$key];
				}
				$value = '';
				if (array_key_exists($key,$values)){
					$value = $values[$key];
				}
				$out.= $this->get_input($valid_option, $parameter, $value);
			}
		}
	}
	return $out;
}

private function get_input($valid_option = array(), $parameter=array(), $value=''){
	$out = "";
	if (array_key_exists('type',$valid_option)){
  	$out.= "<p>" ."\r\n";
  	$out.= "<label>" . $parameter['label'] . "\r\n";
	  if ('number'==$valid_option['type']){
  		$out.= "<input type='text' ";
  		$out.= "name='" . $this->get_prefix() . $parameter['name'] . "' ";
  		$out.= "value='" . $value . "' ";
  		$out.= ">" . "\r\n";
  	}
  	elseif ('boolean'==$valid_option['type']){
  		$out.= "<input type='checkbox' ";
  		$out.= "name='" . $this->get_prefix() . $parameter['name'] . "' ";
  		if (true == $value) $out.= "CHECKED ";
  		$out.= ">" . "\r\n";
  	}
  	$out.= "</label>" . "\r\n";
  	$out.= "</p>" ."\r\n";
  }
  return $out;
}

public function get_prefix(){ 
	return $this->prefix;
}

}

/*
    $obj = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
    $form = new CT1_Form($obj);
    $html = $form->get_calculator(array("delta"));
echo $html;
*/

