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
		$badgeMode =    isset( $_POST['badgeMode'] ) ?  $_POST['badgeMode'] : 0;
		$showPoster =   isset( $_POST['showPoster'] ) ?  $_POST['showPoster'] : 0;
	
		$mobilePopUpButtonText  =  	isset( $_POST['mobilePopUpButtonText'] ) ?  $_POST['mobilePopUpButtonText'] : "OK";
		$mobilePopUpTitle  		=  	isset( $_POST['mobilePopUpTitle'] ) ?  $_POST['mobilePopUpTitle'] : "Welcome to the Mobile Tour";
		$mobilePopUpText  		=  	isset( $_POST['mobilePopUpText'] ) ?  $_POST['mobilePopUpText'] : "Welcome to the Mobile Tour, Click the play button to continue";
	
	
	
		//Popup before showing Form
		$ShowFormAlertText  		=  	isset( $_POST['ShowFormAlertText'] ) ?  $_POST['ShowFormAlertText'] : "Please fill out our form for a chance to win!";
		$ShowFormAlertTitle  		=  	isset( $_POST['ShowFormAlertTitle'] ) ?  $_POST['ShowFormAlertTitle'] : "Thank you for Playing";
		$ShowFormAlertButtonText  	=  	isset( $_POST['ShowFormAlertButtonText'] ) ?  $_POST['ShowFormAlertButtonText'] : "Enter Contest";
		
		//Popup before re-directing
		$RedirectAlertText  		=  	isset( $_POST['RedirectAlertText'] ) ?  $_POST['RedirectAlertText'] : "Thank you for playing you'll now be brought to our website";
		$RedirectAlertTitle  		=  	isset( $_POST['RedirectAlertTitle'] ) ?  $_POST['RedirectAlertTitle'] : "Thank your for Playing";
		$RedirectButtonText  		=  	isset( $_POST['RedirectButtonText'] ) ?  $_POST['RedirectButtonText'] : "OK";

		//If no redirect - finished
		$FinishAlertText  			=  	isset( $_POST['FinishAlertText'] ) ?  $_POST['FinishAlertText'] : "Thank you for playing. Hope you had fun!";
		$FinishAlertTitle   		=  	isset( $_POST['FinishAlertTitle'] ) ?  $_POST['FinishAlertTitle'] : "Thanks for Playing";
		$FinishAlertButtonText  	=  	isset( $_POST['FinishAlertButtonText'] ) ?  $_POST['FinishAlertButtonText'] : "Close";
		
		//Repeat Visitors who entered contest already and no redirect
		$RepeatAlertText  			=  	isset( $_POST['RepeatAlertText'] ) ?  $_POST['RepeatAlertText'] : "Looks like you already entered the contest! Thanks for playing!";
		$RepeatAlertTitle  			=  	isset( $_POST['RepeatAlertTitle'] ) ?  $_POST['RepeatAlertTitle'] : "Thanks for Playing!";
		$RepeatAlertButtonText  	=  	isset( $_POST['RepeatAlertButtonText'] ) ?  $_POST['RepeatAlertButtonText'] : "OK";

	
	
	
	
		$project = $project->load($projID);
		$project->setTitle( $title );
		$project->setActive( $active );
		$project->setShowBadge( $showBadge );
		$project->setBadgeMode( $badgeMode );
		$project->setShowCount( $showCount );
		$project->setHasForm( $hasForm );
		$project->setFormURL( $formURL );
		$project->setRedirect( $redirect );
		$project->setRedirectURL( $redirectURL );
		$project->setShowPoster( $showPoster );
		
		$project->setMobilePopUpButtonText($mobilePopUpButtonText);
		$project->setMobilePopUpTitle($mobilePopUpTitle);
		$project->setMobilePopUpText($mobilePopUpText);
		
		//Popup before showing Form
		$project->setShowFormAlertText($ShowFormAlertText);
		$project->setShowFormAlertTitle($ShowFormAlertTitle);
		$project->setShowFormAlertButtonText($ShowFormAlertButtonText);

		//Popup before re-directing
		$project->setRedirectAlertText($RedirectAlertText);
		$project->setRedirectAlertTitle($RedirectAlertTitle);
		$project->setRedirectButtonText($RedirectButtonText);

		//If no redirect - finished
		$project->setFinishAlertText($FinishAlertText);
		$project->setFinishAlertTitle($FinishAlertTitle);
		$project->setFinishAlertButtonText($FinishAlertButtonText);

		//Repeat Visitors who entered contest already and no redirect
		$project->setRepeatAlertText($RepeatAlertText);
		$project->setRepeatAlertTitle($RepeatAlertTitle);
		$project->setRepeatAlertButtonText($RepeatAlertButtonText);

		
	
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
		
		.formRow input[type="text"],
		.formRow textarea{
			min-width: 250px;
		}
		.formRow textarea{
			min-height: 50px;
		}
		
		
	</style>


		<h1>Project Settings</h1>
		<a class="button wa" target="_blank" href="<?php echo fixedPath; ?>/administration/project/css?projectID=<?php echo $projectID;?>">Edit CSS</a>
		<a class="button wa" target="_blank" href="<?php echo fixedPath; ?>/administration/project/js?projectID=<?php echo $projectID;?>">Edit JS</a>
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
					<label for="showBadge"><i class="fa fa-picture-o"></i> Show Intro Poster: </label>
				</div>
				<div class="col col50">
					<input type="checkbox" value="1" name="showPoster" id="showPoster" <?php if( $project->getShowPoster() ){echo "checked"; } ?>/> (If un-checked this will auto-start video if applicable)
				</div>
			</div>
			
			<?php
				if( $project->getShowPoster() ){
					if( $project->getPosterFile() != "" ){
				?>
						<p><a target="_blank" href="<?php echo fixedPath.$project->getPosterFile(); ?>"><i class="fa fa-eye"></i> View Poster</a></p>
			<?php	
					}
			?>
					<p><a target="_blank" href="<?php echo fixedPath; ?>/administration/project/poster?projectID=<?php echo $projectID; ?>"><i class="fa fa-download"></i> Upload/Change Poster</a></p>
			
			<?php
				}
			?>
			
			
			
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
					<label for="badgeMode"><i class="fa fa-cog"></i> Badge Mode: (to do)</label>
				</div>
				<div class="col col50">
					<input type="radio" name="badgeMode" value="0" <?php if( !$project->getBadgeMode() ){ echo " checked"; } ?>><i class="fa fa-circle"></i> Replace (show current badge only)<br>
					<input type="radio" name="badgeMode" value="1" <?php if( $project->getBadgeMode() ){ echo " checked"; } ?>><i class="fa fa-bars"></i> Append (add badges under one another )
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
			
			<br />
			<hr />
			<h2>PopUp/Instruction on Mobile Device</h2>
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="mobilePopUpText"><i class="fa fa-pencil"></i> Mobile PopUp Text (text in popup):</label>
				</div>
				<div class="col col50">
					<textarea name="mobilePopUpText" id="mobilePopUpText" maxlength="200" required="required"><?php echo $project->getMobilePopUpText(); ?></textarea>
				</div>
			</div>
			
				<div class="formRow">
				<div class="col col50 colLabel">
					<label for="mobilePopUpTitle"><i class="fa fa-pencil"></i> Mobile Popup Title Bar Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="mobilePopUpTitle" id="mobilePopUpTitle" value="<?php echo $project->getMobilePopUpTitle(); ?>" maxlength="50" required="required"/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="mobilePopUpButtonText"><i class="fa fa-pencil"></i> Mobile Popup Button Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="mobilePopUpButtonText" id="mobilePopUpButtonText" value="<?php echo $project->getMobilePopUpButtonText(); ?>" placeholder="OK" maxlength="20" required="required"/>
				</div>
			</div>
			
			
			
			
			<br />
			<hr />
			<h2>Pop before showing Form</h2>
			
			
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="ShowFormAlertText"><i class="fa fa-pencil"></i> Text in popup before showing form:</label>
				</div>
				<div class="col col50">
					<textarea name="ShowFormAlertText" id="ShowFormAlertText" maxlength="200" required="required" placeholder="Please complete this form for a chance to win.."><?php echo $project->getShowFormAlertText(); ?></textarea>
				</div>
			</div>
			
				<div class="formRow">
				<div class="col col50 colLabel">
					<label for="ShowFormAlertTitle"><i class="fa fa-pencil"></i>Popup Title Bar Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="ShowFormAlertTitle" id="ShowFormAlertTitle" value="<?php echo $project->getShowFormAlertTitle(); ?>" maxlength="50" required="required" placeholder="Enter for a chance to win"/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="ShowFormAlertButtonText"><i class="fa fa-pencil"></i> Mobile Popup Button Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="ShowFormAlertButtonText" id="ShowFormAlertButtonText" value="<?php echo $project->getShowFormAlertButtonText(); ?>" placeholder="Enter Contest" maxlength="20" required="required"/>
				</div>
			</div>
			
			
			
			
			<br />
			<hr  />
			<h2>Popup before Redirecting</h2>
			
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="RedirectAlertText"><i class="fa fa-pencil"></i> Text in popup before redirect:</label>
				</div>
				<div class="col col50">
					<textarea name="RedirectAlertText" id="RedirectAlertText" maxlength="200" required="required" placeholder="Thanks for playing you'll now be brought to the website"><?php echo $project->getRedirectAlertText(); ?></textarea>
				</div>
			</div>
			
				<div class="formRow">
				<div class="col col50 colLabel">
					<label for="RedirectAlertTitle"><i class="fa fa-pencil"></i>Popup Title Bar Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="RedirectAlertTitle" id="RedirectAlertTitle" value="<?php echo $project->getRedirectAlertTitle(); ?>" maxlength="50" required="required" placeholder="Thank you for playing"/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="RedirectButtonText"><i class="fa fa-pencil"></i>Popup Button Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="RedirectButtonText" id="RedirectButtonText" value="<?php echo $project->getRedirectButtonText(); ?>" placeholder="OK" maxlength="20" required="required"/>
				</div>
			</div>
			
			
			<br />
			<hr />
			<h2>Popup No Redirect - Finished</h2>
			
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="FinishAlertText"><i class="fa fa-pencil"></i> Text in popup:</label>
				</div>
				<div class="col col50">
					<textarea name="FinishAlertText" id="FinishAlertText" maxlength="200" required="required" placeholder="Thank you for playing"><?php echo $project->getFinishAlertText(); ?></textarea>
				</div>
			</div>
			
				<div class="formRow">
				<div class="col col50 colLabel">
					<label for="FinishAlertTitle"><i class="fa fa-pencil"></i> Popup Title Bar Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="FinishAlertTitle" id="FinishAlertTitle" value="<?php echo $project->getFinishAlertTitle(); ?>" maxlength="50" required="required" placeholder="Thanks for playing"/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="FinishAlertButtonText"><i class="fa fa-pencil"></i>Popup Button Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="FinishAlertButtonText" id="FinishAlertButtonText" value="<?php echo $project->getFinishAlertButtonText(); ?>" placeholder="Close" maxlength="20" required="required"/>
				</div>
			</div>
			
			
			
			<br />
			<hr />
			<h2>Repeat Visitors who entered Contest, and no redirect</h2>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="RepeatAlertText"><i class="fa fa-pencil"></i> Text in popup:</label>
				</div>
				<div class="col col50">
					<textarea name="RepeatAlertText" id="RepeatAlertText" maxlength="200" required="required" placeholder="Looks like you've already entered the contest!"><?php echo $project->getRepeatAlertText(); ?></textarea>
				</div>
			</div>
			
				<div class="formRow">
				<div class="col col50 colLabel">
					<label for="RepeatAlertTitle"><i class="fa fa-pencil"></i> Popup Title Bar Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="RepeatAlertTitle" id="RepeatAlertTitle" value="<?php echo $project->getRepeatAlertTitle(); ?>" maxlength="50" required="required" placeholder="Thanks for playing"/>
				</div>
			</div>
			
			<div class="formRow">
				<div class="col col50 colLabel">
					<label for="RepeatAlertButtonText"><i class="fa fa-pencil"></i> Popup Button Text:</label>
				</div>
				<div class="col col50">
					<input type="text" name="RepeatAlertButtonText" id="RepeatAlertButtonText" value="<?php echo $project->getRepeatAlertButtonText(); ?>" placeholder="OK" maxlength="20" required="required"/>
				</div>
			</div>
			
			
			
			
			
			<input type="hidden" name="projectID" value="<?php echo $projectID; ?>" />
			<input class="button wa" type="submit" value="Save!" />
			
			
		</form>
		
		<br />
		<br />
		<a class="button wa" href="<?php echo fixedPath; ?>/administration/backup/?projectID=<?php echo $projectID; ?>">Export/Dump Project</a>
		<br />
		<br />
<?php
		$projID =  $project->getId(); 
	  footerMenu($projID);
	

		}
	
	}
?>