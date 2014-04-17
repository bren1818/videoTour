<?php
	define( 'isProduction', 0 );
	define( 'isLocal', 1 );
	define( 'isStaging', 0);
	
	if( isStaging ){
		define( 'fixedPath', "http://infowebtest.wlu.ca/its/birwin");
	}else{
		define( 'fixedPath', '');
	}
	
	define( 'CUR_OS', 1 ); //1 = WINDOWS, 2 = *NIX, 3 = OSX (need binary)  - USing FFMPEG 32bit http://www.ffmpeg.org/download.html
	
	
?>