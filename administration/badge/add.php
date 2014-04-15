<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$projectID = $_POST['projectID'];
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
			$badge->setProjectID( $projectID );
			$badge->setNote( $note );
			
			
			$bid = $badge->save();
			$filename = $projectID."_".$bid."_badge.".$ext;
			
			echo "New filename: <b>".$filename.'</b></p><br />';
			//echo getcwd();
			
			if( $bid == -1 ){
			
				echo "Error";
				//badge -> delete?
				
			
			}else{
				//error
				if (file_exists("../../uploads/" . $_FILES["file"]["name"])){
					echo $_FILES["file"]["name"] . " already exists. ";
				}else{
				  $moved = ( move_uploaded_file($_FILES["file"]["tmp_name"],"../../uploads/" .$filename) );
				}
				
				$badge->setPath( "/uploads/".$filename );
				$badge->save();
				
				?>
					<p><a href="/administration/project/edit?id=<?php echo $projectID;  ?>" class="button wa">Back to Project</a></p>
				
				<?php
				pageFooter();
			}
			
		
		
		
		}
	}else{
		pageHeader();
		if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
			$projectID = $_REQUEST['projectID'];
			
			$conn = getConnection();
			$project = new Projects($conn);
			$project = $project->load($projectID);
	?>
		<h1>Upload Badge to "<?php echo $project->getTitle(); ?>"</h1>
		
		<form id="BadgeUpload" action="add.php" method="post" enctype="multipart/form-data">
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
						<textarea id="note" name="note" placeholder="EG: this badge is for part 2" required="required"></textarea>
					</td>
				</tr>
				
			</table>
			<input type="hidden" name="projectID" value="<?php echo $project->getId(); ?>" />
			
			<input  id="submit" class="button" type="submit" value="Upload" />
			<p><a href="/administration/project/edit?id=<?php echo $projectID;  ?>" class="button wa">Back to Project</a></p>
		</form>
		
	<?php
		
		}else{
			?>
			<h1>No ProjectID specified...</h1>
			
			<p><a class="button" href="/admin"><i class="fa fa-reply"></i> Go Back</a></p>
			<?php
		}
	}
	pageFooter();
?>