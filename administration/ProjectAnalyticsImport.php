<?php
	include "../includes/includes.php";
	if( (isset($_REQUEST['projectID']) && $_REQUEST['projectID'] != "") || (isset($_POST['projectID']) && $_POST['projectID'] != "") ){
		$projID = $_REQUEST['projectID'];
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projID);
	}

	$IDMAP = array(); //type, oldID, new ID
	$MapLog = "";
	$WarningLog = "";
	
	function getNewID( $type, $oldID ){
		global $IDMAP;
		if( isset( $IDMAP[$type] ) && is_array($IDMAP[$type])  ){
			for( $ts = 0; $ts < sizeof(  $IDMAP[$type] ); $ts++ ){
				if( $IDMAP[$type][$ts]["oldID"] == $oldID ){
					return  $IDMAP[$type][$ts]["newID"];
					break;
				}
			}
			global $WarningLog;
			$WarningLog.= "Couldn't find ID for : ".$type." with old ID: ".$oldID.'<br />';
			return 0;
		}
	}
	
	function setNewID( $type, $oldID, $newID ){
		global $IDMAP;
		$IDMAP[$type][] = array("oldID" => $oldID, "newID" => $newID );
		global $MapLog;
		$MapLog.= "<p>Old ID for ".$type.": ".$oldID." now set to: ".$newID."</p>";
		if( $newID < 1 ){
			global $WarningLog;
			$WarningLog.= "Set ID for : ".$type." with old ID: ".$oldID." as ".$newID." - which could indicate an issue";
		}
	}
	
	
	
	
	
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$entryOnly = ((isset($_POST['entryOnly']) && $_POST['entryOnly'] == 1) ? 1 : 0 );
	
		pageHeader();
			if ($_FILES["file"]["error"] > 0){
				echo "Error: " . $_FILES["file"]["error"] . "<br>";
			}else{
				echo "<h2>Uploading Project Analytics</h2>";
				echo "<p>Upload: " . $_FILES["file"]["name"] . "<br>";
				echo "Type: " . $_FILES["file"]["type"] . "<br>";
				echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br></p>";
	
				$projectAnalytics = $_FILES["file"]["tmp_name"];
				if( file_exists( $projectAnalytics ) ){
					$ext = explode(".", $_FILES["file"]["name"]);
					$ext = end( $ext );
					if( $ext == "projAnalytics" ){
						
						$theFile = $_FILES["file"]["tmp_name"];
						$handle = fopen($theFile, "rb");
						$contents = fread($handle, filesize($theFile));
						fclose($handle);
			
						$projectAnalyticData =  unserialize($contents);
						
						echo "<h2>Decoding file:</h2>";
						echo '<div style="height: 100px; overflow: auto; width: 100%;">';
							pa( $projectAnalyticData );
						echo '</div>';
						
						echo "<h2>Importing Analytics from old Project: ".$projectAnalyticData['Project']->getTitle()." into ".$project->getTitle()."...</h2>";
						
						echo '<h3>Importing '.sizeof($projectAnalyticData['Analytics_visitors'])." visitor's Data and ".sizeof( $projectAnalyticData['Analytics_visitor_event'])." events...<h3>";
						
						echo "<p>Importing Visitors.";
						$count = 0;
						$skipped = 0;
						foreach( $projectAnalyticData['Analytics_visitors'] as $visitor ){
						
							if( $entryOnly == 1 && ( $visitor->getFilled_out_entry() != 1 ) ){
								//skip
								$skipped++;
								continue;
							}
						
							$oldID = $visitor->getId();
							$visitor->setId("");
							$visitor->setProjectId( $project->getId() );
							$visitor->setConnection($conn);
							$newID = $visitor->save(); 
							if( $newID > 0 ){
								$visitor->save(); // adds in the start device etc
								setNewID( "analytics_visitors", $oldID, $newID );
								echo ".";
								flush();
								$count++;
							}else{
								global $WarningLog;
								$WarningLog.="Could not save Visitor: ".$oldID."<br/>";
							}
							
							if( $count % 50 === 0 ){
								echo "<br />";
							}
						}
						echo ".complete!</p>";
						
						if( $entryOnly == 1 ){
							echo "<p>Skipped: ".$skipped." users without contest entry</p>";
						}
			
						echo "<p>Importing Events.";
						$count = 0;
						foreach( $projectAnalyticData['Analytics_visitor_event'] as $event ){
							$oldID = $event->getEvent_id();
							$oldVisitorID = $event->getVisitor_id();
							$event->setProjectId( $project->getId() );
							$event->setVisitor_id( getNewID( "analytics_visitors" , $oldVisitorID ) );
							
							if( $event->getVisitor_id() == 0 ){
								global $WarningLog;
								$WarningLog.="Wont import event (".$oldID.") <br/>";
							}else{
							
								$event->setConnection($conn);
								$event->save();
								//$newID =  $event->getEvent_id();
								if( $newID > 0 ){
									//setNewID( "analytics_visitors", $oldID, $newID );
									echo ".";
									flush();
									$count++;
								}
								if( $count % 50 === 0 ){
									echo "<br />";
								}
							}
						}
						echo ".complete!</p>";
						
						//Entries
						echo "<p>Importing Entries.";
						$count = 0;
						foreach( $projectAnalyticData['Entries'] as $entry ){
							$oldID = $entry->getEntryID();
							$oldVisitorID = $entry->getVisitorID();
							$entry->setProjectID( $project->getId() );
							$entry->setVisitorID( getNewID( "analytics_visitors" , $oldVisitorID ) );
							$entry->setConnection($conn);
							$entry->save();
							$newID =  $entry->getEntryID();
							if( $newID > 0 ){
								//update the visitor with the fixed entryID...
								
									$vtu = new analytics_visitors($conn);
									$vtu = $vtu->load( $entry->getVisitorID() );
									if( is_object( $vtu ) ){
										$vtu->setEntryID( $newID );
										$vtu->save();
									}
							
							
								echo ".";
								flush();
								$count++;
							}
							if( $count % 50 === 0 ){
								echo "<br />";
							}
						}
						echo ".complete!</p>";
						
			
					}else{
						echo "<p>File is not a project analytics file :(</p>";
					}
					
					global $WarningLog;
					if( $WarningLog != "" ){
						echo "<h2>Warning Log:</h2>";
						echo '<div style="height: 100px; overflow: auto; width: 100%;">';
							echo $WarningLog;
						echo '</div>';
	
					}
					
					echo "<h1>Import Complete!</h1>";
					
				}else{
					echo "<p>Analytics file corrupted on upload, please try again</p>";
				}
			}
		footerMenu( $project->getId() );
		pageFooter();
	}else{
	
		pageHeader("Import Analytics into: ".$project->getTitle());
?>
		<h1>Import analytics into &ldquo;<?php echo $project->getTitle(); ?>&rdquo;</h1>
		<form action="projectAnalyticsImport.php" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td><label for="file">Analytics File: (.projAnalytics)</label></td>
					<td>
						<input type="file" id="file" name="file" required="required"/>
					</td>
				</tr>
				<tr>
					<td><label for="entryOnly">Only import analytic Data with corresponding contest entry</label></td>
					<td>
						<input type="checkbox" name="entryOnly" id="entryOnly" value="1" />
					</td>
				</tr>
				
				<tr>
					<td colspan="2">
						<input class="button wa" type="submit" value="Import" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="projectID" value="<?php echo $project->getId(); ?>" />
			
		</form>
<?php
		footerMenu($project->getId() );
		pageFooter();
	}

?>