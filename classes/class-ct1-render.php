<?php   

//require_once 'functions.php';
CT1_autoloader('HTML_QuickForm2','HTML/QuickForm2.php');

class CT1_Render  {

public function get_render_form( $form ){
	if ('HTML'==$form['render'] ){
		return $this->get_form_html( $form );
	}
	else{
		return $this->get_form_plain( $form );
	}
}

private function get_form_plain( $return ){
	return print_r($return, 1);
}

private function get_form_html( $return ){
		// returns html based on form parameters in $return
	$form = new HTML_QuickForm2($return['name'],$return['method'], $return['action']);
	$form->addDataSource(new HTML_QuickForm2_DataSource_Array( $return['values'] ) );
	if (count($return['parameters']) > 0){
		$fieldset = $form->addElement('fieldset');
		foreach(array_keys($return['parameters']) as $key){
			if (!in_array($key, $return['exclude'])){
				$parameter = $return['parameters'][$key];
				$valid_option = array();
				if (array_key_exists($key,$return['valid_options'])){
					$valid_option = $return['valid_options'][$key];
					if ('number'==$valid_option['type']) $input_type='text';
					if ('boolean'==$valid_option['type']) $input_type='checkbox';
					
				}
				$value = '';
				$fieldset->addElement($input_type, $key)->setLabel($parameter['label']);
			}
		}
	}
	// add page_id
	$fieldset->addElement('hidden', 'request')->setValue($return['request']);
	$fieldset->addElement('hidden', 'page_id')->setValue($_GET['page_id']);
	$fieldset->addElement('submit', null, array('value' => $return['submit']));
	$out = "<p>" . $return['introduction'] . "</p>" . "\r\n";
	$out.= $form;
	return $out;
}

}


