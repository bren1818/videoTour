<?php
	require_once("../../includes/includes.php");
	pageHeader();
	
	$adminSession = new adminSession();
	$userID = $adminSession->getCurrentUserID();
	$admin = new administrator( getConnection() );
	$admin = $admin->load( $userID );
	
	
	if( $admin->getType() != 1 ){
		?>
		<h1>Access Denied</h1>
		<?php
	}else{
?>
	<h1>Super Admin</h1>

	<a href="/admin">Back to Admin</a>
<?php
	}
	pageFooter();
?>