<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	pageHeader();
$saved = 0;
if( $_SERVER['REQUEST_METHOD'] == 'POST' ){

	$title = (isset($_POST['title'])  ? (  $_POST['title'] == "" ? "" :  $_POST['title']  ) : "");
	$note = (isset($_POST['note'])  ? (  $_POST['note'] == "" ? "" :  $_POST['note']  ) : "");
	$step = (isset($_POST['step'])  ? (  $_POST['step'] == "" ? "0" :  $_POST['step']  ) : "0");
	$projectID =  (isset($_POST['projectID'])  ? (  $_POST['projectID'] == "" ? "" :  $_POST['projectID']  ) : -1);
	
	
	
	if( $projectID == "" || $projectID == -1){
		echo "No Project ID. Fail.";
	}else{
		$conn = getConnection();
		$dt = new DecisionTree($conn);
		if( isset($_POST['decisionTreeID']) && $_POST['decisionTreeID'] != "" ){//////////////
			//$dt->setId( $_POST['decisionGroupID'] );
			$dt = $dt->load( $_POST['decisionTreeID'] );
		}
		//$dt->setProjectID( $projectID );
		$dt->setNote( $note );
		$dt->setTitle( $title );
		$dt->setStep( $step );
		
		$dt->save();
		$save = 1;
		
	
		pageHeader();
		if( $dt->getId() > 0 ){
			echo "<p>Decision Group Saved!</p>";
		}else{
			echo "<p>Error! Decision Group not Saved! :( </p>";
		}
	}
}



	if( (isset($_REQUEST['projectID']) && $_REQUEST['projectID'] &&
		isset($_REQUEST['decisionTreeID']) && $_REQUEST['decisionTreeID'] ) || $saved	){
		$projectID = $_REQUEST['projectID'];
		$decisionTreeID =  $_REQUEST['decisionTreeID'];
		
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projectID);
		
		$DecisionTree = new DecisionTree($conn);
		$DecisionTree = $DecisionTree->load($decisionTreeID);
	
	
?>
	<h1>Edit Decision Group</h1>

	<p>Each input is required, but the step is optional. It's just for a note about where you are in the project</p>
	<form id="add_decisionTree" method="post" action="edit.php">
		<table>
			<tr>
				<td valign="top">
					<label for="title">Question/Statement</label>
				</td>
				<td>
					<input type="text" name="title" id="title" value="<?php echo $DecisionTree->getTitle(); ?>" required="required"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="note">Note about Group</label>
				</td>
				<td>
					<textarea id="note" name="note" placeholder="EG this starts the show and goes to clip" required="required"><?php echo $DecisionTree->getNote(); ?></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="step">(optional) Step</label>
				</td>
				<td>
					<input name="step" id="step" value="<?php echo $DecisionTree->getStep(); ?>" type="number"/>
					<p>This is helpful for sorting your decision groups but it's a just a marker. It should however match your "badges"</p>
				</td>
			</tr>
		</table>
		<input type="hidden" name="projectID" id="projectId" value="<?php echo $projectID; ?>" />
		<input type="hidden" name="decisionTreeID" id="decisionTreeID" value="<?php echo $DecisionTree->getId(); ?>" />
		
		<p class="center"><input type="submit" class="button wa" value="Save Decision Group"/></p>
		
		
		
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

?>