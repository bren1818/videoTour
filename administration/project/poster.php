<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	pageHeader();
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$projectID = $_POST['projectID'];
		
		
		if ($_FILES["posterFile"]["error"] > 0){
			echo "Error: " . $_FILES["posterFile"]["error"] . "<br>";
		}else{
			pageHeader();
			echo "<h2>Uploading Badge</h2>";
			echo "<p>Upload: " . $_FILES["posterFile"]["name"] . "<br>";
			echo "Type: " . $_FILES["posterFile"]["type"] . "<br>";
			echo "Size: " . ($_FILES["posterFile"]["size"] / 1024) . " kB<br>";
		
			$conn = getConnection();
			$ext = explode(".", $_FILES["posterFile"]["name"]);
			$ext = end( $ext );
		
			$filename = $projectID."_poster.".$ext;
			$project = new Projects($conn);
			$project = $project->load( $projectID );
			
			if (file_exists("../.." . $project->getPosterFile() ) ){
				echo "Replacing Poster<br />";
			
				$fileExt = explode( "." ,$project->getPosterFile() );
				$fileExt = end( $fileExt );
				
				echo "Old Name: "."../.." . $project->getPosterFile().", New Name: "."../../uploads/deleted/". basename($filename, $fileExt).time().$fileExt.'<br />';
				
				if (!file_exists('../../uploads/deleted')) {
					mkdir('../../uploads/deleted', 0777, true);
				}
				
				
				if( rename ( "../.." . $project->getPosterFile()  , "../../uploads/deleted/". basename($project->getPosterFile(), $fileExt).time().".".$fileExt ) ){
					echo $project->getPosterFile() . " already exists - old poster moved";
				}
				
			
			}
			
			
			

			if (file_exists("../../uploads/" . $_FILES["posterFile"]["name"]) ){
				
				
			}else{
			  $moved = ( move_uploaded_file($_FILES["posterFile"]["tmp_name"],"../../uploads/" .$filename) );
			  if( $moved ){
				$project->setPosterFile( "/uploads/" .$filename );
				$project->save();
			  }else{
				echo "Could not upload Poster";
			  }
			  
			}
			?>
				<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projectID;  ?>" class="button wa">Back to Project</a></p>
			
			<?php
		}
	}
	
	
	$conn = getConnection();
	$project = new Projects($conn);
	if( isset($_REQUEST['projectID']) || isset($_POST['projectID'])  ){
		$projectID = ( isset($_REQUEST['projectID']) ? $_REQUEST['projectID'] : ( isset($_POST['projectID']) ? $_POST['projectID'] : "") );
		$project = $project->load($projectID);
		if( is_object($project) ){
?>
			<h1>Upload Poster</h1>
			<?php
				if( $project->getPosterFile() != "" ){
			?>
					<p><a target="_blank" href="<?php echo fixedPath.$project->getPosterFile(); ?>"><i class="fa fa-eye"></i> View Current Poster</a></p>
			<?php	
				}
			?>
			
			<form method="post" action="poster.php" enctype="multipart/form-data">
				<label for="posterFile"><input type="file" name="posterFile" id="posterFile" /></label>
				<input class="wa button" type="submit" value="Upload" />
				<input type="hidden" name="projectID" value="<?php echo $projectID; ?>" />
			</form>

<?php
		$projID =  $project->getId(); 
		footerMenu($projID);
		}
	}
	pageFooter();
?>