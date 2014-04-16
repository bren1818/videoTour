<?php
	define( 'isProduction', 0 );
	define( 'isLocal', 0 );
	define( 'isStaging', 1);
	
	if( isStaging ){
		define( 'fixedPath', "http://infowebtest.wlu.ca/its/birwin");
	}else{
		define( 'fixedPath', '');
	}
	
?>