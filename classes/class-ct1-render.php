<?php   

//require_once 'functions.php';
CT1_autoloader('HTML_QuickForm2','HTML/QuickForm2.php');

define("CT1_maximum_levels_detail", 10);

class CT1_Render  {

private function get_render_latex_sentence( $equation_array, $label = '' ){
//    echo "\r\n <pre> get_render_latex eq_array " . print_r( $equation_array, 1 ) . "</pre> \r\n";

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
                if ( is_array( $e['right'] ) ){
                    if (array_key_exists('summary', $e['right'])){
                        $out.= " = " . $e['right']['summary'] ;
                        if (array_key_exists('detail', $e['right'])){
                            // refer forward to equation $c
                            // count now may forward refs you need here ?????
                            if ( $this->is_sentence( $e['right']['detail'] ) ) {
                                $out .= " \\mbox{ by \\eqref{eq:" . $c . "}}";
                                $detail[] = array(
                                    'equation' => $e['right']['detail'],
                                    'label' => $c,
                                    );
                            } else {
                                $count_refs = count( $e['right']['detail'] );
                                $eqlist = "";
                                for ($subeq = 0; $subeq < ($count_refs - 1); $subeq++){
                                    $eqlist.= "\\eqref{eq:" . $c . "." . $subeq . "}, ";
                                    $detail[] = array(
                                    'equation' => $e['right']['detail'][$subeq],
                                    'label' => $c . "." . $subeq,
                                    );
                                }
                                $eqlist.= "\\eqref{eq:" . $c . "." . ($count_refs-1) . "}";
                                $detail[] = array(
                                    'equation' => $e['right']['detail'][$count_refs-1],
                                    'label' => $c . "." . ($count_refs-1),
                                    );
                                $out .= " \\mbox{ by " . $eqlist . "}";
                            }
//                            echo "<pre>"; print_r($detail); echo "</pre>";
                        }
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
//    echo "\r\n <pre> get_render_latex detail " . print_r( $detail, 1 ) . "</pre> \r\n";
//    echo "\r\n <pre> get_render_latex out " . print_r( $out, 1 ) . "</pre> \r\n";
    return array('output'=>$out, 'detail'=>$detail);
}

private function is_sentence( $e ){
//echo "<pre>";
//    echo "is_sentence" . "\r\n";
//    print_r($e);
//echo "</pre>";
    if ( is_array( $e ) ){
        if ( count($e) > 0 ){
            if ( is_array( $e[0] ) ){
                if( isset( $e[0]['left'] ) || isset( $e[0]['right'] ) ){
//echo "is-sentence-true" . "\r\n";
                    return true;
                }
            }
        }
    }
//echo "is-sentence-false" . "\r\n";
    return false;
}

public function get_render_latex( $equation_array ){
//echo "<pre>";
//print_r( $equation_array );
//echo "</pre>";
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
                // CHECK HERE how many sets of details there are
		$ret_out = array();
		$ret_out['detail'] = array();
                foreach ($output['detail'] as $e) {
                    if ( $this->is_sentence( $e['equation'] ) ) {

                        $sub_output = $this->get_render_latex_sentence( $e['equation'], $e['label'] );
                        $out .= " \\\\" . "\r\n"; // close off the last line
                        $out .= $sub_output['output'] . "\r\n";
			$ret_out['detail'] = array_merge( $ret_out['detail'], $sub_output['detail']);
                    } else {
                        $sub_count = 0;
                        foreach ($e['equation'] as $e_detail) {
                            $sub_count++;
                            $sub_output = $this->get_render_latex_sentence( $e_detail, $e['label'] . "." . $sub_count );
                            $out .= " \\\\" . "\r\n"; // close off the last line
                            $out .= $sub_output['output'] . "\r\n";
				$ret_out['detail'] = array_merge( $ret_out['detail'], $sub_output['detail']);
                        }
                    }
                }
		$output['detail'] = $ret_out['detail'];
            }
        }
        $out .= "\r\n" . "\\end{align} \r\n $$" . "\r\n";
    }
//    $out .= "\r\n <pre> " . print_r( $equation_array, 1 ) . "</pre> \r\n";
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

// example
function _dummy_equation(){

    $s = array();
    $t = array();
    $e = array();
    $s[0]['left'] = "a";
    $s[0]['right'] = "formual fro a";
    $t[0]['left'] = "b";
    $t[0]['right'] = "formual for b";
    $u[0]['left'] = "c";
    $u[0]['right'] = "formual for c";
    $e[0]['left'] = "main result";
    $e[0]['right']['summary'] = "main formula using a and b";
    $e[0]['right']['detail'][0] = $s;
    $e[0]['right']['detail'][1] = $t;
    $e[0]['right']['detail'][2] = $u;
//    $e[0]['right']['detail'] = $t;
    
    return $e;
}


//example
//print_r( __FILE__ );
//$r = new CT1_Render();
//print_r( _dummy_equation()  );
//print_r( $r->get_render_latex( _dummy_equation() ) );
