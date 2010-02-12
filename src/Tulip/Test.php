<?php

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Tulip.php" );

/**
 * An atomic test
 *
 */
class Tulip_Test {
	
	/**
	 * Flag to know if the test was successful
	 *
	 * @var boolean
	 */
	private $pass;
	
	/**
	 * Message corresponding to the test
	 *
	 * @var string
	 */
	private $text;
	
	/**
	 * Constructor
	 * 
	 * If $pass is false, the global Tulip config property "PASS" will be set to false
	 *
	 * @param boolean $pass
	 * @param string $text
	 */
	public function Tulip_Test( $pass , $text ) {

		if ( ! $pass ) {
			Tulip::config( "PASS" , false );
		}
		
		$this->pass = $pass;
		$this->text = $text;
	}
	
	/**
	 * Know if the test was successful
	 *
	 * @return boolean
	 */
	public final function pass() {
		return $this->pass;
	}
	
	/**
	 * Message associated with the test
	 *
	 * @return string
	 */
	public final function text() {
		return $this->text;
	}
	
}

?>