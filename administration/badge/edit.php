<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$projectID = $_POST['projectID'];
		$badgeID = $_POST['badgeID'];
		
		
		$note = $_POST['note'];
		
		if ($_FILES["file"]["error"] > 0){
			echo "Error: " . $_FILES["file"]["error"] . "<br>";
		}else{
			pageHeader();
			echo "<h2>Uploading Badge</h2>";
			echo "<p>Upload: " . $_FILES["file"]["name"] . "<br>";
			echo "Type: " . $_FILES["file"]["type"] . "<br>";
			echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
		
			$conn = getConnection();
			$ext = explode(".", $_FILES["file"]["name"]);
			$ext = end( $ext );
		
			$badge = new Badge( $conn );
			$badge = $badge->load( $badgeID );
			//$badge->setId( $badgeID );
			//$badge->setProjectID( $projectID );
			$badge->setNote( $note );
			
			
			$bid = $badge->save();
			
			$filename = $projectID."_".$bid."_badge.".$ext;
			
			//echo "New filename: <b>".$filename.'</b></p><br />';
			//echo getcwd();
			
			if( $bid == -1 ){
			
				echo "Error";
				//badge -> delete?
				
			
			}else{
				//error
				if (file_exists("../.." . $badge->getPath() ) ){
					//$filename = $filename.time();
					$fileExt = explode( "." ,$badge->getPath() );
					$fileExt = end( $fileExt );
					
					//echo "Old Name: "."../.." . $badge->getPath().", New Name: "."../../uploads/deleted/". basename($filename, $fileExt).time().$fileExt;
					
					if( rename ( "../.." . $badge->getPath()  , "../../uploads/deleted/". basename($badge->getPath(), $fileExt).time().".".$fileExt ) ){
						echo $badge->getPath() . " already exists - old badge moved";
					}
					
					$moved = ( move_uploaded_file($_FILES["file"]["tmp_name"],"../../uploads/" .$filename) );

				}else{
				  $moved = ( move_uploaded_file($_FILES["file"]["tmp_name"],"../../uploads/" .$filename) );
				}
				
				$badge->setPath( "/uploads/".$filename );
				$badge->save();
				
				?>
					<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projectID;  ?>" class="button wa">Back to Project</a></p>
				
				<?php
				pageFooter();
			}
			
		
		
		
		}
	}else{
		pageHeader();
		if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] !="" && isset($_REQUEST['badgeID']) && $_REQUEST['badgeID'] != ""  ){
			$projectID = $_REQUEST['projectID'];
			$badgeID = $_REQUEST['badgeID'];
			
			$conn = getConnection();
			$project = new Projects($conn);
			$project = $project->load($projectID);
			$badge = new Badge($conn); 
			$badge = $badge->load($badgeID);
	?>
		<h1>Update Badge</h1>
		
		<p>Existing Badge:<br />
		<img src="<?php  echo $badge->getPath(); ?>" /></p>
		
		<form id="BadgeUpload" action="edit.php" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td valign="top">
						<label for="badge"><i class="fa fa-shield"></i> Badge Image: </label>
					</td>
					<td>
						<input type="file" name="file" id="badge" value="" required="required"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<label for="note"><i class="fa fa-pencil"></i> Badge note: </label>
					</td>
					<td>
						<textarea id="note" name="note" placeholder="EG: this badge is for part 2" required="required"><?php echo $badge->getNote(); ?></textarea>
					</td>
				</tr>
				
			</table>
			<input type="hidden" name="projectID" value="<?php echo $project->getId(); ?>" />
			<input type="hidden" name="badgeID" value="<?php echo $badge->getId(); ?>" />
			
			<input  id="submit" class="button" type="submit" value="Upload" />
			<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projectID;  ?>" class="button wa">Back to Project</a></p>
		</form>
		
	<?php
		
		}else{
			?>
			<h1>No ProjectID specified...</h1>
			
			<p><a class="button" href="<?php echo fixedPath; ?>/admin"><i class="fa fa-reply"></i> Go Back</a></p>
			<?php
		}
	}
	pageFooter();
?>