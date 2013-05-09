<?php

interface CT1_Concept{
	public function get_quiz( $parameters );
	public function get_calculator( $parameters );
	public function get_solution( $parameters );
	public function set_score( $parameters );
	public function equals( $parameters );
}
