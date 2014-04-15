<?php
	//actually does the conversion via wget... which means we have to pass params... I don't like it...
	include "../../includes/includes.php";
	require_once("convert.php");

	set_time_limit(20 * 60);
	
	//what we need 
	// -projectID
	// -clip ID
	// -filename
	
	$projectID = $_REQUEST['PID'];
	$clipID = $_REQUEST['CID'];
	$filename = $_REQUEST['FN'];
	
	$conn = getConnection();
	//$clip = new Clip($conn);
				
	
	//new Clips
	$convert = new Convert( $conn );
	$convert->setClipID( $clipID );
	$convert->setProjectID( $projectID );
	
	
	//original cip
	$convert->setType( 0 ); //source
	$convert->setOutputpath( "/uploads/".$filename);
	$srcID = $convert->updateRecord();
	//$convert->updateRecord( $srcID ); // 
	
	// actual conversions
	
	$convert->setSourceFolder("../../uploads/");
	$convert->setDestinationFolder("../../uploads/");
	$convert->setSourceFile($filename);
	$convert->setDestinationFile( $projectID."_".$clipID );
	
	$convert->setType( 1 ); //original
	$convert->doConvert();
	
	//echo "Done Conversion 1";
	
	$convert->setType( 2 ); //mobile
	$convert->setWidth( 640 );
	$convert->setHeight( 480 );
	$convert->doConvert();
	
	//echo "Done Conversion 2";

	$convert->setType( 3 ); //mobile
	$convert->setWidth( 320 );
	$convert->setHeight( 240 );
	$convert->doConvert();
	
	//echo "Done Conversion 3";


?>