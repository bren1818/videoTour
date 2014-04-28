<?php
	$mode = 1; //1 - local (assumed windows), 2 - staging assumed linux, 3 -production assumed linux, 4 -portable assumed windows (usbwebserver)
	
	switch ($mode) {
		default:
		case 1: //Local
			define( 'isLocal', 1 );
			define( 'isProd', 0 );
			define( 'isStaging', 0);
			define( 'isPortable', 0 );
			define( 'fixedPath', '');
			define( 'CUR_OS', 1 );
		break;
		case 2: //Staging
			define( 'isLocal', 0 );
			define( 'isStaging', 1);
			define( 'isProd', 0 );
			define( 'isPortable', 0 );
			define( 'fixedPath', "http://infowebtest.wlu.ca/its/birwin");
			define( 'CUR_OS', 2 );
		break;
		case 3: //production version
			define( 'isLocal', 0 );
			define( 'isStaging', 0);
			define( 'isProd', 1 );
			define( 'fixedPath', "http://web.wlu.ca/goldenhawk");
			define( 'CUR_OS', 2 );
		break;
		case 4: //portable version
			define( 'isLocal', 0 );
			define( 'isProd', 0 );
			define( 'isStaging', 0);
			define( 'isPortable', 1 );
			define( 'fixedPath', '');
			define( 'CUR_OS', 1 );
		break;
	}
	
	//define( 'CUR_OS', 1 ); //1 = WINDOWS, 2 = *NIX, 3 = OSX (need binary)  - USing FFMPEG 32bit http://www.ffmpeg.org/download.html
?>