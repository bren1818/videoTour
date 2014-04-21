<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
$fail = 0;	
if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	

	if( isset( $_POST['projectID'] ) && $_POST['projectID'] != "" && isset( $_POST['decisionTreeID'] ) && $_POST['decisionTreeID'] ){
		$projectID = 		$_POST['projectID'];
		$decisionTreeID = 	$_POST['decisionTreeID'];
		$segmentID = 		$_POST['segmentID'];
		$note =  			$_POST['note'];
		$buttonText = 		$_POST['buttonText'];
		$continues =		0;
		$ends = 			0;
		$clipID =			$_POST['clipID'];
		$order =			$_POST['order'];
		
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
		$decision->setProjectID( $projectID );
		$decision->setDecisionTreeID( $decisionTreeID );
		$decision->setClipID( $clipID );
		$decision->setSegmentID( $segmentID );
		$decision->setContinues( $continues );
		$decision->setEnds( $ends );
		$decision->setNote( $note );
		$decision->setText( $buttonText );
		$decision->setOrder( $order );
		$decision->save();
	

		if( isset( $_POST["saveAndAdd"] ) ){
			//just a flag ...
		
		}else if( isset( $_POST["saveAndFinish"] ) ){
			// show message here
			pageHeader();
			if( $decision->getId() > 0 ){
				echo '<p>Decision saved Successfully, ID: '.$decision->getId().'</p>';	
			}else{
				echo '<p>Could Not Add Decision... an error occurred... try again later?</p>';
			}
			?>
				<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projectID; ?>" class="button"><i class="fa fa-reply"></i> Go Back</a></p>
			<?php
			pageFooter();
		}
	}else{
		$fail = 1;
		pageHeader();
		?>
		<h1>ERROR!</h1>
		<p>No Project ID or DecisionTree ID</p>
		<?php
		if( $projectID != "" ){
			?>
			<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projectID; ?>" class="button"><i class="fa fa-reply"></i> Go Back</a></p>
			<?php
		}
		pageFooter();
	}
}

if( $_SERVER['REQUEST_METHOD'] != 'POST' || isset( $_POST["saveAndAdd"] ) && $fail == 0  ){

	pageHeader();

	if( isset( $_POST["saveAndAdd"] ) ){
		//show insert message here
		//echo '<pre>'.print_r( $_POST, true ).'</pre>';
		
		if( $decision->getId() > 0 ){
				echo '<p>Decision saved Successfully, ID: '.$decision->getId().'</p>';	
		}else{
				echo '<p>Could Not Add Decision... an error occurred... try again later?</p>';
		}
		
	
		$projectID = $_POST['projectID'];
		$decisionTreeID =  $_POST['decisionTreeID'];
	}else{
		$projectID = $_REQUEST['projectID'];
		$decisionTreeID =$_REQUEST['decisionTreeID'];
	}
	
	
	if( isset($projectID) && $projectID != "" &&
		isset($decisionTreeID) && $decisionTreeID != ""
	){
		
	$conn = getConnection();
	
	$projectID = $_REQUEST['projectID'];
	$decisionTreeID = $_REQUEST['decisionTreeID'];
	
	
	$project = new Projects($conn);
	$project = $project->load($projectID);
	
	$dt = new DecisionTree($conn);
	$dt = $dt->load( $decisionTreeID );
	
	$choice = new Decisions($conn);
	$choices = $choice->getList( $dt->getId() );
	
	
	?>
	<h1>Add Decision to: '<?php echo $dt->getTitle(); ?>' Group in the '<?php echo $project->getTitle() ?>' project</h1>
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
			echo '<tr><td>'.$c->getClipID().' <a class="button" onClick="previewClip('.$c->getClipID().')"><i class="fa fa-play"></i> Preview</a></td><td>'.( $c->getContinues() == true ? "Continues" : (   $c->getEnds()  == true ? "Ends" : "Incorrect Answer go back" )).'</td><td>'.$c->getNote().'</td><td>'.$c->getSegmentID().'</td></tr>';
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
	
	<form id="add_decision" method="post" action="add.php">
		<table>
			
			<tr>
				<td valign="top">
					<label for="clipName">Choose Clip:</label><br /> <a id="btn_clipID" class="button" onclick="selectClip(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
					
				</td>
				<td valign="top" id="triggerUpdateClip">
					<input type="text" onChange="updateDesc('clip',this)" id="clipID" name="clipID" value="" readonly />
					<button class="button wa clearInput">Clear</button> 
					<p>If this will be a "Continue Tour" Option the segment chosen should be the "Correct" video for this set of decisions</p>
				</td>
			</tr> 
			<tr>
				<td valign="top">
					<label for="segmentID">Choose Segment:</label><br /> <a id="btn_segmentID" class="button" onclick="selectSegment(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
					
				</td>
				<td valign="top" id="triggerUpdateSegment">
					<input type="text" onChange="updateDesc('segment',this)" id="segmentID" name="segmentID" value="" readonly />
					<button class="button wa clearInput">Clear</button> 
					<!-- on change load the description??? -->
					<p>If this will be a "Continue Tour" Option the segment chosen should be the next set of questions. If it is neither a "Continue Tour" or "End Tour" - It's the "Wrong/try again answer" option the segment should go to this decision Tree(ID: <b><?php	echo $dt->getId();	?></b>)
					</p>
					
				</td>
			</tr>
			<tr>
				<td>
					<label for="continues">Continues Tour</label>
				</td>
				<td>
					<input id="continues" name="continues" type="checkbox" value="1" /><p>Check this box if choosing this decision takes you to the next logical step in the video tour. Think of this as the "right" answer</p>
				</td>
			</tr>
			<tr>
				<td>
					<label for="ends">Ends Tour</label>
				</td>
				<td>
					<input id="ends" name="ends" type="checkbox" value="1" /><p>Check this box if choosing this decision takes you to the end of video tour. Think of this as the "closing" statement / thank you for the participation</p>
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
					<textarea id="note" name="note" required="required" placeholder="This decision goes to step 4"></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="buttonText">
						Text for the Button the user Clicks on
					</label>
				</td>
				<td valign="top">
					<input id="buttonText" name="buttonText" type="text" required="required">
				</td>
			</tr>
			
			<tr>
				<td valign="top">
					<label for="order">
						Order
					</label>
				</td>
				<td valign="top">
					<input id="order" name="order" type="number" required="required" value="0">
				</td>
			</tr>
			
			
		</table>
		<input type="hidden" name="projectID" value="<?php echo $project->getId(); ?>" />
		<input type="hidden" name="decisionTreeID" value="<?php echo $dt->getId(); ?>" />
		<!--
			DECISION ID??
		-->
		
		<p class="center">
		<?php  if( sizeof( $choices ) < 2 ){ ?> <input class="button wa" name="saveAndAdd" type="submit" value="Submit and Add another"/> <?php } ?> <input class="button wa" name="saveAndFinish" type="submit" value="Save & Finish" /></p>
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