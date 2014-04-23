<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		//decode the import
		if ($_FILES["file"]["error"] > 0){
				echo "Error: " . $_FILES["file"]["error"] . "<br>";
		}else{
			echo "<h2>Uploading Project</h2>";
			echo "<p>Upload: " . $_FILES["file"]["name"] . "<br>";
			echo "Type: " . $_FILES["file"]["type"] . "<br>";
			echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br></p>";
		
			$conn = getConnection();
			$ext = explode(".", $_FILES["file"]["name"]);
			$ext = end( $ext );
			if( $ext == "projEx"){
				echo "<p>Project file appears to be correct - attempting decode</p>";
				
				
				 $theFile = $_FILES["file"]["tmp_name"];
				  
				  
				$handle = fopen($theFile, "rb");
				$contents = fread($handle, filesize($theFile));
				fclose($handle);
				
				$projectData =  unserialize($contents);
				
				
				echo "<h2>Decode:</h2>";
				echo '<div style="height: 300px; overflow: auto; width: 100%;">';
					pa( $projectData );
				echo '</div>';
				
				echo "<h2>Decode Complete - Beginning Import</h2>";
				
				echo "<p>Creating Connections...";
				for($PD = 0; $PD < sizeof( $projectData ); $PD++){
					if( is_array( $projectData[$PD] ) ){
						for($o = 0; $o < sizeof($projectData[$PD]); $o++ ){
							if( is_object($projectData[$PD][$o]) ){
								if( method_exists($projectData[$PD][$o], "setConnection") ){
									$projectData[$PD][$o]->setConnection( $conn );
								}
							}
						}
					}
				}
				echo 'Connections created</p>';
				
				$IDMAP = new array(); //type, oldID, new ID
				
				
				echo '<p>Creating <b>New Project</b></p>';
				$newProj = $projectData["project"];
				if( is_object( $newProj ) ){
					echo "<p>Old ID: ".$newProj->getId().'<br />';
					$newProj->setId("");
					echo "New ID: ".$newProj->getId()."</p>";
					//will have to come back to fix starting segment, posterFile
					
					
					
				}else{
					echo "<h3>Could Not create Project, this is mandatory!</h3>";
				}
				
			}else{
				echo "<p>Project file doesn't appear to be correct</p>";
			}
		}
	}else{
		pageHeader();
?>
	<form action="importDump.php" method="post" enctype="multipart/form-data">
		<table>
			<tr>
				<td>Projex File:</td>
				<td>
					<input type="file" name="file" required="required"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input class="button wa" type="submit" value="Import" />
				</td>
			</tr>
		</table>
	</form>
<?php
	pageFooter();
}
?>