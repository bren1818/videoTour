<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	
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
		//decode the import
		if ($_FILES["file"]["error"] > 0){
				echo "Error: " . $_FILES["file"]["error"] . "<br>";
		}else{
			pageHeader();
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
				
				
				echo '<p>Creating <b>New Project</b></p>';
				$newProj = $projectData["project"];
				if( is_object( $newProj ) ){	
					
					$oldID = $newProj->getId();
					$newProj->setId("");
					$newProj->setConnection( $conn );
					$newProjID = $newProj->save(); //creates the new ID
					
					if( $newProjID != -1 ){
						$newProj->setTitle("Import_".$newProj->getTitle() );
						$newProj->save(); //Updates the record
						
						setNewID( "project", $oldID, $newProjID );  //0 will be whatever the new ID is
						echo "Swapping ProjectID: ".$oldID." with: ".getNewID( "project" , $oldID );
						echo "<h3>New Project Setup Complete.</h3>";
						
						echo "<p>Setting Up Clip</p>";
						
						//get Clip First...
						$clip =  $projectData["Clip"];
						foreach( $clip as $c ){
							$oldClipID = $c->getId();
							$c->setId("");
							$c->setProjectID( $newProjID );
							$c->setConnection( $conn );
							$c->save();
							$newClipID = $c->getId();
							setNewID( "Clip", $oldClipID, $newClipID );
						}
						
						echo "<p>Setting Up Clips</p>";
						
						$clips =  $projectData["Clips"];
						foreach( $clips as $clip ){	
							$oldClipsID = $clip->getId();
							$clip->setId("");
							$clip->setProjectID( $newProjID );
							$refClip = $clip->getClipID();
							$clip->setClipID( getNewID( "Clip" , $refClip ) );
							$clip->setConnection( $conn );
							$newClipsID = $clip->save();
							setNewID( "Clips", $oldClipsID, $newClipsID );
							
							//TODO
							$oldPath = $clip->getPath();
							
													//search                     replace                                 haystack
							$newPath = str_replace( "/".$oldID."_".$refClip."_", "/".$newProjID."_".$newClipsID."_", $oldPath );
							echo "<p>I should replace the old path (".$oldPath." - ". "/".$oldID."_".$refClip."_" ." ) with the new path: ".$newPath." ( "."/".$newProjID."_".$newClipsID."_".") </p>";
							//$clip->setPath( $newPoster );
							//$clip->save();
							
							//move the uploaded files with old name, to the new name
							
							
						}
						
						echo "<p>Setting Up Decision Trees</p>";
						$decisionTrees = $projectData["DecisionTree"];
						foreach( $decisionTrees as $dt){
							$oldDecisionTreeID = $dt->getId();
							$dt->setId("");
							$dt->setProjectID($newProjID );
							$dt->setConnection($conn);
							$newDecisionTreeID = $dt->save();
							setNewID( "DecisionTree", $oldDecisionTreeID, $newDecisionTreeID );
						}
						
						echo "<p>Setting up Badges</p>";
						$badges = $projectData["Badges"];
						foreach( $badges as $badge ){
							$oldBadgeID = $badge->getId();
							$badge->setId("");
							$badge->setProjectID( $newProjID );
							$badge->setConnection($conn);
							
							
							
							
							$newBadgeID = $badge->save();
							setNewID( "Badges", $oldBadgeID, $newBadgeID );
							
							
							//Badge ID
							$oldPath = $badge->getPath();
													//search                     replace                                 haystack
							$newPath = str_replace( "/".$oldID."_".$oldBadgeID."_", "/".$newProjID."_".$newBadgeID."_", $oldPath );
							echo "<p>I should replace the old path (".$oldPath." - ". "/".$oldID."_".$oldBadgeID."_" ." ) with the new path: ".$newPath." ( "."/".$newProjID."_".$newBadgeID."_".") </p>";
							
						}
						
						echo "<p>Setting up Segments</p>";
						$segments = $projectData["Segments"];
						foreach( $segments as $segment ){
							$oldSegmentsID = $segment->getId();
							$segment->setId("");
							$segment->setProjectID( $newProjID );
							$theClip = $segment->getClipID();
							$segment->setClipID ( getNewID( "Clip" , $theClip ) );
							$refDTID = $segment->getDecisionTreeID();
							$segment->setDecisionTreeID( getNewID( "DecisionTree" , $refDTID ) );
							$refBadgeID = $segment->getBadge();
							$segment->setBadge(  getNewID( "Badges" , $refBadgeID ) );
							$segment->setConnection($conn);
							
							$newSegmentsID = $segment->save();
							
							
							setNewID( "Segments", $oldSegmentsID, $newSegmentsID );
						}
						
						echo "<p>Setting up Decisions</p>";
						$decisions = $projectData["Decisions"];
						foreach( $decisions as $decision ){
							$oldDecisionID = $decision->getId();
							$decision->setId("");
							$decision->setProjectID( $newProjID );
							
							$refClip = $decision->getClipID();
							$decision->setClipID( getNewID( "Clip" , $refClip ) );
							
							$refDT = $decision->getDecisionTreeID();
							$decision->setDecisionTreeID( getNewID( "DecisionTree" , $refDT ) );
							
							$refSegment = $decision->getSegmentID();
							$decision->setSegmentID( getNewID( "Segments" , $refSegment ) );
							
							$refBadgeID = $decision->getForcedBadgeID();
							$decision->setForcedBadgeID( getNewID( "Badges" , $refBadgeID ) );
						
							$decision->setConnection( $conn );
							
							$newDecisionID = $decision->save();
							setNewID( "Decisions", $oldDecisionID, $newDecisionID );
						}
						
						//CSS,JS
						$CSS = new stylesheet($conn);
						$CSS->setProjectID( $newProjID );
						$CSS->setCSS( $projectData["CSS"] );
						$NewCSSID = $CSS->save();
						
						$JS = new javascript($conn);
						$JS->setProjectID( $newProjID );
						$JS->setJS( $projectData["JS"] );
						$NEWJSID = $JS->save();
						
						echo "<p>Fixing some file links</p>";
						
						//project
						$origStart = $newProj->getStartingSegmentID();
						$newProj->setStartingSegmentID(   getNewID( "Segments" , $origStart ) );
						//poster path
						if( $newProj->getPosterFile() != "" ){
							$oldPoster = $newProj->getPosterFile();
							$newPoster = str_replace( "/".$oldID."_poster", "/".$newProjID."_poster", $oldPoster );
							
							echo "<p>I should replace the oldPoster ".$oldPoster." with the new Poster ".$newPoster."</p>"; 
							
							//$newProj->setPosterFile( $newPoster );
						}
						$newProj->save();

						//File renaming!!
						
						
						echo "<p><a target='_blank' href='".fixedPath."/administration/project/edit?id=".$newProjID."'>View Imported Project</a></p>";
						
						echo '<div style="height: 100px; overflow: auto; width: 100%;">';
							echo '<h3>Logs:</h3>';
							global $MapLog;
							echo $MapLog;
						echo '</div>';
						
						echo '<div style="height: 100px; overflow: auto; width: 100%;">';
							echo '<h3>Warnings:</h3>';
							global $WarningLog;
							echo $WarningLog;
						echo '</div>';
						
						echo '<div style="height: 100px; overflow: auto; width: 100%;">';
							echo '<h3>Full Map</h3>';
							global $IDMAP;
							pa( $IDMAP );
						echo '</div>';
					
						echo '<br /><br /><br />';
					
					}else{
						echo "<h3>Could Not create Project, this is mandatory!</h3>";
					}	
				}else{
					echo "<h3>Loaded Project not an object</h3>";
				}
			}else{
				echo "<p>Project file doesn't appear to be correct</p>";
			}
			
			footerMenu($newProjID);
			
			pageFooter();
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
				<td>Project Files (Zip):</td>
				<td>
					<input type="file" name="zipFile" /> <!--required="required"-->
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