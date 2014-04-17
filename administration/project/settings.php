<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	pageHeader();
	
	$conn = getConnection();
	$project = new Projects($conn);
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		$title = 		isset( $_POST['title'] ) ? ($_POST['title'] == "" ? "Namless Project :(" : $_POST['title']) :  $_POST['title'];
		$active = 		isset( $_POST['active'] ) ?  "1" : "0";
		$showBadge =  	isset( $_POST['showBadge'] ) ?  "1" : "0";
		$showCount =  	isset( $_POST['showCount'] ) ?  "1" : "0";
		$hasForm =  	isset( $_POST['hasForm'] ) ?  "1" : "0";
		$formURL =  	isset( $_POST['formURL'] ) ?  $_POST['formURL'] : "";
		$redirect = 	isset( $_POST['redirect'] ) ?  "1" : "0";
		$redirectURL =  isset( $_POST['redirectURL'] ) ?  $_POST['redirectURL'] : "";
		$projID  =  	isset( $_POST['projectID'] ) ?  $_POST['projectID'] : "";
	
		$project = $project->load($projID);
		$project->setTitle( $title );
		$project->setActive( $active );
		$project->setShowBadge( $showBadge );
		$project->setShowCount( $showCount );
		$project->setHasForm( $hasForm );
		$project->setFormURL( $formURL );
		$project->setRedirect( $redirect );
		$project->setRedirectURL( $redirectURL );
	
		if( $project->save() > 0 ){
			echo "<p>Saved!</p>";
		}else{
			echo "<p>Error! Could Not save!</p>";
		}	
	}
	
	
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projectID = $_REQUEST['projectID'];
		
		
		
		$project = $project->load($projectID);

		if( is_object($project) ){
		
		
?>
	<style>
		form{
			width: 500px;
		}
			.formRow{
				clear: both;
				width: 100%;
				padding: 3px 0px;
			}
			
				.col{
					float: left;
					display: block;
					margin: 0px;
				}
					.colLabel label{
						
					}
					
					.colLabel label i{
						
					}
					
					.col.col50{
						width: 50%;
					}
		
	</style>


		<h1>Project Settings</h1>
		<a class="button wa" target="_blank" href="<?php echo fixedPath; ?>/administration/project/css?projectID=<?php echo $projectID;?>">Edit CSS</a>
		<form name="projectSettings" method="post" action="settings.php?projectID=<?php echo $projectID; ?>">
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="title"><i class="fa fa-pencil"></i> Title:</label>
				</div>
				<div class="col col50">
					<input type="text" name="title" id="title" value="<?php echo $project->getTitle(); ?>"/>
				</div>
			</div>
		
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="active"><i class="fa fa-thumbs-up"></i> Project is Active:</label>
				</div>
				<div class="col col50">
					<input type="checkbox" name="active" id="active" <?php if( $project->isActive() ){echo "checked"; } ?>/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="showBadge"><i class="fa fa-shield"></i> Show Badges: </label>
				</div>
				<div class="col col50">
					<input type="checkbox" name="showBadge" id="showBadge" <?php if( $project->getShowBadge() ){echo "checked"; } ?>/>
				</div>
			</div>
		
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="showBadge"><i class="fa fa-sort-numeric-asc"></i> Show Step: </label>
				</div>
				<div class="col col50">
					<input type="checkbox" name="showCount" id="showCount" <?php if( $project->getShowCount() ){echo "checked"; } ?>/>
				</div>
			</div>
			
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="hasForm"><i class="fa fa-tasks"></i> Show a Form: </label>
				</div>
				<div class="col col50">
					<input type="checkbox" name="hasForm" id="hasForm" <?php if( $project->getHasForm() ){echo "checked"; } ?>/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="formURL"><i class="fa fa-pencil"></i> Form URL:</label>
				</div>
				<div class="col col50">
					<input type="url" name="formURL" id="formURL" value="<?php echo $project->getFormURL(); ?>"/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="redirect"><i class="fa fa-external-link"></i> Redirect after Completion?: </label>
				</div>
				<div class="col col50">
					<input type="checkbox" name="redirect" id="redirect" <?php if( $project->getRedirect() ){echo "checked"; } ?>/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="redirectURL"><i class="fa fa-pencil"></i> Redirect URL:</label>
				</div>
				<div class="col col50">
					<input type="url" name="redirectURL" id="redirectURL" value="<?php echo $project->getRedirectURL(); ?>"/>
				</div>
			</div>
			<input type="hidden" name="projectID" value="<?php echo $projectID; ?>" />
			<input class="button wa" type="submit" value="Save!" />
			
			
		</form>
		
		<br />
		<br />
<?php
		$projID =  $project->getId(); 
	  footerMenu($projID);
	

		}
	
	}
?>