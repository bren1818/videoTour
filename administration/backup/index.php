<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";

	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projID = $_REQUEST['projectID'];

		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projID);
		checkAccess( $projID );
		pageHeader();
		echo '<h1>"' .$project->getTitle() .'" -> Backup </h1>';
		echo '<p>Gathered Required elements</p>';
		$visitedSegments = array();
		$clipsToBackup = array();
		$segmentsToBackup = array();
		$decisionTreesToBackup = array();
		$decisionsToBackup = array();
		$segmentsToBackup = array();
		
		function showSegmentPath($segmentID){
			global $visitedSegments;
			if( in_array( $segmentID, $visitedSegments ) ){

			}else{
				global $conn;	
				$visitedSegments[] = $segmentID;
				$segments = new Segments($conn);
				$segment = $segments->load( $segmentID  );
				if( is_object($segment) ) {
					global $segmentsToBackup;
					$segmentsToBackup[] = $segment->getId() ;
					if(  $segment->getClipID()  != "" && $segment->getClipID() !=0 ){ // 0?
						if( $segment->getClipID() != -1 ){
							global $clipsToBackup;
							$clipsToBackup[] = $segment->getClipID();
						}
					}
					
					if( $segment->getDecisionTreeID() != "" || $segment->getDecisionTreeID() != -1 ){
						$dtID = $segment->getDecisionTreeID();
						global $decisionTreesToBackup;
						$decisionTreesToBackup[] =  $dtID ;
						
						$decisions = new Decisions( $conn );
						$decisions = $decisions->getList( $dtID );
						
						foreach( $decisions as $decision ){
							if( is_object ( $decision ) ){
								global $decisionsToBackup;
								$decisionsToBackup[] = $decision->getId();
								if( $decision->getEnds() ){
									if($decision->getClipID() != 0 && $decision->getClipID() != ""){
										global $clipsToBackup;
										$clipsToBackup[] = $decision->getClipID();
									}
								}else if( $decision->getContinues() ){ 
									if($decision->getClipID() != 0 && $decision->getClipID() != ""){
										global $clipsToBackup;
										$clipsToBackup[] = $decision->getClipID();
									}
									$nextSegment = new Segments($conn);
									$nextSegment = $nextSegment->load( $decision->getSegmentID() );
									
									if( is_object($nextSegment) ){
										showSegmentPath( $decision->getSegmentID() );
									}
								}else{	
									if($decision->getClipID() != 0 && $decision->getClipID() != ""){
										global $clipsToBackup;
										$clipsToBackup[] = $decision->getClipID();
									}
									
									if( $decision->getSegmentID() != $segmentID ){
										showSegmentPath($decision->getSegmentID());
									}
								}
							}
						}// end for
					}
				}else{
					echo "<p>Can't generate list - project likely incomplete</p>";
				}
			}
		}
		
		$segmentID = $project->getStartingSegmentID();
		if( $segmentID != 0 && $segmentID != "" ){
			showSegmentPath($segmentID);
			echo '<p>Segments - '.sizeof($visitedSegments).' > '."{".implode($visitedSegments, ",")."}</p>";
			echo '<p>Clips - '.sizeof($clipsToBackup).' > '. "{".implode($clipsToBackup, ",")."}</p>";
			echo '<p>Decision Trees - '.sizeof($decisionTreesToBackup).' > '. "{".implode($decisionTreesToBackup, ",")."}</p>";
			echo '<p>Decisions - '.sizeof($decisionsToBackup).' > '. "{".implode($decisionsToBackup, ",")."}</p>";
		}else{
			echo "<p>Cannot gather mandatory project Map</p>";
		}
	
		//get list of stuff from db, and serialize the objects.
		
		//step 0, projects
		//step 1, clips
		//step 2, badges
		//step 3, decision trees,
		//step 4, segways
		//step 5, decisions
		pa( (array)$project );
		
		
		$query = $conn->prepare("SELECT `id` FROM `clip` WHERE `projectID` = :projectID");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$clips = $query->fetchAll();
			pa( $clips );
		}
		

		
		
		?>
		<?php footerMenu($projID); ?>
		<?php
	}else{
		echo "<h1>Error: a project ID is required</h1>";
	}
	pageFooter();
?>