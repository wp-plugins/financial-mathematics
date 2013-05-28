<?php   
require_once 'class-ct1-annuity-escalating.php';
require_once 'class-ct1-annuity-increasing.php';

class CT1_Cashflow extends CT1_Object {

    private $annuity;
    private $rate_per_year;
    private $effective_time;

    public function get_valid_options(){ 
        $r = $this->annuity->get_valid_options();
        $r['rate_per_year'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        $r['effective_time'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        return $r; 
    }

    public function get_parameters(){ 
        $r = array();
        $r['rate_per_year'] = array(
            'name'=>'rate_per_year',
            'label'=>'Rate of payment out per year',
            );
        $r['effective_time'] = array(
            'name'=>'effective_time',
            'label'=>'Effective time after t=0 (in years)',
            );
        $r = array_merge( $r, $this->annuity->get_parameters() );
        return $r; 
    }

    public function get_values(){ 
        $r = $this->annuity->get_values();
        $r['rate_per_year'] = $this->get_rate_per_year();
        $r['effective_time'] = $this->get_effective_time();
        $r['cashflow_value'] = $this->get_value();
        return $r; 
    }
    
    public function get_value(){
//echo "<pre>" . __FILE__ . "\r\n";
//echo "get_rate"  . $this->get_rate_per_year();
//echo "delta" .  $this->get_annuity()->get_delta();
//echo "effective-time" .  $this->get_effective_time() ;
//echo "annuity_value" .  $this->get_annuity()->get_value();
//echo "</pre>";
        return $this->get_rate_per_year() * exp( -$this->get_annuity()->get_delta() * $this->get_effective_time() ) * $this->get_annuity()->get_value();
    }


    public function __construct( $rate_per_year = 0, $effective_time = 0, CT1_Annuity $annuity = null ) {
        if ( null === $annuity )
            $annuity = new CT1_Annuity();
        $this->set_annuity( $annuity );
        $this->set_rate_per_year( $rate_per_year );
        $this->set_effective_time( $effective_time );
    }

    public function set_annuity( CT1_Annuity $a ){
        $this->annuity = $a;
    }

    public function set_rate_per_year($n){
        $candidate = array('rate_per_year'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['rate_per_year']){
            $this->rate_per_year = $n;
        }
    }
    
    public function set_effective_time($n){
        $candidate = array('effective_time'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['effective_time']){
            $this->effective_time = $n;
        }
    }

    public function get_annuity(){
        return $this->annuity;
    }

    public function get_rate_per_year(){
        return $this->rate_per_year;
    }

    public function get_effective_time(){
        return $this->effective_time;
    }

    private function is_single_payment(){
        if ( 1 != $this->get_annuity()->get_term() )
            return false;
        if ( 1 != $this->get_annuity()->get_m() )
            return false;
        if ( !$this->get_annuity()->get_advance() )
            return false;
        return true;
    }

    public function get_abs_label_with_annuity_evaluated(){
        if ( $this->is_single_payment() ) 
            $ann_label = "";
        else
            $ann_label = "\\times " . $this->get_annuity()->explain_format( $this->get_annuity()->get_annuity_certain() );
        if ( 0 == $this->get_effective_time() )
            $time_label = "";
        else
            $time_label = " \\times " . (1 + $this->get_annuity()->explain_format( $this->get_annuity()->get_i_effective() ) ) . "^{ - " . $this->get_effective_time() . " }";
        $rate_label = $this->rate_format( abs( $this->get_rate_per_year()) );
        return $rate_label . $time_label . $ann_label;
    }


            
    public function get_label( $abs = false ){
        if ( $this->is_single_payment() ) 
            $ann_label = "";
        else
            $ann_label = $this->get_annuity()->get_label();
        if ( 0 == $this->get_effective_time() )
            $time_label = "";
        else
            $time_label = "v^{ " . $this->get_effective_time() . " }";
        if ($abs ) {
            $rate_label = $this->rate_format( abs($this->get_rate_per_year()) );
        } else {
            $rate_label = $this->rate_format( $this->get_rate_per_year() );
        }
        return $rate_label . $time_label . $ann_label;
    }

    private function rate_format( $d, $dps = 2 ){
        return number_format( $d, $dps );
    }

    public function get_labels(){
        $labels = $this->annuity->get_labels();
        $labels['CT1_Cashflow'] = $this->get_label();
        return $labels;
    }

}

// example 
//$a = new CT1_Annuity(12, true, 0.1, 12);
//$a->set_value(11.234567890123456789);
//print_r($a->get_values());
//print_r($a->get_delta_for_value());
//$a->set_delta( $a->get_delta_for_value() );
//print_r($a->explain_interest_rate_for_value());

