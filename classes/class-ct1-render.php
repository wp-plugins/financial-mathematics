<?php   

//require_once 'functions.php';
CT1_autoloader('HTML_QuickForm2','HTML/QuickForm2.php');

class CT1_Render  {

public function get_render_latex( $equation_array ){
	if (count($equation_array) > 0 ) {
	  $c = 1;
		$out = $this->get_mathjax_header() .  "\r\n";
		$out.= "$$ \r\n \\begin{align*} " . "\r\n";
		foreach ($equation_array as $e) {
			if (array_key_exists('left', $e)){
				$out.= $e['left'] . " & ";
			}
			else{
				$out.= " & ";
			}
			if (array_key_exists('right', $e)){
				if (array_key_exists('summary', $e['right'])){
					$out.= " = " . $e['right']['summary'] ;
				}
				else{
					$out.= " = " . $e['right'] ;
				}
			}
			if ($c < count($equation_array)) $out.= " \\\\ " . "\r\n";
			if ($c ==count($equation_array)) $out.= "." . "\r\n";
			$c++;
		}
		$out.= "\r\n" . "\\end{align*} \r\n $$" . "\r\n";
	}
	return $out;
}

public function test_popup(){
	return $this->get_popup_head() . '<A HREF="' . $this->get_popup_latex("a fraction $$ <a href=''>\\frac{1}{2}</a>$$ and Some text linking to <a href='http://www.bbc.co.uk'>bbc</a> and a fraction <a href='http://cnn.com'>$$ \\frac{1}{2}$$</a> that links to cnn") . '" onClick="return popup(this, ' . "'stevie'" .')">my popup</A>';
}

private function get_popup_head(){
	// source: http://www.htmlcodetutorial.com/linking/popup_test_a.html
	return '<SCRIPT TYPE="text/javascript">
		<!--
		function popup(mylink, windowname)
		{
			if (! window.focus)return true;
			var href;
			if (typeof(mylink) == "string")
				href=mylink;
			else
				href=mylink.href;
			window.open(href, windowname, "width=400,height=200,scrollbars=yes");
			return false;
		}
		//-->
		</SCRIPT>
';
}

private function get_mathjax_header(){
	return "
		<script type='text/x-mathjax-config'>
			MathJax.Hub.Config({tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}});
		</script>
		<script type='text/javascript' src='http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML&#038;ver=3.5.1'>
		</script>
";
}

private function get_popup($string){
	return $this->get_data_uri($string);
}

private function get_popup_latex($string){
	$page = "<html>" . "\r\n";
	$page.= "<head>" . "\r\n";
	$page.= $this->get_mathjax_header();
	$page.= "</head>" . "\r\n";
	$page.= "<body>" . "\r\n";
	$page.= $string . "\r\n";
	$page.= "</body>" . "\r\n";
	$page.= "</html>" . "\r\n";	
	return $this->get_data_uri($page);
}


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

private function get_data_uri($string){
// source: http://davidwalsh.name/data-uri-php
	return 'data: text/html;base64,'.base64_encode($string);
}


}


