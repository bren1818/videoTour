<?php
	error_reporting(E_ALL);
	include "../includes/includes.php";
	
	$HIDE_DUPES = 0;
	
	if( isset($_REQUEST['hideDupeTrails'] ) && $_REQUEST['hideDupeTrails'] == 0 ){
		$HIDE_DUPES = 1;
	}

	
	
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projID = $_REQUEST['projectID'];
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projID);
		pageHeader();
		echo '<h1>"' .$project->getTitle() .'" Flow Chart</h1>';
	?>
		<style type="text/css">
			ul.decisionTree,
			ul.segments{
				list-style: none;
			}
			
			ul.decisionTree{
				padding-left: 0px;
			}
			
			li.decision_ends{
				border-color: #f00;
			}
		</style>	
	<?php	
		$visitedSegments = array();
		
		function showSegmentPath($segmentID){
			global $visitedSegments;
			echo '<ul class="segments segmentID_'.$segmentID.'">';
				if( in_array( $segmentID, $visitedSegments ) ){
					echo '<li class="alreadyFollowed"><p>This Segment ('.$segmentID.') <b>has already be followed</b>. This could because it is a <b>try again option</b> (which means this is ok). If not, it could indicate a logic error. - If you\'re concerned add the <b><a href="'.fixedPath.'/administration/MapProject.php?projectID='.$_REQUEST['projectID'].'&hideDupeTrails=0">&hideDupeTrails=0</a></b> flag in the URL </p></li>';
				}else{
					global $conn;	
					$visitedSegments[] = $segmentID;
					$segments = new Segments($conn);
					$segment = $segments->load( $segmentID  );
					if( is_object($segment) ) {
						echo '<li class="segment">';		
							if(  $segment->getClipID()  != "" && $segment->getClipID() !=0 ){ // 0?
							
								if( $segment->getClipID() != -1 ){
									echo '<hr /><b>New Scene</b> - '.$segment->getNote().' <hr />';
									echo '<p>This segment begins with playing this clip <a class="button" onClick="previewClip('.$segment->getClipID().')"><i class="fa fa-play"></i> Preview</a></p>';
								}else{
									echo '<p>This segment has no clip - so the tour will jump to the questions</p>';
								}
								
							}else{
								echo '<p>This segment has an invalid clip id set... <b>Fix it</b></p>';
							}
							
							if( $segment->getDecisionTreeID() != "" || $segment->getDecisionTreeID() != -1 ){
								echo "<p>The Clip is then followed by a list of decisions (Decision Tree: id: ". $segment->getDecisionTreeID().')</p>';
								$dtID = $segment->getDecisionTreeID();
								$decisions = new Decisions( $conn );
								$decisions = $decisions->getList( $dtID );
						
								echo '<ul class="decisionTree decisionTree_'.$dtID.'">';
									foreach( $decisions as $decision ){
										if( is_object ( $decision ) ){
											echo '<li id="decision_'.$decision->getId().'" class="decision  '.(  ($decision->getEnds()) ? ' decision_ends' : '').(  ($decision->getContinues()) ? ' decision_continues' : '').'">';		
												echo '<p>Button: <b>'.$decision->getText().'</b><br /><p>Note: '.$decision->getNote().'</p>';
												if( $decision->getEnds() ){
													echo '<p>This option <b>ends</b> the tour afted playing this clip: ';
													
													if($decision->getClipID() != 0 && $decision->getClipID() != ""){
														echo '<a class="button" onClick="previewClip('.$decision->getClipID().')"><i class="fa fa-play"></i> Preview</a></p>';
													}else{
														echo '<b>INVALID CLIP</b></p>';
													}
													
													/*
													//load the ending  segment, ensure it doesn't continue
													$nsegmentID = $decision->getSegmentID();
													if( $nsegmentID != 0 || $nsegmentID != ""){
														echo '<p><b>warning... this clip has a segment attached to it when it is supposed to end the video. Technically it shouldnt! Check your logic... or remove the segment attached to this decision</p>';
														showSegmentPath($nsegmentID);
													}else{
														////
													}
													*/
									
												}else if( $decision->getContinues() ){ 
													echo '<p>This option <b>Continues</b> the tour after playing this clip: ';
													if($decision->getClipID() != 0 && $decision->getClipID() != ""){
														echo '<a class="button" onClick="previewClip('.$decision->getClipID().')"><i class="fa fa-play"></i> Preview</a></p>';
													}else{
														echo '<b>INVALID CLIP</b></p>';
													}
													$nextSegment = new Segments($conn);
													$nextSegment = $nextSegment->load( $decision->getSegmentID() );
													
													if( is_object($nextSegment) ){
														showSegmentPath( $decision->getSegmentID() );
													}else{
														echo '<p>Could not load the segment. This is likely an error. Since this video continues, it should lead to another set of Questions. Consider Assigning a decision Group, or making this choice end the tour, or try again.</p>';
													}
												}else{	
													echo '<p>This option forces the user to <b>try again</b> after playing this clip: ';
													
													if($decision->getClipID() != 0 && $decision->getClipID() != ""){
														echo '<a class="button" onClick="previewClip('.$decision->getClipID().')"><i class="fa fa-play"></i> Preview</a></p>';
													}else{
														echo '<b>INVALID CLIP</b></p>';
													}
													
													if( $decision->getSegmentID() != $segmentID ){
														echo '<p>This clip is supposed to force a try again, but it leads to a different video segway - which may be an error.</p>';
														showSegmentPath($decision->getSegmentID());
													}
												}
											echo '</li>';
										}else{	
											//not object
											echo '<li class="decision_error"><p>Error with Decision</p></li>';
										}
									}// end for
								echo '</ul>';
							}else{
								echo '<p>This is <b>not</b> followed by a list of questions. Add a set!</p>';
							}
					}else{
						//not object
						echo "<p>Can't load Segment  ".$segmentID.'</p>';
						echo '<pre>'.print_r($segment, true).'</pre>';	
					}
				}
				//pop off the visited one
				//in_array( $segmentID, $visitedSegments )
				//remove $segmentID from $visitedSegments
				global $HIDE_DUPES;
				if( $HIDE_DUPES ){
				
					if(($key = array_search($segmentID, $visitedSegments)) !== false) {
						unset($visitedSegments[$key]);
					}
				
				}
				
			echo '</ul>';
		}

		$segmentID = $project->getStartingSegmentID();
		if( $segmentID != 0 && $segmentID != "" ){
			showSegmentPath($segmentID);
		}else{
			echo "<p>No Starting Segment Id! Error!</p>";
		}
		?>
		<?php footerMenu($projID); ?>
		<?php
	}else{
		echo "<h1>Error: a project ID is required</h1>";
	}
	pageFooter();
?>