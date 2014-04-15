<?php
	error_reporting(E_ALL);
	include "../includes/includes.php";
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projID = $_REQUEST['projectID'];
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projID);
		pageHeader();
		echo '<h1>"' .$project->getTitle() .'" Contest Entries</h1>';
?>

	

<?php
		function getReason($id){
			switch ($id){
				case 1:
					return "Accepted Offer";
				break;
				case 2:
					return "On the Fence";
				break;
				case 3:
					return "Waiting to hear from Other Uni";
				break;
				default:
				case 4:
					return "Other";
				break;
			}
		}
		
		function getDevice($id){
			switch ($id){
				default:
				case 1:
					return "Computer";
				break;
				case 2:
					return "Tablet";
				break;
				case 3:
					return "Phone";
				break;
			}
		}


		$query = $conn->prepare("SELECT f.*, av.`ip`, av.`start_time`, av.`has_returned`, av.`device_type` FROM `form_entry` f
INNER JOIN `analytics_visitors` av 
ON av.`entryID` = f.`entryID`
WHERE f.`projectID` = :projectID");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$result = $query->fetchAll();
			if( is_array( $result ) ){
				?>
				<table class="tablesorter">
				<thead> 
					<tr>
						<th>#</th><th>EntryID</th><th>Visitor ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Telephone</th><th>Twitter Name</th><th>Offer</th>
						<th>Other Reason</th><th>Device</th><th>IP</th><th>Has Returned</th><th>Tour Start</th><th>Contest TimeStamp</th>
					</tr> 
				</thead>
				<tbody>
				<?php
				$i = 1;
				foreach( $result as $row ){
					echo '<tr><td>'.$i.'</td><td>'.$row['entryID'].'</td><td>'.$row['visitorID'].'</td><td>'.$row['firstName'].'</td><td>'.$row['lastName'].'</td><td>'.$row['email'].'</td><td>'.$row['telephone'].'</td><td>'.$row['twitter'].'</td><td>'.getReason( $row['other'] ).'</td><td>'.$row['other_reason'].'</td><td>'.getDevice($row['device_type']).'</td><td>'.$row['ip'].'</td><td>'.$row['has_returned'].'</td><td>'.$row['start_time'].'</td><td>'.$row['timestamp'].'</td></tr>';
					$i++;
				}
				?>
				</tbody>
				</table>
				<?php
			}
		}
	?>
	<a target="_blank" href="saveCSV.php?projectID=<?php echo $_REQUEST['projectID']; ?>" class="button wa" id="toCSV"><i class="fa fa-floppy-o"></i> Save as CSV</a> 
	<?php
$query = $conn->prepare("SELECT f.*, '', '', '', '' FROM `form_entry` f WHERE f.`projectID` = :projectID AND f.`entryID` NOT IN ( SELECT f.`entryID` FROM `form_entry` f INNER JOIN `analytics_visitors` av ON av.`entryID` = f.`entryID` WHERE f.`projectID` = :projectID )");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$result = $query->fetchAll();
			if( is_array( $result )  && $query->rowCount() > 0 ){
				?>
				<hr />
	<h3>Entries without Analytic Data </h3>
	<p>Entries may not have anlytic data if the data was deleted, if you think this is an error, please contact an administrator.</p>
	
				<table class="tablesorter">
				<thead> 
					<tr>
						<th>#</th><th>EntryID</th><th>Visitor ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Telephone</th><th>Twitter Name</th><th>Offer</th>
						<th>Other Reason</th><th>Device</th><th>IP</th><th>Has Returned</th><th>Tour Start</th><th>Contest TimeStamp</th>
					</tr> 
				</thead>
				<tbody>
				<?php
				$i = 1;
				foreach( $result as $row ){
					echo '<tr><td>'.$i.'</td><td>'.$row['entryID'].'</td><td>'.$row['visitorID'].'</td><td>'.$row['firstName'].'</td><td>'.$row['lastName'].'</td><td>'.$row['email'].'</td><td>'.$row['telephone'].'</td><td>'.$row['twitter'].'</td><td>'.getReason( $row['other'] ).'</td><td>'.$row['other_reason'].'</td><td></td><td></td><td></td><td></td><td>'.$row['timestamp'].'</td></tr>';
					$i++;
				}
				?>
				</tbody>
				</table>
				<a target="_blank" href="saveCSV.php?projectID=<?php echo $_REQUEST['projectID']; ?>&woAnalytics=1" class="button wa" id="toCSV"><i class="fa fa-floppy-o"></i> Save as CSV</a> 
				<br />
				<?php
			}
		}
	?>

	
	<br />
	<br />
	<a class="button wa" onClick="clearContestEntries(<?php echo $_REQUEST['projectID']; ?>)"><i class="fa fa-eraser"></i> Clear Contest Entries</a>
	<br />	<br />


	<p>
	  <a href="/administration/ProjectAnalytics.php?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-bar-chart-o"></i> Project Analytics</a>	
	  <a href="/administration/project/edit?id=<?php echo $projID; ?>" class="button wa"><i class="fa fa-tachometer"></i> Project Dashboard</a>
	  <a href="/administration/MapProject.php?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-sitemap"></i> Flow Chart</a>
	  <a class="button wa" href="/admin"><i class="fa fa-th-list"></i> Back to Admin</a> 
	</p>



	<?php
		}else{
		echo "<h1>Error: a project ID is required</h1>";
	}
	pageFooter();
?>