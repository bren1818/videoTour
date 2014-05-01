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

	<script type="text/javascript">
		function deletePath( visitorID, row ){
			var conf = confirm("Are you sure you want to delete this analytic (and it's corresponding entry if applicable)?");
			if( conf ){
				var ret;
				$.ajaxSetup({async: false});
				$.get( "<?php echo fixedPath; ?>/requestHandler.php", { fx : "deleteUserEvents", projectID : <?php echo $projID; ?>, userID : visitorID }, function( data ) {
					ret =  jQuery.parseJSON( data );
				});
				$.ajaxSetup({async: true});
			}
			
			if( ret ){
				if( ret.DeleteUserEvents ){
					$(row).closest("tr").remove();
					window.alert("Delete Trail!");
				
				}else{
					window.alert("Couldn't delete Trail");
				}
			
			}
	
		}
	
	
		function whereIs(ip){
			
			$(function(){
				
				$.ajax({
					url: 'https://freegeoip.net/json/' + ip,
					type: 'GET',
					dataType: 'jsonp',
					error: function(xhr, status, error) {
						alert("error");
					},
					success: function(json) {
						//alert("success");
						console.log( json );
						var html = "";
						html += "<p>Country : " + json.country_name + '<br />';
						html += "Province : " + json.region_name + '<br />';
						html += "City : " + json.city + '<br />';
						//html += "Area Code : " + json.area_code + '<br />';
						html += "Lat : " + json.latitude + '<br />';
						html += "Long : " + json.longitude + '<br />';
						html += "</p>";
						
						if( !$('#dialog').length ){
							$('body').append('<div id="dialog" title="Estimated User Location"></div>');
						}
						$( "#dialog" ).hide();
						$('#dialog').html( html );
					
						$( "#dialog" ).dialog({ width: "80%", title: "Estimated User Location" });
						
						
					}
				});
				
				
			});
		}
	
	</script>

	<style>
		td .button{
			white-space: nowrap;
		}
	</style>
	
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
					echo '<tr><td>'.$i.'</td><td>'.$row['entryID'].'</td><td>'.$row['visitorID'].' <a class="button wa" onClick="deletePath('.$row['visitorID'].', this)"><i class="fa fa-trash-o"></i> Delete</a></td><td>'.$row['firstName'].'</td><td>'.$row['lastName'].'</td><td>'.$row['email'].'</td><td>'.$row['telephone'].'</td><td>'.$row['twitter'].'</td><td>'.getReason( $row['other'] ).'</td><td>'.$row['other_reason'].'</td><td>'.getDevice($row['device_type']).'</td><td><button class="wa button" onClick="whereIs(\''.$row['ip'].'\')"><i class="fa fa-globe"></i> '.$row['ip'].'</button></td><td>'.$row['has_returned'].'</td><td>'.$row['start_time'].'</td><td>'.$row['timestamp'].'</td></tr>';
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
		$desktopEntries = 0;
		$tabletEntries = 0; 
		$mobileEntries = 0;
		
		$accepted = 0;
		$deciding =  0; 
		$waiting =  0;
		$other = 0;
		
		
		$deviceEntries	= "SELECT av.`device_type`, COUNT(*) as `count` FROM `form_entry` fe INNER JOIN `analytics_visitors` av ON fe.`visitorID` = av.`visitor_id` WHERE fe.`projectID` = :projectID GROUP BY av.`device_type`";
		$query = $conn->prepare( $deviceEntries );
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$deviceEntries = $query->fetchAll();
			//pa( $deviceEntries );
			if( is_array( $deviceEntries ) ){
				for($de = 0; $de < sizeof( $deviceEntries ); $de++ ){
					if( $deviceEntries[$de]["device_type"] == 1 ){
						$desktopEntries = $deviceEntries[$de]["count"];
					}else if( $deviceEntries[$de]["device_type"] == 2 ){
						$tabletEntries = $deviceEntries[$de]["count"];
					}else if( $deviceEntries[$de]["device_type"] == 3 ){
						$mobileEntries = $deviceEntries[$de]["count"];
					}
				}
			}
		}
		
		$twitterNames = "SELECT COUNT( DISTINCT  `twitter` ) as `count` FROM  `form_entry` WHERE  `twitter` !=  '' AND  `projectID` =:projectID";
		$query = $conn->prepare( $twitterNames );
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$twitterNames = $query->fetch();
			//pa( $twitterNames );
			if( is_array($twitterNames) ){
				$twitterNames = $twitterNames["count"];
			}else{
				$twitterNames = 0;
			}
		}
		
		
		$entryReasons = "SELECT fe.`other` , COUNT( * ) as `count` FROM  `form_entry` fe INNER JOIN  `analytics_visitors` av ON fe.`visitorID` = av.`visitor_id` WHERE fe.`projectID` =:projectID GROUP BY fe.`other` ";
		$query = $conn->prepare( $entryReasons );
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$entryReasons = $query->fetchAll();
			//pa( $entryReasons );
			if( is_array( $entryReasons ) ){
				for($er = 0; $er < sizeof( $entryReasons ); $er++ ){
					if( $entryReasons[$er]["other"] == 1 ){ //accepted
						$accepted = $entryReasons[$er]["count"];
					}else if( $entryReasons[$er]["other"] == 2 ){ //still deciding
						$deciding = $entryReasons[$er]["count"];
					}else if( $entryReasons[$er]["other"] == 3 ){ //waiting to hear 
						$waiting = $entryReasons[$er]["count"];
					}else if( $entryReasons[$er]["other"] == 4 ){ //other
						$other = $entryReasons[$er]["count"];
					}
				}
			}
		}
		
		
		
		
		$multiplays = "SELECT COUNT( av.`has_returned` ) AS  `count` FROM  `analytics_visitors` av INNER JOIN  `form_entry` fe ON fe.`entryID` = av.`entryID` WHERE av.`has_returned` > 0 AND av.`project_id` =:projectID";
		$query = $conn->prepare( $multiplays );
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$multiplays = $query->fetch();
			if( is_array($multiplays) ){
				$multiplays = $multiplays["count"];
			}else{
				$multiplays = 0;
			}
		}
		
		
		function nicePercent($num, $divisor){
			return (round( ($num/$divisor) ,2) * 100);
		}
		
		
		$totalEntries = ($desktopEntries + $tabletEntries + $mobileEntries);
		$totaldecisions = ( $deciding + $waiting + $accepted + $other );
		
	?>
	<p>Number of Computer Entries: <?php echo $desktopEntries; ?> - <?php echo nicePercent($desktopEntries, $totalEntries)."%" ?><br />
	   Number of Tablet Entries:  <?php echo $tabletEntries; ?> - <?php echo nicePercent($tabletEntries, $totalEntries)."%" ?><br />
	   Number of Mobile Entries   <?php echo $mobileEntries; ?> - <?php echo nicePercent($mobileEntries, $totalEntries)."%" ?><br /><br />
	   Twitter Usernames collected: <?php echo $twitterNames; ?>- <?php echo nicePercent($twitterNames, $totalEntries)."%" ?><br /><br/>
	   People on the Fence: <?php echo $deciding; ?> - <?php echo nicePercent($deciding, $totaldecisions)."%" ?><br />
	   People waiting to hear from other Universities: <?php echo $waiting; ?> - <?php echo nicePercent($waiting, $totaldecisions)."%" ?><br />
	   People who have accepted the offer: <?php echo $accepted; ?> - <?php echo nicePercent($accepted, $totaldecisions)."%" ?><br />
	   People who have listed another reason:<?php echo $other; ?> - <?php echo nicePercent($other, $totaldecisions)."%" ?><br /><br />
	   People who played more than once: <?php echo $multiplays;?> - <?php echo nicePercent($multiplays, $totalEntries)."%" ?><br />
	</p>
	   
	
	
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
					echo '<tr><td>'.$i.'</td><td>'.$row['entryID'].'</td><td>'.$row['visitorID'].' <a class="button wa" onClick="deletePath('.$row['visitorID'].', this)"><i class="fa fa-trash-o"></i> Delete</a></td><td>'.$row['firstName'].'</td><td>'.$row['lastName'].'</td><td>'.$row['email'].'</td><td>'.$row['telephone'].'</td><td>'.$row['twitter'].'</td><td>'.getReason( $row['other'] ).'</td><td>'.$row['other_reason'].'</td><td></td><td></td><td></td><td></td><td>'.$row['timestamp'].'</td></tr>';
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

	
	<h3>Possible Duplicate Entries</h3>
	<?php
		$query = $conn->prepare("SELECT *, COUNT(*) c FROM `form_entry` WHERE `projectID` = :projectID GROUP BY `email` HAVING c > 1 
		UNION
		SELECT *, COUNT(*) c FROM `form_entry` WHERE `projectID` = :projectID GROUP BY `telephone` HAVING c > 1
		UNION
		SELECT *, COUNT(*) c FROM `form_entry` WHERE `projectID` = :projectID AND `twitter` != '' GROUP BY `twitter` HAVING c > 1");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$result = $query->fetchAll();
			if( is_array( $result )  && $query->rowCount() > 0 ){
		?>
				<table class="tablesorter">
				<thead> 
					<tr>
						<th>#</th><th>EntryID</th><th>Visitor ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Telephone</th><th>Twitter Name</th><th>Offer</th>
						<th>Other Reason</th><th>Device</th><th>IP</th><th>Has Returned</th><th>Tour Start</th><th>Contest TimeStamp</th><th>Entries</th>
					</tr> 
				</thead>
				<tbody>
				<?php
				$i = 1;
				foreach( $result as $row ){
					echo '<tr><td>'.$i.'</td><td>'.$row['entryID'].'</td><td>'.$row['visitorID'].' <a class="button wa" onClick="deletePath('.$row['visitorID'].', this)"><i class="fa fa-trash-o"></i> Delete</a></td><td>'.$row['firstName'].'</td><td>'.$row['lastName'].'</td><td>'.$row['email'].'</td><td>'.$row['telephone'].'</td><td>'.$row['twitter'].'</td><td>'.getReason( $row['other'] ).'</td><td>'.$row['other_reason'].'</td><td></td><td></td><td></td><td></td><td>'.$row['timestamp'].'</td><td>'.$row['c'].'</td></tr>';
					$i++;
				}
				?>
				</tbody>
				</table>
		<?php
			}
			
		}
	?>
	
	<br />
	<br />
	<a class="button wa" onClick="clearContestEntries(<?php echo $_REQUEST['projectID']; ?>)"><i class="fa fa-eraser"></i> Clear Contest Entries</a>
	<br />	<br />


	<?php footerMenu($_REQUEST['projectID']); ?>



	<?php
		}else{
		echo "<h1>Error: a project ID is required</h1>";
	}
	pageFooter();
?>