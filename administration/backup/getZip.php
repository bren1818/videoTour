<?php
error_reporting(E_ALL);
include "../../includes/includes.php";
if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] != "" ){
	$projID = $_REQUEST['projectID'];
	$conn = getConnection();
	
	$project = new Projects($conn);
	$project = $project->load($projID);
	
	//clip Files
	$filePaths = array(); 
	$filePath = "../..";
	
	$query = $conn->prepare("SELECT `id` FROM `clip` WHERE `projectID` = :projectID");
	$query->bindParam(':projectID', $projID);
	if( $query->execute() ){
		$clips = $query->fetchAll();
		for($c =0; $c < sizeof($clips); $c++){
			$id = $clips[$c]["id"];
		
			$clip = new Clip($conn);
			$clip = (array)$clip->load( $id );
			
			//clips by clip id
			$files = new Clips($conn);
			$files = $files->getList($id);
			
			for( $f = 0; $f < sizeof( $files ); $f++){
				if( $f > 0 ){ //dont keep original files too large
					$clip["files"][] = $files[$f]->getPath();
					$filePaths[] = $filePath.$files[$f]->getPath();
				}
			}
		}
	}
	
	//add badge files
	$badges = new Badge($conn);
	$badges = $badges->getList($projID);
	for($b =0; $b < sizeof($badges); $b++ ){
		$filePaths[] = $filePath.$badges[$b]->getPath();
	}

	if( $project->getShowPoster() ){
		$filePaths[] = $filePath.$project->getPosterFile();
	}
	
	$zipName = 'project_'.$projID.'_backup_files.zip';
	$created = create_zip( $filePaths, $zipName );
	if( $created ){
		header('Content-type: application/zip');
		header('Content-Disposition: attachment; filename="'.$zipName.'"');
	//	ob_clean();
	//	flush();
		readfile($zipName);
	}
	//unlink($zipName);
		
}else{
	exit;
}
?>