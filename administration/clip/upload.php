<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	require_once("convert.php");
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		$projectID = $_POST['projectID'];

		if( $_FILES['video']['error'] == 0 ){
			$videoName = $_FILES['video']['name'];
			$videoType = $_FILES['video']['tmp_name'];
			$videoSize = $_FILES['video']['size'];
			
			
			//$filename = $projectID."_".$clip->getId().".".$ext; //md5($_FILES["video"]["name"]);
			
			if (file_exists("../../uploads/" . $_FILES["video"]["name"])){
				pageHeader("Upload & Convert Clip");
				echo '<p>Upload Complete!</p>';
				echo '<p>"'.$_FILES["video"]["name"] .'" already exists. </p>';
				$error = 1;
				echo '<p><a class="button wa" href="'.fixedPath.'/administration/clip/upload?projectID='.$projectID.'">Try again</a></p>';
			}else{
				//we've uploaded the video, lets create the entry and then convert the clips
				
				$conn = getConnection();
				$clip = new Clip($conn);
				
				$name = $_POST['name'];
				$note = $_POST['note'];
				//$duration = $_POST['duration'];
				
				
				$clip->setName( $name );
				$clip->setNote( $note );
				//$clip->setDuration ( $duration );
				$clip->setProjectID( $projectID );
				//$clip->setPath("/uploads/".$videoName);
				$x =  $clip->save();
				
				pageHeader("Upload & Convert Clip");
				
				ob_implicit_flush(true);ob_end_flush();
				
				echo '<h1>Uploading Video</h1>';
				
				echo "<p>Clip ID saved as: ".$clip->getId()."</p><p><b>PLEASE NOTE</b> the subsequent versions are going to converted as a background process. They will be marked as converted in the dashboard when complete</p>";
			
				//do conversion if not m4v
				$ext = (explode(".", $_FILES["video"]["name"]));
				$ext = end($ext);
				//if( $ext != "m4v"){
				
					
				echo '<p>Upload Complete!</p>';
				
				
				echo "<p> Video extension is: ".$ext." which needs to be converted to M4V</p>";
				$filename = $projectID."_".$clip->getId().".".$ext; //md5($_FILES["video"]["name"]);
				
				move_uploaded_file($_FILES["video"]["tmp_name"], "../../uploads/" .$filename);
				
				
				
			
				
				
				//echo $actual_link.$url;
				
				set_time_limit(20 * 60);
				/*
				$vars = "?PID=".$projectID."&CID=".$clip->getId()."&FN=".$filename;
				$actual_link = "http://$_SERVER[HTTP_HOST]";
				$url = '/administration/clip/backgroundConvert.php'.$vars;
				
				$ch = curl_init($actual_link.$url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_NOBODY, TRUE);
				$data = curl_exec($ch);
				curl_close($ch);
				
				print_r( $data );
				
				?>
				<h2>The server will now convert your video in the background</h2><p>Please feel free to continue working on your project.</p>
				<?php
				

				*/
				
				echo '<p><a target="_blank" class="button wa" href="'.fixedPath.'/administration/project/edit?id='.$projectID.'">Go back to Project while this converts</a></p>';
				
				
				echo '<h3>The Server will now convert your clips</h3>';
				//new Clips
				$convert = new Convert( $conn );
				$convert->setClipID( $clip->getId() );
				$convert->setProjectID( $projectID );
				$convert->setOS( CUR_OS );
				
				
				//original cip
				$convert->setType( 0 ); //source
				$convert->setOutputpath( "/uploads/".$filename);
				$convert->updateRecord();
				
				
				
				
				// actual conversions
				
				$convert->setSourceFolder("../../uploads/");
				$convert->setDestinationFolder("../../uploads/");
				$convert->setSourceFile($filename);
				$convert->setDestinationFile( $projectID."_".$clip->getId() );
				
				$convert->setType( 1 ); //original
				$convert->doConvert();
				
				echo "<p>Done Conversion 1/3</p>";
				
				logMessage( "Converted original source file, ProjectID: ".$projectID." ClipID: ".$clip->getId()." to path: ".$filename, "conversion.log");
			
				
				$convert->setType( 2 ); //mobile
				$convert->setWidth( 640 );
				$convert->setHeight( 480 );
				$convert->doConvert();
				
				echo "<p>Done Conversion 2/3</p>";
				
				logMessage( "Converted tablet source file, ProjectID: ".$projectID." ClipID: ".$clip->getId()." to path: ".$filename, "conversion.log");
			
				
				$convert->setType( 3 ); //mobile
				$convert->setWidth( 320 );
				$convert->setHeight( 240 );
				$convert->doConvert();
				
				echo "<p>Done Conversion 3/3</p>";
			
			logMessage( "Converted mobile source file, ProjectID: ".$projectID." ClipID: ".$clip->getId()." to path: ".$filename, "conversion.log");
				
				?>
				<p><a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projectID;  ?>" class="button wa">Back to Project</a></p>
				
				<?php
				
			}
		}else{
			pageHeader("File Upload Error");
			echo '<p>'.$_FILES["file"]["error"].'</p>';
			echo '<p><a class="button wa" onClick="reload('.$projectID.')">Try again</a></p>';
		}
	
		
	
	}else{
		pageHeader();
		if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
			$projectID = $_REQUEST['projectID'];
			
			$conn = getConnection();
			$project = new Projects($conn);
			$project = $project->load($projectID);
	?>
		<h1>Upload clip to "<?php echo $project->getTitle(); ?>"</h1>
		
		<form id="clipUpload" action="upload.php" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td valign="top">
						<label for="video"><i class="fa fa-video-camera"></i> Video file: </label>
					</td>
					<td>
						<input type="file" name="video" id="video" value="" required="required"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<label for="name"><i class="fa fa-pencil"></i> Clip name: </label>
					</td>
					<td>
						<input type="text" name="name" id="name" value="" required="required"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<label for="note"><i class="fa fa-pencil"></i> Video note: </label>
					</td>
					<td>
						<textarea id="note" name="note" placeholder="EG: this clip is wrong choice for part 2" required="required"></textarea>
					</td>
				</tr>
				
			</table>
			<input type="hidden" name="projectID" value="<?php echo $project->getId(); ?>" />
			
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