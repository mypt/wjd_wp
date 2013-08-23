<?php
if ( function_exists('register_sidebar') )
    register_sidebar();
	
function getWJIdentifierImage(){
	
	if( !$_SESSION['wjIdentifierImages'] ){
		$path = TEMPLATEPATH.'/images/identifier/';
		$files =scandir( $path );
		foreach( $files as $v ){
			if( !is_file( $path.$v ) ) continue;
			$_SESSION['wjIdentifierImages'][] = $v;
		}
	}
	
	shuffle( $_SESSION['wjIdentifierImages'] );
	$img = array_pop( $_SESSION['wjIdentifierImages'] );
	return '/wp-content/themes/WJD/images/identifier/'.$img;
}
?>
