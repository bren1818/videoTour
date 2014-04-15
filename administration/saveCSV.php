<?php
	session_start();
	error_reporting(E_ALL);
	include "../includes/includes.php";
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projID = $_REQUEST['projectID'];
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projID);
		$withoutAnalytics = 0;
		
		if( isset( $_REQUEST['woAnalytics'] ) && $_REQUEST['woAnalytics'] ){
			$withoutAnalytics = 1;
		}
		
		
		function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
			ob_clean();
			header('Content-Type: application/csv; charset=UTF-8');
			header('Content-Disposition: attachement; filename="'.$filename.'";');

			
			global $projID;
			logMessage( "Exporting CSV of ".$projID." for: ".$_SESSION['currentUser'], "export.log");
			
			$f = fopen('php://output', 'w');
			foreach ($array as $line) {
				fputcsv($f, $line, $delimiter);
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
		
		
		if( is_object( $project ) ){
			
			$filename = $project->getTitle()."_contest_Entry_Export_".date("Y-m-d").".csv";
			
			if( !$withoutAnalytics ){
			
			$query = $conn->prepare("SELECT f.*, av.`ip`, av.`start_time`, av.`has_returned`, av.`device_type` FROM `form_entry` f
			INNER JOIN `analytics_visitors` av 
			ON av.`entryID` = f.`entryID`
			WHERE f.`projectID` = :projectID");
			
			}else{
				$query = $conn->prepare("SELECT f.*, '', '', '', '' FROM `form_entry` f WHERE f.`projectID` = :projectID AND f.`entryID` NOT IN ( SELECT f.`entryID` FROM `form_entry` f INNER JOIN `analytics_visitors` av ON av.`entryID` = f.`entryID` WHERE f.`projectID` = :projectID )");
			
			}
			
			$finalresult =  array();
			
			$query->bindParam(':projectID', $projID);
			if( $query->execute() ){
				$result = $query->fetchAll();
				if( is_array( $result ) ){
					$finalresult[] = array("#","Visitor ID", "First Name", "Last Name", "Email", "Telephone", "Twitter", "Offer", "Other", "Device Type", "IP Address", "Returned", "Entry Time");
					$i = 1;
					foreach( $result as $row ){
						$finalresult[] = array( $i, $row['visitorID'], $row['firstName'], $row['lastName'], $row['email'], $row['telephone'], $row['twitter'], getReason( $row['other'] ), $row['other_reason'], getDevice($row['device_type']), $row['ip'], $row['has_returned'], $row['timestamp'] );
						$i++;
					}
				}
				
			}
			array_to_csv_download( $finalresult, $filename, ",");
			exit;
		}else{
			echo "error";
			exit;
		}
	}
?>

