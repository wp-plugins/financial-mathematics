<?php   

//require_once 'functions.php';
CT1_autoloader('HTML_QuickForm2','HTML/QuickForm2.php');

define("CT1_maximum_levels_detail", 10);

class CT1_Render  {

private function get_render_latex_sentence( $equation_array, $label = '' ){
	$c = 1;
	$out = "";
	$detail = array();
	if ( !empty($label) ){
		$out.= "\\label{eq:" . $label . "} ";
		$c = $label;
	}
		$d = 1;
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
					if (array_key_exists('detail', $e['right'])){
						// refer forward to equation $c
						$out .= " \\mbox{ by \\eqref{eq:" . $c . "}}";
						$detail[] = array(
								'equation' => $e['right']['detail'],
								'label' => $c,
								);
//						echo "<pre>"; print_r($detail); echo "</pre>";
					}
				}
				else{
					$out.= " = " . $e['right'] ;
				}
			}
			if ($d < count($equation_array)) $out.= " \\\\ " . "\r\n";
			if ($d ==count($equation_array)) $out.= ". \\\\ \r\n \\nonumber " ;
			$d++;
			$c++;
		}
	return array('output'=>$out, 'detail'=>$detail);
}

public function get_render_latex( $equation_array ){
	// would be better if this were just recursive but I don't know how
	if (count($equation_array) > 0 ) {
		$out  = $this->get_mathjax_header() .  "\r\n";
		$out .= "$$ \r\n \\begin{align} " . "\r\n";
		$output = $this->get_render_latex_sentence( $equation_array );
		$out .= $output['output'] . "\r\n";
		$count_levels = 1;
		while ( $count_levels < CT1_maximum_levels_detail && isset( $output['detail'] ) ) {
			$count_levels++;
			if ( 0 < count( $output['detail'] ) ){
				foreach ($output['detail'] as $e) {
					$output = $this->get_render_latex_sentence( $e['equation'], $e['label'] );
					$out .= " \\\\" . "\r\n"; // close off the last line
					$out .= $output['output'] . "\r\n";
				}
			}
		}
		$out .= "\r\n" . "\\end{align} \r\n $$" . "\r\n";
	}
//	$out .= "\r\n <pre> " . print_r( $equation_array, 1 ) . "</pre> \r\n";
	return $out;
}

/*
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
*/

private function get_mathjax_header(){
	return "
		<script type='text/x-mathjax-config'>
		  MathJax.Hub.Config({ TeX: { equationNumbers: {autoNumber: 'all'} } });
		</script>
		<script type='text/javascript' src='http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML&#038;ver=3.5.1'>
		</script>
";
}

/*
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
*/

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


