<?php
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	//this file should only be accessed by the show (index.php) other ajax functions should be locked to admin 
	//header('Content-Type: application/json');
	require_once("includes/includes.php");
	if( $_SERVER['REQUEST_METHOD'] == 'GET' ){
		if( isset( $_GET['fx']) && $_GET['fx'] != "" ){
			$function = $_GET['fx'];
			$conn = getConnection();
			
			
			
			switch ( $function ){
				case 'getSegment':
					if( isset( $_GET['id'] ) ){
					
						$segmentID = $_GET['id'];
						
						$segment= new Segments($conn);
						$segment = $segment->load( $segmentID );
						
						$segmentID = 0;
						$badgeID = 0;		 	
						$decisionTreeID = 0;
						$question = "";
						$step = 0;
						$startingClipID = 0;
						
						$badgePath = "";
						$badgeAlt = "";
						$badgePath = "";
						
						$decisionList = array();
						
						
						if( is_object( $segment ) ){
							$segmentID = $segment->getID();
							$badgeID   = $segment->getBadge();			
							$decisionTreeID = $segment->getDecisionTreeID();
							$startingClipID= $segment->getClipID();
						}else{
							logMessage( "Could not load segment ID: ".$segmentID, "requestHandler.log");
						}
						
						if( $decisionTreeID != 0 ){
							$decisionTree = new DecisionTree($conn);
							$decisionTree = $decisionTree->load( $decisionTreeID );
							if( is_object( $decisionTree ) ){
								$question = $decisionTree->getTitle();
								$step =   $decisionTree->getStep();
							}
						}else{
							logMessage( "Could not load Decision Tree ID: ".$decisionTreeID, "requestHandler.log");
						}
						
						if( $badgeID != 0 ){
							$badge = new Badge($conn);
							$badge = $badge->load($badgeID); 
							if( is_object( $badge ) ){
								$badgeAlt = $badge->getNote();
								$badgePath = $badge->getPath();
							}else{
								logMessage( "Could not load Badge ID: ".$badgeID, "requestHandler.log");
							
							}
						}
						
						if( $decisionTreeID != 0 ){
							//get choices 
							$decisions = new Decisions($conn);
							$decisions = $decisions->getList( $decisionTreeID );
							
							if( is_array( $decisions ) ){
								
								foreach( $decisions as $decision ){
									if( is_object( $decision ) ){
										$decisionID = $decision->getId();
										$playsClip = $decision->getClipID();
										$nextSegment = $decision->getSegmentID();
										$continues = $decision->getContinues();
										$ends = $decision->getEnds();
										$buttonText = $decision->getText();
									
										$decisionList[] = array("DecisionID"=>$decisionID, "PlaysClip"=>$playsClip, "NextSegmentID"=>$nextSegment, "Continues"=>$continues, "Ends"=> $ends, "ButtonText" => $buttonText);
									
									}
								}
							}else if( is_object( $decisions ) ){
						
								$decisionID = $decision->getId();
										$playsClip = $decisions->getClipID();
										$nextSegment = $decisions->getSegmentID();
										$continues = $decisions->getContinues();
										$ends = $decisions->getEnds();
										$buttonText = $decisions->getText();
									
								$decisionList[] = array("DecisionID"=>$decisionID, "PlaysClip"=>$playsClip, "NextSegmentID"=>$nextSegment, "Continues"=>$continues, "Ends"=> $ends, "ButtonText" => $buttonText);
								
							
							
							}else{
								
							
							}
						
						}
						
					
						$return = array("SegmentID" => $segmentID, "BadgeID"=> $badgeID, "BadgePath" =>$badgePath, "BadgeAlt"=> $badgeAlt, "DecisionTreeID"=>$decisionTreeID, "Question" => $question, "Step" => $step, "StartingClipID"=> $startingClipID, "Decisions" => $decisionList );
						
						$json = str_replace('\\u0000', "", json_encode( $return  ));
						ob_clean();
						echo $json;
					}
				break;
				case 'getClip':
					if( isset( $_GET['id'] ) ){
						$clipID = $_GET['id'];
						$clip = new Clip($conn);
						$clip = $clip->load( $clipID ); 
						$clipList = array();
						if( is_object( $clip ) ){
							//valid Clip
							$clips = $clip->getVersions( $clipID );
							foreach( $clips as $clip ){
								if( is_object($clip) ){
									$clipList[] = array("ClipType"=> $clip->getType(), "ClipPath"=> $clip->getPath() );
								}
							}
						}else{
							logMessage( "Could not load Clip ID: ".$clipID, "requestHandler.log");
						}
					
					$json = str_replace('\\u0000', "", json_encode( array("Clips" => $clipList)  ));
					ob_clean();
					echo $json;
				
					}
				break;
				case 'recordEvent':
					if( isset( $_GET['userID'] ) ){
						$userID = $_GET['userID'];
						//check if user exists
						$user =  new analytics_visitors($conn);
						$userExists = $user->exists($userID );
						if( $userExists ){
							$eventType = $_GET['eventType'];
							$step = $_GET['step'];
							$segmentId = $_GET['SegmentID'];
							$actionCount = $_GET['actionCount'];
							$clipID = $_GET['clipID'];
							$projectID = $_GET['projectID'];
					
					
							$event = new analytics_visitor_event($conn);
							$event->setVisitor_id( $userID );
							$event->setProjectID( $projectID );
							$event->setEvent_type( $eventType );
							$event->setOn_step( $step );
							$event->setUser_action( $actionCount );
							$event->setSegment_id( $segmentId );
							$event->setClipID( $clipID );
							
							$saved = $event->save();
							$ended = 0; $endedR = 0;
							if( $eventType == "Finished" ){
								$ended  = 1;
								$analyticUser = new analytics_visitors($conn);
								$userExists = $analyticUser->exists($userID );
								if( $userExists ){
									$analyticUser = $analyticUser->load( $id );
									if( is_object( $analyticUser ) ){
										$endedR = $analyticUser->saveEnded();
									}
								}
								
									
								
							}
							
							if( $saved == 1){
							ob_clean();
								if( $ended ){
									echo json_encode( array("Saved" => 1, "Ended" => $endedR ) );
								}else{
									echo json_encode( array("Saved" => 1 ) );
								}
							}
					
						}else{
							//no record
							ob_clean();
							echo json_encode( array("Saved" => 0 ) );
						}
					}else{
						//no record
						ob_clean();
						echo json_encode( array("Saved" => 0 ) );
					}
				break;
				case 'userEvents':
					if( isset( $_GET['userID'] ) &&  isset( $_GET['projectID'] ) ){
						$userID = $_GET['userID'];
						$projectID = $_GET['projectID'];
						$query = $conn->prepare("SELECT `event_time`, `event_type`, `on_step`, `user_action`, `clipID` FROM `analytics_events` WHERE `project_id` = :projectID AND `visitor_id` = :userID");
						$query->bindParam(':projectID', $projectID);
						$query->bindParam(':userID', $userID);
						if( $query->execute() ){
							$res = $query->fetchAll();
							ob_clean();
							echo str_replace('\\u0000', "", json_encode( array( "UserEvents" => $res) ));
						}
					}else{
						ob_clean();
						echo json_encode( array() );
					}
				break;
			}
		}
	}
?>