<?php

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Tulip.php" );

header("Content-type: text/html; charset=UTF-8");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 12 Apr 1975 06:00:00 GMT");

Tulip::config( "QUERY_STRING" , isset( $_SERVER[ "QUERY_STRING" ] ) ? urldecode( $_SERVER[ "QUERY_STRING" ] ) : "" );

Tulip::config( "START_TIME" , microtime() );

ob_start();

function Tulip_Output_shutdown() {
	
	// Report fatal errors
	
	$fatal_errors = array();
	preg_match_all ( '/fatal\s+error.*?:(.*)/i' , $tmp = ob_get_clean() , $fatal_errors );
	$fatal_errors = $fatal_errors[ 1 ];
	
	if ( count( $fatal_errors) ) {
		$last =  end( end( Tulip::modules() )->units() );
		foreach( $fatal_errors as &$fatal_error ) {
			$last->add( false , preg_replace( '/^\s+|\s+$|<.*?>/' , "" , $fatal_error ) , false );
		}
	}
	
	// Get the template
	$template = Tulip::config( "TEMPLATE");
	
	if ( substr( $template , 0 , 1 ) == "#" ) {
		$template = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "Output" . DIRECTORY_SEPARATOR . substr( $template , 1 ) . ".php";
	}
	
	// If template does not exist, try to get it from path
	if ( ! file_exists( $template ) ) {
		
		$file = false;
		
		$dirs = explode( PATH_SEPARATOR , Tulip::config( "PATH" ) );
			
		foreach( $dirs as $dir ) {
			
			if ( file_exists( $dir . DIRECTORY_SEPARATOR . $template ) ) {
				$file = $dir . DIRECTORY_SEPARATOR . $template;
				break;
			}
		}
		
		if ( ! $file ) {
			
			echo "Tulip: couldn't find template file $template";
			exit;
		}
		
		$template = $file;
	}
	
	// Include the template and render
	require_once( $template );
	
}

register_shutdown_function( "Tulip_Output_shutdown" );

?>