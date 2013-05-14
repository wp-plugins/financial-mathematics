<?php   

//require_once 'class-ct1-mortgage.php';
require_once 'interface-ct1-concept.php';

abstract class CT1_Form implements CT1_Concept {

private $prefix = "CT1_";
protected $obj;

public function __construct(CT1_Object $obj){
	$this->set_obj($obj);
}

public function set_obj(CT1_Object $obj){
	$this->obj = $obj;
}

public function get_solution($parameters){
	return;
}

public function get_calculator(
		$exclude = array(), 
		$request='',
		$submit="Submit",
		$type='', 
		$special_input = '',
		$action='', 
		$method='GET', 
		$_dummy = NULL){
	$out = ""; 
	$out.="<form action='" . $action . "' method='" . $method . "'>" . "\r\n";
	$parameters = $this->obj->get_parameters();
	$valid_options = $this->obj->get_valid_options();
	$values = $this->obj->get_values();
	if (count($parameters) > 0){
		$out.= $this->get_form_inputs($parameters, $exclude, $valid_options, $values, $type);
	}
	$out.= $special_input;
	$out.= $this->hidden_page();
	$out.="<input type='hidden' name='" . $this->get_prefix() . "request' value='" . $request . "' />" . "\r\n";
	$out.="<input type='submit' value='" . $submit . "' />" . "\r\n";
	$out.="</form>" . "\r\n";
	return $out;
}

private function current_page(){
	if (isset($_GET['page_id'])) return $_GET['page_id'];
}

private function hidden_page(){
	return "<input type='hidden' name='page_id' value='" .$this->current_page() . "' />" . "\r\n";
}

protected function set_received_input(&$_INPUT = array()){
	$pre = $this->get_prefix();
	foreach (array_keys($this->obj->get_parameters()) as $p){
		if (!isset($_INPUT[$pre. $p])) $_INPUT[$pre. $p] = NULL;
	}
}

private function get_form_inputs($parameters = array(), 
		$exclude=array(), 
		$valid_options= array(), 
		$values=array(), 
		$type=''){
	$out = "";
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
				$out.= $this->get_input($valid_option, $parameter, $value, $type);
			}
		}
		return $out;
	}

}
private function get_input(
		$valid_option = array(), 
		$parameter=array(), 
		$value='', 
		$type=''
){
	$out = "";
	if (array_key_exists('type',$valid_option)){
	if ('hidden'!=$type){
  		$out.= "<p>" ."\r\n";
  		$out.= "<label>" . $parameter['label'] . "\r\n";
	}
  	$out.= "<input ";
  	$out.= "name='" . $this->get_prefix() . $parameter['name'] . "' ";
	if ('hidden'==$type){
		$out.= "type='hidden' ";
  		$out.= "value='" . $value . "' ";
  	}
	else{
		if ('number'==$valid_option['type']){
			$out.= "type='text' ";
  			$out.= "value='" . $value . "' ";
  		}
  		elseif ('boolean'==$valid_option['type']){
			$out.= "type='checkbox' ";
  			if (true == $value) $out.= "CHECKED ";
		}
  	}
  	$out.= "/>" . "\r\n";
	if ('hidden'!=$type){
  		$out.= "</label>" . "\r\n";
  		$out.= "</p>" ."\r\n";
	}
  }
  return $out;
}

public function get_prefix(){ 
	return $this->prefix;
}

}


