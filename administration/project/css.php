<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	pageHeader();
	
	$conn = getConnection();
	$project = "";
	$projectID = "";
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if( isset( $_POST['projectID'] ) && $_POST['projectID'] != "" ){
			$projectID = $_POST['projectID'];
			
			checkAccess( $projectID );
			
			$CSS = ( isset( $_POST['CSS'] ) ? $_POST['CSS'] : "" );
			
			$stylesheet = new stylesheet( $conn );
			
			if( isset( $_POST['SSID'] ) && $_POST['SSID'] == "" ){
				$stylesheet->setCSS( $CSS );
				$stylesheet->setProjectID( $projectID );
				$stylesheet->save();
				

				
				echo "New SS Saved as: ".$stylesheet->getId();
			}else{
				
				$stylesheet = $stylesheet->loadByProjectID($projectID);
				if( is_object(  $stylesheet ) ){
					$stylesheet->setCSS( $CSS );
					$saved = $stylesheet->save();
					
					echo "Saved";
					
				}else{
					echo "Error Could not save changes";
				}
			}
			

			$project = new Projects($conn);
			
			$project = $project->load($projectID);
		}else{
			$project = "";
		}
		
	}
	
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projectID = $_REQUEST['projectID'];
		$project = new Projects($conn);
		$project = $project->load($projectID);
		
		checkAccess( $projectID );
	}
	
	if( is_object($project) ){
		$stylesheet = new stylesheet( getConnection() );
		$stylesheet = $stylesheet->loadByProjectID($projectID );
		
		if( is_object($stylesheet) ){
			$css = $stylesheet->getCSS();
			$ssid = $stylesheet->getId();
		}
?>
	<h1>&ldquo;<?php echo $project->getTitle(); ?>&rdquo; project StyleSheet</h1>
	<form name="ProjectCSS" method="POST" action="css.php">
		<textarea style="width: 100%; min-height: 500px; overflow: auto;" name="CSS" ID="CSS"><?php echo $css; ?></textarea>
		<input type="hidden" name="projectID" value="<?php echo $projectID; ?>" />
		<input type="hidden" name="SSID" value="<?php echo $ssid; ?>" />
		<input class="button wa" type="submit" value="Save" />
	</form>
<?php
	}
	footerMenu($projectID );
	pageFooter();
?>
