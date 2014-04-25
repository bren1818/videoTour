<?php
	require_once("includes.php");
	//need to validate
	error_reporting(E_ALL^ E_WARNING);  
	//require_once("classes\authentication.php");
	$adminSession = new adminSession();
	if( $adminSession->check() ){
		//Not valid admin can't use this!
		$response = array("error" => "You must be an admin / logged in to use this.");
		logMessage( "User (not logged in) tried to access ajaxHandler", "ajaxHandler.log");
		ob_clean();
		echo json_encode( $response );
		exit;
	}
	
	
	if( $_SERVER['REQUEST_METHOD'] == 'GET' ){
		//this will handle requests;
		if( isset( $_GET['projectID'] ) && $_GET['projectID'] != "" &&
			isset( $_GET['object'] ) && $_GET['object'] != "" &&
			isset( $_GET['fx'] ) && $_GET['fx'] != ""
		){
		
			$pid = $_GET['projectID'];
			$obj = $_GET['object'];
			$fx = $_GET['fx'];
		
			logMessage( "Method: GET, id:".$pid.", object: ".$obj.", function: ".$fx, "ajaxHandler.log");
		
		
			//do magic here
			$conn = getConnection();
			$data = null;
			
			$fxu = array("error" => "function undefined");
			$err = array("error" => "function or object undefined");
			
			switch( $obj ){
				case 'session':
					if( $fx == "destroyRemoteSession" ){
						$adminSession = new adminSession();
						$adminSession->killRemoteSession( $pid );
						
						$data[] = array("killed" => 1);
						
					}
				break;
				case 'entry':
					if( $fx == "getEntry"){
						//entry id is pid
						$entry = new entry($conn);
						$entry = $entry->load( $pid );
						
					
						if( is_object( $entry ) ){
							$data[] = (array)$entry;
						}else{
							$data = array();
						}
					}
				break;
				case 'decisionTree':
					if( $fx == "list"){
						$dt = new DecisionTree($conn);
						$list = $dt->getList( $pid );
						foreach( $list as $item){
							$data[] = (array)$item;
						}
					}else{
						$data = $fxu; //function undefined
					}
				break;
				case 'clip':
					$clip = new Clip($conn);
					if( $fx == "list" ){
						$list = $clip->getList( $pid ); //array
						$data = array();
						foreach( $list as $item){
							$data[] = (array)$item;
						}						
					}else if($fx == "isUsed"){
						$clip->setId( $pid );
						$count = $clip->getUsageCount();
						$data[] = array("count" => $count );
					}else if( $fx == "delete"){
						$data = $fxu; //function undefined
					}else if( $fx == "getNote"){
						$clip = $clip->load( $pid );
						$data[] = array("Note" => $clip->getNote() );
					}else if( $fx == "getName"){
						$clip = $clip->load( $pid );
						$data[] = array("Name" => $clip->getName() );
					}
					
					
					
				break;
				case 'segment':
					$segment = new Segments( $conn );
					if( $fx == "get" ){
						$segment = $segment->load( $pid ); //actually its the segment id, we're cheating
						$data[] = (array)$segment;
					}else if( $fx == "getClip"){ 			//clip from Segment
						$segment = $segment->load( $pid );
						
						if( is_object( $segment ) ){
							$clipID = $segment->getClipID();
							$clip = new Clip( $conn );
							$clip = $clip->load( $clipID );
							$data[] = (array)$clip;	
						}else{
							$data[] = ""; // no segment
						}
						
					}else if($fx == "getDecisions"){
						$segment = $segment->load( $pid );
						$decisionTree = new DecisionTree( $conn );
						$decisionTree = $decisionTree->load(  $segment->getDecisionTreeID() );
						$decisions = new Decisions( $conn );
						$list = $decisions->getList( $decisionTree->getId() );
						foreach( $list as $item){
							$data[] = (array)$item;
						}	
					}else if($fx == "list"){
						$list = $segment->getList($pid);
						foreach( $list as $item){
							$data[] = (array)$item;
						}
					}else if( $fx == "getBadgePath" ){
						$segment = $segment->load( $pid );
						if( is_object( $segment ) ){
							$badgeID = $segment->getBadge();
							if( $badgeID != 0 ){
								$badge = new Badge($conn);
								
								$badge = $badge->load( $badgeID );
								if( is_object ( $badge ) ){
									$data[] = $badge->getPath();
								}else{
									$data[] = "";
								}
								
							}else{
								$data[] = "";
							}
						}else{
							$data[] = "";
						}
					
					}else{
						$data = $fxu; //function undefined
					}
				break;
				case 'badge':
					$badge = new Badge($conn);
					if( $fx == "getPath" ){
						$badge = $badge->load( $pid );
						if( is_object( $badge ) ){
							$data[] = $badge->getPath();
						}else{
							$data[] = 0;
						}
					}else if( $fx == "list" ){
					
						foreach( $badge->getList( $pid ) as $badge){
							$list[] = (array)$badge;
						}
						$data[] = $list;
						
					}else if($fx == "delete"){
						$badge = $badge->load( $pid );
						$deleted  = 0;
						if( is_object( $badge ) ){
							$deleted = $badge->delete();
						}
						$data[] = array("Deleted" => $deleted); 
						
					}else{
						$data[] = $fxu;
					}
				
				break;
				case 'project':
				
					if( $fx == "clearContestEntries" ){	
						$conn->beginTransaction();
						$entries = 0;
						try{
							$query = $conn->prepare("DELETE FROM `form_entry` where `projectID` = :pid");
							$query->bindParam(':pid', $pid);
							if( $query->execute() ){
								$entries = 1;
							}
						}catch(PDOException $e) {
							// roll back transaction
							logMessage( "Could not delete Contest Entries for project: ".$pid, "ajaxHandler.log");
							$conn->rollback();
							$entries = 0;
						}
						
						if( $entries == 1 ){
							$data[] = array( "Deleted" => "1" );
						
						}else{
							$data[] = array( "Deleted" => "0" );
						
						}
				
					}else if( $fx == "clearAnalytics" ){	
						$conn->beginTransaction();
						$analytic_events = 0;
						$analytic_visitors = 0;
						try{
							$query = $conn->prepare("DELETE FROM `analytics_events` WHERE `project_id` = :projectID");
							$query->bindParam(':projectID', $pid);
							if( $query->execute() ){
								$analytic_events = 1;
							}
							
							$query = $conn->prepare("DELETE FROM `analytics_visitors` WHERE `project_id` = :projectID");
							$query->bindParam(':projectID', $pid);
							if( $query->execute() ){
								$analytic_visitors = 1;
							}
							$conn->commit();
						}catch(PDOException $e) {
							// roll back transaction
							$conn->rollback();
							$analytic_events = 0;
							$analytic_visitors = 0;
						}
						
						if( $analytic_events ==1 && $analytic_visitors == 1 ){
							$data[] = array( "Deleted" => "1" );
						
						}else{
							$data[] = array( "Deleted" => "0" );
						
						}

					}else if( $fx == "delete" ){
						
						logMessage( "Began Project Deletion of Project ID: ".$pid, "ajaxHandler.log");
						
						$deletedClips = 0; $deletedClip = 0;
						$deletedBadges = 0;	$deletedDecisions = 0;
						$deletedDecisionTree = 0; $deletedSegments = 0;
						$deletedProject = 0; $deletionSucceeded = 0;
						$fileDelete = 0;
						
						//$conn = getConnection();
						$badges = new Badge( $conn );
						$badges = $badges->getList( $pid ); //array of clips
						$badgeList = array();
						
						$clips = new Clips( $conn );
						$clips = $clips->getProjectClips( $pid ); //array of clips
						$clipList = array();
						
						$project = new Projects($conn);
						$project = $project->load($pid);
						
						if( $project->getPosterFile() != "" ){
							if( unlink( '..'.$project->getPosterFile() ) ){
							
							}
						}
						
						
						if( is_array( $badges ) && sizeof( $badges ) > 0 ){
							foreach( $badges as $badge ){
								if( is_object( $badge ) ){
									//echo $badge->getPath();
									if( unlink( '../'.$badge->getPath() ) ){
										$badgeList[] = $badge->getPath();
									}
								}
							}
						}
					
					
						if( is_array( $clips ) && sizeof( $clips ) > 0 ){
							foreach( $clips as $clip ){
								if( is_object( $clip ) ){
									//echo $clip->getPath();
									if( unlink( '../'.$clip->getPath() ) ){
										$clipList[] = $clip->getPath();
									}
								}
							}
						}
						
					
						
						
						$conn->beginTransaction();
						try{
							$query = $conn->prepare("DELETE FROM `badges` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								$deletedBadges = 1;
							}
							
							$query = $conn->prepare("DELETE FROM `clip` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								$deletedClip = 1;
							}
							
							$query = $conn->prepare("DELETE FROM `clips` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								$deletedClips = 1;
							}
					
							$query =$conn->prepare("DELETE FROM `decisions` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								$deletedDecisions = 1;
							}
							
							$query = $conn->prepare("DELETE FROM `decisiontree` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								$deletedDecisionTree = 1;
							}
					
							$query = $conn->prepare("DELETE FROM `segments` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								$deletedSegments = 1;
							}
					
							$query = $conn->prepare("DELETE FROM `projects` WHERE `id` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								$deletedProject = 1;
							}
							
							$query = $conn->prepare("DELETE FROM `analytics_events` WHERE `project_id` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								//$deletedProject = 1;
							}
							
							$query = $conn->prepare("DELETE FROM `analytics_visitors` WHERE `project_id` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								//$deletedProject = 1;
							}
							
							
							$query = $conn->prepare("DELETE FROM `form_entry` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								//$deletedProject = 1;
							}
							
							$query = $conn->prepare("DELETE FROM `css` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								//$deletedProject = 1;
							}
							
							
							$query = $conn->prepare("DELETE FROM `js` WHERE `projectID` = :id");
							$query->bindParam(':id', $pid);
							if( $query->execute() ){
								//$deletedProject = 1;
							}
							
							
							
							
							
							$deletionSucceeded = 1;
							$conn->commit();
						}catch(PDOException $e) {
							// roll back transaction
							$conn->rollback();
							$deletedClips = 0; $deletedClip = 0;
							$deletedBadges = 0;	$deletedDecisions = 0;
							$deletedDecisionTree = 0; $deletedSegments = 0;
							$deletedProject = 0; $deletionSucceeded = 0;
							logMessage( "Error Deleting Project ID: ".$pid." > ".$e, "ajaxHandler.log");
						}

						$data[] = array( "deletionSucceeded" => $deletionSucceeded, "FileDelete"=>  $fileDelete,  "NumBadges" => sizeof( $badges ), "NumClips" => sizeof( $clips ) );

					}
				break;
				default:
					$data = $err;
				break;
			}
			
			$items =  sizeof( $data );
			if( $items == 1 ){ $data = $data[0]; }
			
			$response = array("object"=> $obj, "function" => $fx, "data"=> $data, "items" => $items );
			
		
			$json = str_replace('\\u0000', "", json_encode( $response  )); // don't know what I got crappy unicode...
			ob_clean();
			echo $json;
		
		}else{
		
			$response = array("error" => "(GET) Invalid Data Received");
			echo json_encode( $response );
		}
	}elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){
	

		if( isset( $_POST['objectType'] ) && $_POST['objectType'] != "" && isset( $_POST['fx'] ) && $_POST['fx'] != ""){
			
			$id = isset( $_POST['id'] ) ?  $_POST['id'] : "" ;
			$type = $_POST['objectType'];
			$fx = $_POST['fx'];
			$nv =  $_POST['newValue'];
			
			//do magic here
			$conn = getConnection();
			$data = null;
			
			$fxu = array("error" => "function undefined");
			$err = array("error" => "function or object undefined");
			
			logMessage( "Method: POST, id:".$id.", type: ".$type.", function: ".$fx.( (isset($nv) && $nv !="" ) ? "New value: ".$nv : ""), "ajaxHandler.log");
			
			
			switch( $type ){
				case 'project':
					$project = new Projects($conn);
					if( $fx == "active" ){	
						$project = $project->load( $id );
						$project->setActive( $nv );
						$resp = $project->save();
						$data[] = array("update"=>$resp);
					
					}else if($fx=="startingSegment"){
						$project = $project->load( $id );
						$project->setStartingSegmentID( $nv );
						$resp = $project->save();
						$data[] = array("update"=>$resp);
					}else{
						$data = $fxu;
					}
				break;
				case 'segment':
					$segment = new Segments( $conn );
					if( $fx == "SegmentUsageCount"){
						$uses = $segment->getUsageCount($id, $nv);
						$data[] = array("uses"=>$uses);
					}else if( $fx == "delete" ){
						$deleted = $segment->delete( $nv );
						$project = new Projects($conn);
						$project = $project->load( $id );
						if( $project->getStartingSegmentID() == $nv ){
							$project->setStartingSegmentID("");
							$pupdated = $project->save();
						}else{
							$pupdated = 0;
						}
						$data[] = array("deleted"=>$deleted, "projectUpdated"=>$pupdated);
					}else{
						$data = $fxu;
					}
					
					
				break;
				case 'decision':
					$decision = new Decisions( $conn );
					if( $fx == "delete" ){
						$deleted = $decision->delete( $nv );
						$data[] = array("deleted"=>$deleted);
					}else{
						$data = $fxu;
					}
				break;
				case 'decisionTree':
					$dt = new DecisionTree($conn);
					if( $fx == "delete" ){
						$deleted = $dt->delete( $nv );
						$data[] = $deleted; //array
					}else{
						$data = array("fx"=>$fx,"nv"=>$nv);
					}
				break;
				case 'clip': 
					$clip = new Clip($conn);
					if( $fx == "delete" ){
						$clip->setId( $id );
						$data[] = $clip->delete();
						
					}else if( $fx == "setNote" ){
						$clip = $clip->load( $id );
						$clip->setNote( $nv );
						if( $clip->save() ){
							$data[] = array("saved" => 1);
						}else{
							$data[] = array("saved" => 0);
						}
					}else if( $fx == "setName" ){
						$clip = $clip->load( $id );
						$clip->setName( $nv );
						if( $clip->save() ){
							$data[] = array("saved" => 1);
						}else{
							$data[] = array("saved" => 0);
						}
					
					}
				
				case 'clips':
					$clips = new Clips($conn);
					$type = $nv;
					//$clipId =  $id;
					if( $fx == "getPath" ){
						$data[] = array("path" => $clips->getVidPath($id, $type) );
					}else{
					
					}
				break;
				default:
					$data = $err;
				break;
			}
			
			
			
			
			//$data = array( "id"=> $id, "type"=>$type, "fx"=> $fx, "nv"=>$nv);
			
			$response = array("object"=> $type, "function" => $fx, "data"=> $data );
			$json = str_replace('\\u0000', "", json_encode( $response  )); // don't know what I got crappy unicode...
			ob_clean();
			echo $json;
		}else{
			$response = array("error" => "(POST) Invalid Data Received");
			ob_clean();
			echo trim( json_encode( $response ) );
		}
	}else{
		$response = array("error" => "No Data Received");
		ob_clean();
		echo json_encode( $response );
	}
	
	
?>