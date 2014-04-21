<?php
	include "includes.php";
	ob_clean();
	header('Content-Type: text/javascript');
	echo "/**Generated JS FILE FOR PROJECT**/\r\n";
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] != "" ){
		$projID = $_REQUEST['projectID'];
		$conn = getConnection();
		$query = $conn->prepare("SELECT `JAVASCRIPT` FROM `js` WHERE `projectID` = :projectID");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$JS = $query->fetch();
			if( isset( $JS['JAVASCRIPT'] ) ){
				echo $JS['JAVASCRIPT'];
			}
		}
	}
?>