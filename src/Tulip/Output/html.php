<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr" id="html">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= Tulip::config( "NAME" ) ?> Test Suite</title>

	<!-- Tulip style -->
	<style>/*<!--*/
<?php
	
	$style = explode( "\n" , file_get_contents( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "style.css" ) );
	
	foreach( $style as $styleLine ) {
		
		echo "\t\t$styleLine\n";
		
	}
	
?>
	/*-->*/</style>

<?php if ( Tulip::config( "CSS" ) ) { ?>
	<!-- Custom style -->
	<link rel="stylesheet" media="screen" href="<?= Tulip::config( "CSS" ) ?>" />
<?php } ?>

	<!-- Tulip script -->
	<script type="text/javascript">//<!--
<?php
	
	$script = explode( "\n" , file_get_contents( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "script.js"  ) );
	
	foreach( $script as $scriptLine ) {
		
		echo "\t\t$scriptLine\n";
		
	}
	
?>
	//--></script>
<body>
	<!-- Tulip structure -->
	<h1 id="tulip-header"><?= Tulip::config( "NAME" ) ?> Test Suite</h1>
	<h2 id="tulip-banner" class="<?= Tulip::config( "PASS" ) ? "pass" : "fail" ?>"></h2>
	<h2 id="tulip-info">PHP Version <?= Tulip::config( "PHP_VERSION" ) ?> - <?= Tulip::config( "PHP_ENGINE" ) ?></h2>
	<ol id="tulip-tests">
<?php
	$pass = 0;
	$fail = 0;
	
	$id = 0;
	
	$modules =& Tulip::modules();
	
	foreach( $modules as &$module ) { 
		
	$units =& $module->units();
	
	foreach( $units as &$unit ) {
		
	$id++;
	
	$pass += $unit->nbPass();
	$fail += $unit->nbFail();
	
	$name = htmlentities( $module->name() . ": " . $unit->name() , ENT_QUOTES , "UTF-8" );
?>
		<li class="<?= $unit->pass() ? "pass" : ( "fail" . ( $unit->wouldPass() ? " softFail" : "" ) ) ?>">
			<strong>
				<span onclick="Tulip.toggle( 'tulip-<?= $id ?>' )"><?= $name ?></span>
				<b style="color: black<?= $unit->pass() ? "" : "; background-color: white" ?>">(
					<b class="fail"><?= $unit->nbFail() ?></b>,
					<b class="pass"><?= $unit->nbPass() ?></b>,
					<b <?= $unit->unexpected() ? 'class="fail"' : "" ?>><?= $unit->nbExpected() ?></b>					
				)</b>
				-
				<span onclick="Tulip.open('<?= $name ?>');">[test this]</span>
				<ol id="tulip-<?= $id ?>" style="display:none">
<?php
	$tests =& $unit->tests(); 
	foreach( $tests as &$test ) {
?>
					<li class="<?= $test->pass() ? "pass" : "fail" ?>"><?= htmlentities( $test->text() , ENT_QUOTES , "UTF-8" ) ?></li>
<?php
	}
?>
				</ol>
			</strong>
		</li>
<?php
	} }
?>
	</ol>
	<p id="tulip-result">
		Tests completed in <?= number_format ( ( microtime( true ) - Tulip::config( "START_TIME" ) ) , 2 ) ?> seconds.
		<br />
		<span class="passed"><?= $pass ?></span> out of
		<span class="total"><?= $pass + $fail ?></span> tests passed,
		<span class="failed"><?= $fail ?></span> failed.
	</p>
</body>
</html>