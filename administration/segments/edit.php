<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";

	$saved = 0;
if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	
	
	
	$clipID = (isset($_POST['clipID'])  ? (  $_POST['clipID'] == "" ? "-1" :  $_POST['clipID']  ) : -1);
	$decisionTreeID = (isset($_POST['decisionTreeID'])  ? (  $_POST['decisionTreeID'] == "" ? "-1" :  $_POST['decisionTreeID']  ) : -1);
	$description = (isset($_POST['description'])  ? (  $_POST['description'] == "" ? "-1" :  $_POST['description']  ) : -1);
	$projectID =  (isset($_POST['projectID'])  ? (  $_POST['projectID'] == "" ? "-1" :  $_POST['projectID']  ) : -1);
	
	$segmentID =  (isset($_POST['segmentID'])  ? (  $_POST['segmentID'] == "" ? "-1" :  $_POST['segmentID']  ) : -1);
	$badgeID =  (isset($_POST['badgeID'])  ? (  $_POST['badgeID'] == "" ? "0" :  $_POST['badgeID']  ) : 0);

	
	if( $projectID == "" || $projectID == -1 ||  $segmentID == "" || $segmentID == -1){
		
		//No project ID - fail
		echo "No Project or Segment ID. Fail.";
		
	}else{
		$conn = getConnection();
		$segment = new Segments( $conn );
		if( isset($_POST['segmentID']) && $_POST['segmentID'] != "" ){ 
			//$segment->setId( $_POST['segmentID'] );
			$segment = $segment-> load($segmentID);
		}
		$segment->setClipID( $clipID);
		//$segment->setProjectID($projectID);
		$segment->setNote($description);
		$segment->setDecisionTreeID( $decisionTreeID);
		$segment->setBadge( $badgeID );
		$segment->save();
		
		$saved = 1;
		
	}
	
	
}

	pageHeader();

	if( (isset($_REQUEST['projectID']) && $_REQUEST['projectID'] &&
		isset($_REQUEST['segmentID']) && $_REQUEST['segmentID']) 
		
		||
		
		( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($POST['projectID']) &&  isset($POST['segmentID'])  )
		
		
	){
	
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			$projectID =  (isset($_POST['projectID'])  ? (  $_POST['projectID'] == "" ? "-1" :  $_POST['projectID']  ) : -1);
	
			$segmentID =  (isset($_POST['segmentID'])  ? (  $_POST['segmentID'] == "" ? "-1" :  $_POST['segmentID']  ) : -1);
		}else{
	
			$projectID = $_REQUEST['projectID'];
			$segmentID = $_REQUEST['segmentID'];
		
		}
		
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projectID);
		
		$segment = new Segments( $conn );
		$segment = $segment->load( $segmentID );
		
	
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
			
			$('#triggerUpdateBadge').click(function(event){
				event.preventDefault();
				$('#btn_badgeID').trigger( "click" );
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
		
		#clipID, #decisionTreeID,#badgeID{
			background-color: #ccc;
		}
	
	</style>

	
	<h1>Edit Segment in: '<?php echo $project->getTitle(); ?>'</h1>
	
	<?php if( $saved ){ ?>
	<p><b>Saved Ok!</b></p>
	<?php } ?>
	
	<div id="dialog"></div>
	
	<form id="add_segment" method="post" action="edit.php">
		<p>Edit a segment. Clips/Decision Groups can be assigned later if they're not created yet, but a description is required.</p> 
		<table>
			<tr>
				<td valign="top">
					<label for="clipName">Choose Clip:<br /> <a id="btn_clipID" class="button" onclick="selectClip(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
					</label>
				</td>
				<td valign="top" id="triggerUpdateClip">
					<input type="text" onChange="updateDesc('clip',this)" id="clipID" name="clipID" value="<?php echo $segment->getClipID(); ?>" readonly /><button class="button wa clearInput">Clear</button> 
					<!-- on change load the description??? -->
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="decisionTree"> Choose Decision Group:<br /> <a id="btn_decisionTreeID" class="button" onclick="selectDecisionTree(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
												
					</label>
				</td>
				<td valign="top" id="triggerUpdateDecisionTree">
					<input type="text" onChange="updateDesc('decisionTree',this)" id="decisionTreeID" name="decisionTreeID" value="<?php echo $segment->getDecisionTreeID(); ?>" readonly /><button class="button wa clearInput">Clear</button> 
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
					<textarea id="description" name="description" required="required"><?php echo $segment->getNote(); ?></textarea>
				</td>
			</tr>
			
			<tr>
				<td valign="top">
					<label for="badge">Completion Badge:<br /> <a id="btn_badgeID" class="button" onclick="selectBadge(<?php echo $project->getId(); ?>)"><i class="fa fa-folder-open"></i></a>
												
					</label>
				</td>
				<td valign="top" id="triggerUpdateBadge">
					<input type="text" onChange="updateDesc('badge',this)" id="badgeID" name="badgeID" value="<?php echo $segment->getBadge(); ?>" readonly /><button class="button wa clearInput">Clear</button> 
					<!-- on change load the description??? -->
				</td>
			</tr>
			
			
		</table>
		<input type="hidden" name="projectID" value=<?php echo $segment->getProjectID(); ?>" />
		<input type="hidden" name="segmentID" value="<?php echo $segment->getId(); ?>" />
		<!--Segment ID???--->
		<p class="center"><input type="submit" class="button wa" value="Update Segment"/></p>
		
	</form>
	
	
	<p><a href="/administration/project/edit?id=<?php echo $segment->getProjectID(); ?>" class="button"><i class="fa fa-reply"></i> Go Back</a></p>

<?php
	}else{
		?>
		<h1>No ProjectID specified...</h1>
		
		<p><a class="button" href="/admin"><i class="fa fa-reply"></i> Go Back</a></p>
		<?php
	}
	pageFooter();

?>