<?php
	define( 'isProduction', 0 );
	define( 'isLocal',0 );
	define( 'isStaging', 1);
	
	if( isStaging ){
		define( 'fixedPath', "http://infowebtest.wlu.ca/its/birwin");
	}else{
		define( 'fixedPath', '');
	}
	
	define( 'CUR_OS', 2 ); //1 = WINDOWS, 2 = *NIX, 3 = OSX (need binary)  - USing FFMPEG 32bit http://www.ffmpeg.org/download.html
	
	
?>