<?php
	include "includes.php";
	ob_clean();
	header('Content-Type: text/css; charset=UTF-8');
	//header('Content-Disposition: attachement; filename="projectCSS.css";');
	echo "/**Generated CSS FILE FOR PROJECT**/\r\n";
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] != "" ){
		$projID = $_REQUEST['projectID'];
		$conn = getConnection();
		$query = $conn->prepare("SELECT `CSS` FROM `css` WHERE `projectID` = :projectID");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$CSS = $query->fetch();
			//pa($CSS);
			if( isset( $CSS['CSS'] ) ){
				echo $CSS['CSS'];
			}
		}
	}
?>