<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
$saved = 0;	
if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	

	if( isset( $_POST['projectID'] ) && $_POST['projectID'] != "" && isset( $_POST['decisionID'] ) && $_POST['decisionID'] != "" ){
	
		$decisionID = $_POST['decisionID'];
		$segmentID = 		$_POST['segmentID'];
		$note =  			$_POST['note'];
		$buttonText = 		$_POST['buttonText'];
		$continues =		0;
		$ends = 			0;
		$clipID =			$_POST['clipID'];
		
		//continues
		if( isset( $_POST['continues']) ){
			$continues = 		1;
		}
		
		//ends
		if( isset( $_POST['ends']) ){
			$ends =	1;
		}
		
		if( isset( $_POST['ends']) && isset( $_POST['continues']) ){
			$continues = 0;
			$ends = 0;
		}
		
		$conn = getConnection();
		
		
		$decision = new Decisions( $conn );
		$decision = $decision->load( $decisionID );
		$decision->setClipID( $clipID );
		$decision->setSegmentID( $segmentID );
		$decision->setContinues( $continues );
		$decision->setEnds( $ends );
		$decision->setNote( $note );
		$decision->setText( $buttonText );
		$decision->save();
	
		$saved = 1;	

	}
}




if( $_SERVER['REQUEST_METHOD'] != 'POST' ||  $saved 	){

	pageHeader();
	if( $saved ){
		$projectID = $_POST['projectID'];
		$decisionID =$_POST['decisionID'];
	}else{
		$projectID = $_REQUEST['projectID'];
		$decisionID =$_REQUEST['decisionID'];
	}

	
	if( isset($projectID) && $projectID != "" &&
		isset($decisionID) && $decisionID != ""
	){
		
	$conn = getConnection();
	
	$project = new Projects($conn);
	$project = $project->load($projectID);
	
	$choice = new Decisions($conn);
	$choice = $choice->load( $decisionID );
	
	$decisionTreeID = $choice->getDecisionTreeID();
	
	$dt = new DecisionTree($conn);
	$dt = $dt->load( $decisionTreeID );
	
	$choices = new Decisions($conn);
	
	//get segment which  which decisiontree id = $decisionTreeID
	$segment = new Segments($conn);	
	$segment = $segment->getbyDecisionTreeID( $decisionTreeID );
	
	
	
	//echo '<pre>'.print_r( $segment, true ).'</pre>';

	
	
	$choices = $choices->getList(  $decisionTreeID );
	
	
	?>
	<h1>Edit Decision to: '<?php echo $dt->getTitle(); ?>' Group in the '<?php echo $project->getTitle() ?>' project</h1>
	<p><b>Decision Group Step</b>: <?php echo $dt->getStep();  ?></p>
	<p><b>Decision Group Description</b>: <?php echo $dt->getNote(); ?></p>
	<?php
	if( sizeof( $choices ) > 0 ){
	?>
	<p><b>Existing Decisions in this group:</b></p>
	
	<table class="tablesorter">
	<thead> 
		<tr> 
			<th>Plays Clip id</th> 
			<th>Continues/Ends</th> 
			<th>Note</th> 
			<th>Go to Segment</th>
		</tr> 
	</thead>
	<tbody> 
	<?php
		foreach( $choices as $c ){
			
			if( $c->getId() != $decisionID ){
		
			echo '<tr><td>'.$c->getClipID().' <a class="button" onClick="previewClip('.$c->getClipID().')"><i class="fa fa-play"></i> Preview</a></td><td>'.( $c->getContinues() == true ? "Continues" : (   $c->getEnds()  == true ? "Ends" : "Incorrect Answer go back" )).'</td><td>'.$c->getNote().'</td><td>'.$c->getSegmentID().'</td></tr>';
			
			}
		}
	?>
	</tbody>
	</table> 
	<?php
		}
	?>
	<script>
		$(function(){
			$('#triggerUpdateSegment').click(function(event){
				$('#btn_segmentID').click();
			});
			
			$('.clearInput').click(function(event){
				event.preventDefault();
				event.stopPropagation();
				$(this).parent().find('input').val("");
			});
		});
	</script>
	
	<style>
		#triggerUpdateClip, #triggerUpdateSegment{
			cursor: pointer;
		}
		
		#clipID, #segmentID{
			background-color: #ccc;
		}
	
	</style>

	
	
	<div id="dialog"></div>
	
	<form id="edit_decision" method="post" action="edit.php">
		<table>
			
			<tr>
				<td valign="top">
					<label for="clipName">Choose Clip:</label><br /> <a id="btn_clipID" class="button" onclick="selectClip(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
					
				</td>
				<td valign="top" id="triggerUpdateClip">
					<input type="text" onChange="updateDesc('clip',this)" id="clipID" name="clipID" value="<?php echo $choice->getClipID(); ?>" readonly />
					<button class="button wa clearInput">Clear</button> 
					<p>If this will be a "Continue Tour" Option the segment chosen should be the "Correct" video for this set of decisions</p>
				</td>
			</tr>
			
			<tr>
				<td valign="top">
					<label for="segmentID">Choose Segment:</label><br /> <a id="btn_segmentID" class="button" onclick="selectSegment(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
					
				</td>
				<td valign="top" id="triggerUpdateSegment">
					<input type="text" onChange="updateDesc('segment',this)" id="segmentID" name="segmentID" value="<?php echo $choice->getSegmentID(); ?>" readonly />
					<button class="button wa clearInput">Clear</button> 
					<!-- on change load the description??? -->
					<p>If this will be a "Continue Tour" Option the segment chosen should be the next set of questions. If it is neither a "Continue Tour" or "End Tour" - It's the "Wrong/try again answer" option the segment should go to this Segment (ID: <b><?php	
						if( is_object( $segment ) ){
						echo $segment->getId();
						}
						//echo $dt->getId(); //segment which points to the dtree if



					?></b>)
					</p>
					
				</td>
			</tr>
			<tr>
				<td>
					<label for="continues">Continues Tour</label>
				</td>
				<td>
					<input id="continues" name="continues" type="checkbox" value="1" <?php if($choice->getContinues()){ echo "checked"; } ?>/><p>Check this box if choosing this decision takes you to the next logical step in the video tour. Think of this as the "right" answer</p>
				</td>
			</tr>
			<tr>
				<td>
					<label for="ends">Ends Tour</label>
				</td>
				<td>
					<input id="ends" name="ends" type="checkbox" value="1" <?php if( $choice->getEnds() ){ echo "checked"; } ?> /><p>Check this box if choosing this decision takes you to the end of video tour. Think of this as the "closing" statement / thank you for the participation</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<p>Checking Neither Continues Tour or Ends Tour is valid, this results in the choice being a "Wrong" answer and brings the user back to the initial question set to try again.</p><p>Checking BOTH Continues and Ends will result in a condition not met, so it will be treated as a try again.</p>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="note">
						Note on this Decision:
					</label>
				</td>
				<td valign="top">
					<textarea id="note" name="note" required="required" placeholder="This decision goes to step 4"><?php echo $choice->getNote(); ?></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="buttonText">
						Text for the Button the user Clicks on
					</label>
				</td>
				<td valign="top">
					<input id="buttonText" name="buttonText" type="text" required="required" value="<?php echo $choice->getText(); ?>">
				</td>
			</tr>
		</table>
		<input type="hidden" name="projectID" value="<?php echo $project->getId(); ?>" />
		<input type="hidden" name="decisionTreeID" value="<?php echo $dt->getId(); ?>" />
		<input type="hidden" name="decisionID" value="<?php echo $decisionID; ?>" />
		<!--
			DECISION ID??
		-->
		
		<p class="center">
		<input class="button" type="submit" value="SAVE" />
		</p>
		<p><a class="button" href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $project->getId();  ?>"><i class="fa fa-reply"></i> Go Back</a></p>
	</form>
	
	
	
	
	
	
	
	
	<?php
	}else{
		?>
		<h1>No ProjectID or Decision ID specified...</h1>
		
		<p><a class="button" href="<?php echo fixedPath; ?>/admin"><i class="fa fa-reply"></i> Go Back</a></p>
		<?php
	}
		pageFooter();
}	
?>