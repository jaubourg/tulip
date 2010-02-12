<?php

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "Unit.php" );

/**
 * A module
 */
class Tulip_Module {
	
	/**
	 * Name of the module
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * List of Tulip_Unit in the module
	 *
	 * @var array
	 */
	private $units = array();

	/**
	 * Constructor
	 *
	 * @param string $name name of the module
	 * @return Tulip_Module
	 */
	public function Tulip_Module( $name ) {
		$this->name = $name;
	}
	
	/**
	 * Get the name of the module
	 *
	 * @return string
	 */
	public final function name() {
		return $this->name;
	}
	
	/**
	 * Get the list of unit tests
	 *
	 * @return array
	 */
	public final function &units() {
		return $this->units;
	}
	
	/**
	 * Add a unit test to the collection
	 *
	 * @param Tulip_Unit $unit
	 */
	public final function add( &$unit ) {
		$this->units[] =& $unit;
	}
	
}
