<?php   

require_once 'class-ct1-object.php';

class CT1_Spot_Delta extends CT1_Object {

    private $delta;
    private $effective_time;

	private function get_valid_types(){
		return array( 'spot', 'forward', 'par' );
	}

    public function get_valid_options(){ 
        $r = array();
        $r['delta'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        $r['i_effective'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                        'min'=>'-0.99',
                    );
        $r['effective_time'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        $r['type'] = array(
                        'type'=>'string',
                    );
        return $r; 
    }

    public function get_parameters(){ 
        $r = array();
        $r['i_effective'] = array(
            'name'=>'i_effective',
            'label'=>'Annual effective rate (effective for time 0 to t for spot rates and par yields, from time t-1 to t for forward rates)',
            );
        $r['effective_time'] = array(
            'name'=>'effective_time',
            'label'=>'Effective time after t=0 (in years)',
            );
        return $r; 
    }

    public function get_values(){ 
        $r = array();
        $r['delta'] = $this->get_delta();
        $r['effective_time'] = $this->get_effective_time();
        $r['type'] = $this->get_type();
        return $r; 
    } 

    public function __construct( $i_effective = 0, $effective_time = 0, $type = 'spot' ) {
        $this->set_i_effective( $i_effective );
        $this->set_effective_time( $effective_time );
        $this->set_type( $type );
    }

    public function get_type(){
        return $this->type ;
	}

	public function set_type( $s ){
		if ( in_array( $s, $this->get_valid_types() ) ){
			$this->type = $s;
		}
	}

    public function get_delta(){
        return $this->delta ;
	}

    public function set_delta($n){
        $candidate = array('delta'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['delta']){
            $this->delta = $n;
        }
	}

    public function get_i_effective(){
        return exp( $this->delta ) -1;
	}

    public function set_i_effective($n){
        $candidate = array('i_effective'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['i_effective']){
            $this->delta = log( 1 + $n);
        }
    }
    
    public function set_effective_time($n){
        $candidate = array('effective_time'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['effective_time']){
            $this->effective_time = $n;
        }
    }

    public function get_effective_time(){
        return $this->effective_time;
    }
            
    public function get_label(){
        return "i" . "_{" . $this->get_effective_time();
    }

    public function get_label_delta(){
        return "\\delta" . "_{" . $this->get_effective_time();
    }

    public function get_labels(){
        $labels['CT1_Spot_Delta'] = $this->get_label();
        return $labels;
    }

}

