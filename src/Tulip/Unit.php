<?php

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "Test.php" );

/**
 * Class holding results for a unit test
 *
 */
class Tulip_Unit {
	
	/**
	 * Name/label of the method
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * List of tests
	 *
	 * @var array
	 */
	private $tests = array();
	
	/**
	 * Number of successfull tests
	 *
	 * @var number
	 */
	private $pass;

	/**
	 * Number of erroneous tests
	 *
	 * @var number
	 */
	private $fail;
	
	/**
	 * Number of non counted erroneous tests
	 *
	 * @var number
	 */
	private $notCountedFail;
	
	/**
	 * Number of expected tests (0 for unspecified)
	 *
	 * @var number
	 */
	private $expected;
	
	/**
	 * Constructor
	 *
	 * @param string $name name/label
	 * @return Tulip_Unit
	 */
	public function Tulip_Unit( $name ) {		
		$this->name = $name;
		$this->pass = 0;
		$this->fail = 0;
		$this->notCountedFail = 0;
		$this->expected = 0;
	}
	
	/**
	 * Get the name/label associated with the test
	 *
	 * @return string
	 */
	public final function name() {
		return $this->name;
	}
	
	/**
	 * Get the tests
	 *
	 * @return array
	 */
	public final function &tests() {
		return $this->tests;
	}
	
	/**
	 * Add a test
	 *
	 * @param boolean $pass true if success, false otherwize
	 * @param string $text message associated with the test
	 * @param boolean $count if set to false, test will not count as $pass or $fail
	 */
	public final function add( $pass, $text , $count = true ) {
		
		$this->tests[] =& new Tulip_Test( $pass && $count , $text );
		
		if ( $count ) {
			
			if ( $pass ) {
				
				$this->pass++;
				
			} else {
				
				$this->fail++;
				
			}
		} else {
			
			$this->notCountedFail++;
		}
	}
	
	/**
	 * Get number of successful tests
	 *
	 * @return number
	 */
	public final function nbPass() {
		return $this->pass;
	}
	
	/**
	 * Get number of erroneous tests
	 *
	 * @return number
	 */
	public final function nbFail() {
		return $this->fail;
	}
	
	/**
	 * Get number of expected tests
	 *
	 * @return number
	 */
	public final function nbExpected() {
		return $this->expected ? $this->expected : ( $this->pass + $this->fail );
	}
	
	/**
	 * Say if number of tests did not correspond with expected number of tests
	 *
	 * @return boolean
	 */
	public final function unexpected() {
		return $this->expected && ( $this->expected != $this->pass + $this->fail );
	}
	
	/**
	 * Say if the unit was entirely successful
	 *
	 * @return boolean
	 */
	public final function pass() {
		return ! $this->fail && ! $this->notCountedFail;
	}
	
	/**
	 * Say if the unit would have passed without non counted errors
	 * 
	 * @return boolean
	 */
	public final function wouldPass() {
		return ! $this->fail;
	}
	
	/**
	 * Specify the number of expected tests
	 *
	 * @param number $exp
	 */
	public final function setExpected( $exp ) {
		if ( $exp > 0 ) {
			$this->expected = $exp;
		}
	}
	
	/**
	 * Control if an unexpected number of tests were performed.
	 * If so, add a non-counting one to the collection.
	 */
	public final function checkExpected() {
		if ( $this->unexpected() ) {
			$nb = $this->pass + $this->fail;
			$exp = $this->expected;
			$this->add(
				false,
				"Expected $exp test" . ( $exp > 1 ? "s" : "" ) . " but "
					. ( $nb && $nb < $exp ? "only " : "" )
					. ( $nb ? $nb : "none" ) . " "
					. ( $nb > 1 ? "were" : "was" ) . " executed"
					. ( $nb > $exp ? " instead" : "" ),
				false
			);
		}
	}
}

?>