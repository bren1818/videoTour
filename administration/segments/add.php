<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	
if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	
	//echo "<pre>".print_r( $_POST, true ).'</pre>';
	
	$clipID = (isset($_POST['clipID'])  ? (  $_POST['clipID'] == "" ? "-1" :  $_POST['clipID']  ) : -1);
	$decisionTreeID = (isset($_POST['decisionTreeID'])  ? (  $_POST['decisionTreeID'] == "" ? "-1" :  $_POST['decisionTreeID']  ) : -1);
	$description = (isset($_POST['description'])  ? (  $_POST['description'] == "" ? "-1" :  $_POST['description']  ) : -1);
	$projectID =  (isset($_POST['projectID'])  ? (  $_POST['projectID'] == "" ? "-1" :  $_POST['projectID']  ) : -1);
	$badgeID =  (isset($_POST['badgeID'])  ? (  $_POST['badgeID'] == "" ? "0" :  $_POST['badgeID']  ) : 0);

	
	if( $projectID == "" || $projectID == -1){
		
		//No project ID - fail
		echo "No Project ID. Fail.";
	}else{
		$conn = getConnection();
		$segment = new Segments( $conn );
		if( isset($_POST['segmentID']) && $_POST['segmentID'] != "" ){ //////////////
			$segment->setId( $_POST['segmentID'] );
		}
		$segment->setClipID( $clipID);
		$segment->setProjectID($projectID);
		$segment->setNote($description);
		$segment->setDecisionTreeID( $decisionTreeID);
		$segment->setBadge( $badgeID );
		$segment->save();
		pageHeader();
		if( $segment->getId() > 0 ){
			echo "<p>Segment Saved! Segment ID: ". $segment->getId()."</p>";
		}else{
			echo "<p>Error! Segment not Saved! :( Segment ID: ". $segment->getId()."</p>";
		}
		
		?>
		<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projectID; ?>" class="button"><i class="fa fa-reply"></i> Go Back</a></p>
		
		<?php
		
		
		pageFooter();
	}
	
	
}else{
	
	
	pageHeader();

	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projectID = $_REQUEST['projectID'];
		
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projectID);
	
	
	//edit mode????
?>
	<script>
		$(function(){
			$('#triggerUpdateClip').click(function(event){
				event.preventDefault();
				$('#btn_clipID').trigger( "click" );
				
			});
			
			$('#triggerUpdateDecisionTree').click(function(event){
				event.preventDefault();
				$('#btn_decisionTreeID').trigger( "click" );
			});
			
			$('.clearInput').click(function(event){
				event.preventDefault();
				event.stopPropagation();
				$(this).parent().find('input').val("");
			});
			
		});
	</script>
	
	<style>
		#triggerUpdateClip, #triggerUpdateDecisionTree{
			cursor: pointer;
		}
		
		#clipID, #decisionTreeID, #badgeID{
			background-color: #ccc;
		}
	
	</style>

	
	<h1>Add Segment to: '<?php echo $project->getTitle(); ?>'</h1>
	
	<div id="dialog"></div>
	
	<form id="add_segment" method="post" action="add.php">
		<p>Setup a segment. Clips/Decision Groups can be assigned later if they're not created yet, but a description is required.</p> 
		<table>
			<tr>
				<td valign="top">
					<label for="clipName">Choose Clip:<br /> <a id="btn_clipID" class="button" onclick="selectClip(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
					</label>
				</td>
				<td valign="top" id="triggerUpdateClip">
					<input type="text" onChange="updateDesc('clip',this)" id="clipID" name="clipID" value="" readonly /><button class="button wa clearInput">Clear</button> 
					<!-- on change load the description??? -->
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="decisionTree"> Choose Decision Group:<br /> <a id="btn_decisionTreeID" class="button" onclick="selectDecisionTree(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
												
					</label>
				</td>
				<td valign="top" id="triggerUpdateDecisionTree">
					<input type="text" onChange="updateDesc('decisionTree',this)" id="decisionTreeID" name="decisionTreeID" value="" readonly /><button class="button wa clearInput">Clear</button> 
					<!-- on change load the description??? -->
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="description">
						Description for this Segment:
					</label>
				</td>
				<td valign="top">
					<textarea id="description" name="description" required="required"></textarea>
				</td>
			</tr>
			
		<tr>
				<td valign="top">
					<label for="badge">Completion Badge:<br /> <a id="btn_badgeID" class="button" onclick="selectBadge(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
												
					</label>
				</td>
				<td valign="top" id="triggerUpdateBadge">
					<input type="text" id="badgeID" name="badgeID" value="<?php  ?>" readonly /><button class="button wa clearInput">Clear</button> 
					<!-- on change load the description??? -->
				</td>
			</tr>
			
		</table>
		<input type="hidden" name="projectID" value=<?php echo $projectID; ?>" />
		<!--Segment ID???--->
		<p class="center"><input type="submit" class="button wa" value="Add Segment"/></p>
		
	</form>
	
	
	<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $project->getId(); ?>" class="button"><i class="fa fa-reply"></i> Go Back</a></p>

<?php
	}else{
		?>
		<h1>No ProjectID specified...</h1>
		
		<p><a class="button" href="<?php echo fixedPath; ?>/admin"><i class="fa fa-reply"></i> Go Back</a></p>
		<?php
	}
	pageFooter();
}
?>