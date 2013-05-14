<?php   

$path_to_pear = ''; // this is a feature of your php installation
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_pear);

function CT1_autoloader($class, $file){
	if (!class_exists($class)){
		if (!stream_resolve_include_path($file)){ 
			throw new Exception("Can't instatiate " . $class);
		}
		else{
			require_once ($file);
			if (!class_exists($class)){
				throw new Exception("Can't instatiate " . $class);
			}
		}
	}
}

