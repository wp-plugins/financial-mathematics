<?php   

//$path = "/home/owen/public_html/wordpress/wp-content/plugins/fm16/classes/";
//set_include_path(get_include_path() . PATH_SEPARATOR . $path);

$path_to_pear = '/big/dom/xbdmfst/owenks/pear/share/pear/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_pear);

function CT1_autoloader($class, $file){
	if (!class_exists($class)){
		if (!include($file)){ 
			throw new Exception("Can't instantiate " . $class);
		}
		else{
			require_once ($file);
			if (!class_exists($class)){
				throw new Exception("Can't instantiate " . $class);
			}
		}
	}
}

