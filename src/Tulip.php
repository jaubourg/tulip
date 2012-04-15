<?php

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "Tulip" . DIRECTORY_SEPARATOR . "Module.php" );

/**
 * Tulip is the base class for all unit tests
 * 
 * A unit test will look like this:
 * 
 * class MyUnitTest extends Tulip {
 * 
 *     public $module = "MyUnitTest's module name";
 * 
 *     public $labels = array(
 *         
 *         "func1" => "func1's title",
 *         "func2" => "func2's title",
 *         // ...
 *         "funcN" => "funcN's title"
 *     );
 * 
 *     public function func1() {
 *         // Tests
 *     }
 * 
 *     public function func2() {
 *         // Tests
 *     }
 * 
 *     // ...
 * 
 *     public function funcN() {
 *         // Tests
 *     }
 * 
 * }
 * 
 * $module and $labels are optional if not found, Tulip will use the class name
 * and the functions names as module name and labels
 *
 */
abstract class Tulip {
	
	/**
	 * Hash table containing config information
	 * 
	 * @var array
	 */
	private static $config = array(
		"NAME" => "Unit",
		"PATH" => ".",
		"CSS" => false,
		"TEMPLATE" => "#html",
		"PASS" => true
	);
	
	/**
	 * Set/retrieve config information
	 * 
	 * Usage:
	 * - config( key ) => get the value associated with the key
	 * - config( key , value ) => set the value for the given key to the given value
	 * - config( array( key => value, ... ) ) => for each element, do config( key , value )
	 *
	 * Keys are case insensitive strings.
	 * 
	 * @param mixed $name the name or an array of values
	 * @param array $value
	 * @return mixed
	 */
	public static final function config( $name , $value = NULL ) {
		
		if ( is_array( $name ) ) {
			
			foreach( $name as $n => $v ) {
				
				Tulip::config( $n , $v );
				
			}
			
		} else if ( is_string( $name ) ) {
			
			$name = strtoupper( $name );
			
			if ( $value !== NULL ) {
				
				Tulip::$config[ $name ] = $value;
				
			} else {
				
				return Tulip::$config[ $name ];
				
			}
			
		}
		
	}
	
	/**
	 * Include a php file using require_once
	 * 
	 * If the file is a directory, then it will be recursively explored
	 * and all php files within will get included
	 *
	 * @param string $path path to the file
	 * @param boolean $findPath if set to true, will use 
	 */
	private static final function loadFile( $path , $findPath = true ) {
		
		if ( $findPath && ! file_exists( $path ) ) {
			
			$name = false;
			
			$dirs = explode( PATH_SEPARATOR , Tulip::$config[ "PATH" ] );
			
			foreach( $dirs as $dir ) {
				
				if ( file_exists( $dir . DIRECTORY_SEPARATOR . $path ) ) {
					$name = $dir . DIRECTORY_SEPARATOR . $path;
					break;
				}
			}
			
		} else {
			
			$name = $path;
		}
		
		if ( file_exists( $name ) ) {
		
			if ( is_dir( $name ) ) {
				
				$name =  realpath( $name );
				
				$dir = opendir( $name );
				
				while ( $file = readdir( $dir ) ) {
					
					if ( substr( $file , 0 , 1 ) != "." ) {
						
						Tulip::loadFile( $name . DIRECTORY_SEPARATOR . $file , false );
						
					}
				}
				
				closedir( $dir );
				
			} else if ( substr( $name , -4 ) == ".php" ) {
				
				require_once( $name );
					
			}
		}
	}
	
	/**
	 * Runs all tests
	 * 
	 * If $file is given, then unit test classes defined within it will get tested too.
	 * If $file is a directory, then all files within it and its subdirectories will be inspected
	 * for test classes.
	 *
	 * @param string $file optional path to a file
	 */
	public static final function run( $file = false ) {
		
		if ( is_string( $file ) ) {
		
			Tulip::loadFile( $file );	
			
		}
		
		// Get the page outputer (in case no test was run
		require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "Tulip" . DIRECTORY_SEPARATOR . "Output.php" );
		
		$tulip = new ReflectionClass( "Tulip" );
		$classes = get_declared_classes();
		
		foreach( $classes as $class ) {
			$class = new ReflectionClass( $class );
			if ( $class->isSubclassOf( $tulip ) ) {
				try {
					
					$class->newInstance();
					
				} catch( Exception $e ) {
					
				}
			}
		}
	}
	
	/**
	 * List of all the modules ran so far
	 *
	 * @var array
	 */
	private static $modules = array();
	
	/**
	 * Get the list of modules
	 *
	 * @return array
	 */
	public static final function &modules() {
		return Tulip::$modules;
	}
	
	/**
	 * Current test being ran
	 *
	 * @var Tulip_Result
	 */
	private $current;
	
	/**
	 * Tests if 2 test identifiers are not compatible
	 *
	 * @param string $str1 first identifier
	 * @param string $str2 second identifier
	 * @param boolean $anyOrder
	 * @return boolean
	 */
	private static final function incompatible( $str1 , $str2 , $anyOrder = false ) {
		$tmp = strpos( $str1 , $str2 );
		$tmp = $tmp === FALSE || $tmp != 0;
		if ( $tmp && $anyOrder ) {
			$tmp = strpos( $str2 , $str1 );
			$tmp = $tmp === FALSE || $tmp != 0;
		}
		return $tmp;
	}
	
	/**
	 * Constructor
	 *
	 * @param mixed $module
	 * @param array $labels
	 * @return Tulip
	 */
	public function Tulip( $moduleName = false, $labels = false ) {
		
		// Manage params
		if ( $labels === false ) {
			if ( is_array( $moduleName ) ) {
				$labels = $moduleName;
				$moduleName = false;
			}
		}
		
		if ( $moduleName === false ) {
			if ( property_exists( $this , "module" ) ) {
				$moduleName = $this->module;
			}
		}
		
		if ( ! is_string( $moduleName ) ) {
			$moduleName = false;
		}
		
		if ( ! is_array( $labels ) ) {
			$labels = array();
		}
		
		if ( property_exists( $this , "labels" ) && is_array( $this->labels ) ) {
			foreach ( $this->labels as $name => $alias ) {
				if ( ! isset( $labels[ $name ] ) ) {
					$labels[ $name ] = $alias;
				}
			}
		}
		
		// Get the page outputer
		require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "Tulip" . DIRECTORY_SEPARATOR . "Output.php" );
		
		// Get the class
		$class = new ReflectionClass( get_class( $this ) );
		
		// Get the module name
		$moduleName = ( $moduleName ? $moduleName : $class->getName() ) . " Module";
		
		// Control if the module has to be executed
		if ( Tulip::$config[ "QUERY_STRING" ] && Tulip::incompatible( $moduleName , Tulip::$config[ "QUERY_STRING" ] , true ) ) {
			return;
		}
		
		// Get the methods
		$tests = $class->getMethods( ReflectionMethod::IS_PUBLIC );
		
		// Init a module
		$module = new Tulip_Module( $moduleName );
		
		// Stack the result
		Tulip::$modules[] = $module;
		
		// For each public method
		foreach ( $tests as $test ) {
			
			$name = $test->getName();
			
			if ( isset( $labels[ $name ] ) ) {
				$name = $labels[ $name ];
			}
			
			if (	
					// Don't call for static or constructor
					$test->isStatic() || $test->isConstructor()
					
					// Don't call Tulip's own methods
				||	$test->getDeclaringClass()->getName() == "Tulip" 
			
				||	// Control if the module has to be executed
					Tulip::$config[ "QUERY_STRING" ]
					&& Tulip::incompatible(  "$moduleName: $name" , Tulip::$config[ "QUERY_STRING" ] ) ) {
				
				continue;
			}
			
			// Create a unit test repository and set it as current
			$this->current = new Tulip_Unit( $name );
			
			// Add to the module
			$module->add( $this->current );
			
			try {
				
				// Invoke the test
				$test->invokeArgs( $this , array() );
				
			} catch( Exception $e ) {
				
				// Eventually notify an error
				$this->current->add( false , $e->getMessage() , false );
				
			}
			
			// Control we have all expected tests (if needs be)
			$this->current->checkExpected();			
		}
		
		// Forget about the current
		unset( $this->current );
	}
	
	/**
	 * Specifies how many tests the unit currently ran is supposed to issue.
	 *
	 * @param number $n
	 */
	public final function expect( $n ) {
		$this->current->setExpected( 1 * $n );
	}
	
	/**
	 * Creates a test for the unit currently ran:
	 * - if $test is true, then a success is added
	 * - if $test is false, then an error is added
	 *
	 * @param boolean $test
	 * @param string $message optional
	 */
	public final function ok( $test , $message = "OK" ) {
		$this->current->add( $test , $message );
	}
	
	/**
	 * Creates a test for the unit currently ran:
	 * - if $actual == $test, then a success is added
	 * - if $actual != $test, then an error is added
	 *
	 * @param mixed $actual actual value
	 * @param mixed $expected expected value
	 * @param string $message optional
	 */
	public final function equal( $actual , $expected , $message = false ) {
		$this->ok(
			$actual == $expected , 
			( $message ? "$message - " : "" ) . ( var_export( $expected , true ) . "==" . var_export( $actual ,true ) )
		);
	}
	
	/**
	 * Creates a test for the unit currently ran:
	 * - if $actual != $test, then a success is added
	 * - if $actual == $test, then an error is added
	 *
	 * @param mixed $actual actual value
	 * @param mixed $expected expected non-value
	 * @param string $message optional
	 */
	public final function notEqual( $actual , $expected , $message = false ) {
		$this->ok(
			$actual != $expected , 
			( $message ? "$message - " : "" ) . ( var_export( $expected , true ) . "!=" . var_export( $actual ,true ) )
		);
	}
	
	/**
	 * Creates a test for the unit currently ran:
	 * - if $actual === $test, then a success is added
	 * - if $actual !== $test, then an error is added
	 *
	 * @param mixed $actual actual value
	 * @param mixed $expected expected value
	 * @param string $message optional
	 */
	public final function strictEqual( &$actual , &$expected , $message = false ) {
		$this->ok(
			$actual === $expected , 
			( $message ? "$message - " : "" ) . ( var_export( $expected , true ) . "===" . var_export( $actual ,true ) )
		);
	}
	
	/**
	 * Creates a test for the unit currently ran:
	 * - if $actual !== $test, then a success is added
	 * - if $actual === $test, then an error is added
	 *
	 * @param mixed $actual actual value
	 * @param mixed $expected expected non-value
	 * @param string $message optional
	 */
	public final function notStrictEqual( &$actual , &$expected , $message = false ) {
		$this->ok(
			$actual !== $expected , 
			( $message ? "$message - " : "" ) . ( var_export( $expected , true ) . "!==" . var_export( $actual ,true ) )
		);
	}
	
}

Tulip::config( array(
	"PATH" => get_include_path(),
	"PHP_VERSION" => phpversion()
) );

try {
	
	Tulip::config( "PHP_ENGINE" , "Zend " . zend_version() );
	
} catch( Exception $_120475_Tulip ) {
	
	Tulip::config( "PHP_ENGINE" , "Unknown" );
	
}

?>