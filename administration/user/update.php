<?php
	require_once("../../includes/includes.php");
	pageHeader();
	
	
	$adminSession = new adminSession();
	$userID = $adminSession->getCurrentUserID();
	$admin = new administrator( getConnection() );
	$admin = $admin->load( $userID );
	
	//check to see what type / what user can do
	
?>



<?php
	pageFooter();
?>