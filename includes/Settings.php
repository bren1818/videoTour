<?php
	define( 'isProduction', 0 );
	define( 'isLocal', 1 );
	define( 'isStaging', 0);
	
	if( isStaging ){
		define( 'fixedPath', "http://infowebtest.wlu.ca/its/birwin");
	}else{
		define( 'fixedPath', '');
	}
	
?>