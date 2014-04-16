<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	pageHeader();

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
		if( isset($_POST['decisionGroupID']) && $_POST['decisionGroupID'] != "" ){//////////////
			$dt->setId( $_POST['decisionGroupID'] );
		}
		$dt->setProjectID( $projectID );
		$dt->setNote( $note );
		$dt->setTitle( $title );
		$dt->setStep( $step );
		
		$dt->save();
	
		pageHeader();
		if( $dt->getId() > 0 ){
			echo "<p>Decision Group Saved! Decision Group ID: ". $dt->getId()."</p>";
		}else{
			echo "<p>Error! Decision Group not Saved! :( Decision Group ID: ". $dt->getId()."</p>";
		}
		
		?>
		<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projectID; ?>" class="button"><i class="fa fa-reply"></i> Go Back</a></p>
		
		<?php
	
	
	}
}else{

	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projectID = $_REQUEST['projectID'];
		
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projectID);
	
	
?>
	<h1>Add Decision Group</h1>

	<p>Each input is required, but the step is optional. It's just for a note about where you are in the project</p>
	<form id="add_decisionTree" method="post" action="add.php">
		<table>
			<tr>
				<td valign="top">
					<label for="title">Question/Statement</label>
				</td>
				<td>
					<input type="text" name="title" id="title" value="" required="required"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="note">Note about Group</label>
				</td>
				<td>
					<textarea id="note" name="note" placeholder="EG this starts the show and goes to clip" required="required"></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="step">(optional) Step</label>
				</td>
				<td>
					<input name="step" id="step" value="0" type="number"/>
					<p>This is helpful for sorting your decision groups but it's a just a marker. It should however match your "badges"</p>
				</td>
			</tr>
		</table>
		<input type="hidden" name="projectID" id="projectId" value="<?php echo $projectID; ?>" />
		<p class="center"><input type="submit" class="button wa" value="Add Decision Group"/></p>
		
		
		
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