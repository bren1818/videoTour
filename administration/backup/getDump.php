<?php
error_reporting(E_ALL);
include "../../includes/includes.php";
ob_clean();
if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] != "" ){
	$projID = $_REQUEST['projectID'];
	header('Content-Disposition: attachement; filename="project_'.$projID.'.projEx";');
	$conn = getConnection();
	$project = new Projects($conn);
	$project = $project->load($projID);
	//get list of stuff from db, and serialize the objects.
	
	$ProjectData = array();
	
	//step 1, projects
	$project->setConnection( "" );
	$projectData["project"] = $project;
	
	//step 2, clips
	$allClips = new Clips($conn);
	$allClips = $allClips->getProjectClips( $project->getId() );
	$projectData["Clips"] =  $allClips;
	
	//step 3, badges
	$allBadges = new Badge($conn);
	$allBadges = $allBadges->getList( $project->getId() );
	$projectData["Badges"] =  $allBadges;
	
	//step 4, Segments
	$allSegments = new Segments($conn);
	$allSegments = $allSegments->getList( $project->getId() );
	$projectData["Segments"] =  $allSegments;
	
	//step 5, decision trees,
	$allDecisionTrees = new DecisionTree($conn);
	$allDecisionTrees = $allDecisionTrees->getList( $project->getId() );
	$projectData["DecisionTree"] =  $allDecisionTrees;
	
	//step 6, (decisions)
	$allDecisions = new Decisions($conn);
	$allDecisions = $allDecisions->getListbyProject( $project->getId() );	
	$projectData["Decisions"] = $allDecisions;
	
	
	//step 7 CSS, JS
	$CSS =  new stylesheet($conn);
	$CSS = $CSS->loadByProjectID($project->getId() );
	if( is_object( $CSS ) ){
		$projectData["CSS"] = $CSS->getCSS();
	}else{
		$projectData["CSS"] = "";
	}
	
	$JS =  new javascript($conn);
	$JS = $JS->loadByProjectID($project->getId() );	
	if( is_object( $JS ) ){
		$projectData["JS"] = $JS->getJS();
	}else{
		$projectData["JS"] = "";
	}
		
	//clip Files
	$filePaths = array(); 
	$filePath = "../..";
	
	$query = $conn->prepare("SELECT `id` FROM `clip` WHERE `projectID` = :projectID");
	$query->bindParam(':projectID', $projID);
	if( $query->execute() ){
		$clips = $query->fetchAll();
		$clipObjects = array();

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
					$fileMap[] = array("id"=> $id, "type" => "clip", "origPath" => $files[$f]->getPath() );
				}
			}
			$clipObjects[] = $clip;
		}
		
	}
	
	//add badge files
	$badges = new Badge($conn);
	$badges = $badges->getList($projID);
	for($b =0; $b < sizeof($badges); $b++ ){
		$filePaths[] = $filePath.$badges[$b]->getPath();
		
		$fileMap[] = array("id"=> $badges[$b]->getId(), "type" => "badge", "origPath" => $badges[$b]->getPath() );
	}
	//add project start image
	
	if( $project->getShowPoster() ){
		$filePaths[] = $filePath.$project->getPosterFile();
		$fileMap[] = array("id"=> "", "type" => "poster", "origPath" => $project->getPosterFile() );
	}
	
	//echo "<p>Creating File... Contains: ".sizeof($filePaths)." files</p>";
	
	ob_implicit_flush(true);ob_end_flush();
	//pa( $filePaths );
	$zipName = 'project_'.$projID.'_backup.zip';
	
	//$created = create_zip( $filePaths, $zipName );
	$projectData["fileMap"] = $fileMap;
	//pa( $projectData );
	
	
	//remove PDO Connection data for serialization
	for($PD = 0; $PD < sizeof( $projectData ); $PD++){
		if( is_array( $projectData[$PD] ) ){
			for($o = 0; $o < sizeof($projectData[$PD]); $o++ ){
				if( is_object($projectData[$PD][$o]) ){
					if( method_exists($projectData[$PD][$o], "setConnection") ){
						$projectData[$PD][$o]->setConnection( "" );
					}
				}
			}
		}
	}
	
	//pa( $projectData );
	$ser =  serialize( $projectData );
	echo $ser;
	
	
}
?>