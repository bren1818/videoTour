<?php
	require_once("../../includes/includes.php");
	pageHeader();
	
	
	$adminSession = new adminSession();
	$userID = $adminSession->getCurrentUserID();
	$admin = new administrator( getConnection() );
	$admin = $admin->load( $userID );
	
	$userToEdit = new  administrator( getConnection() );
	
	//check to see what type / what user can do
	if( isset($_REQUEST['new'] ) && $_REQUEST['new'] == 1 ){
		//new user
		echo '<p>Creating a new profile</p>';
		
	
	}else if( isset($_REQUEST['id'] ) && $_REQUEST['id'] != "" ){
		$id = $_REQUEST['id'];
		//other members profile
		if( $admin->getType() == 1 ){
			if( $id == $userID ){
				echo '<p>Modifying your own profile</p>';
				$userToEdit = $admin;
			}else{
				$userToEdit = $userToEdit->load( $id );
				if( is_object( $userToEdit ) ){
					echo '<p>Editting '.$userToEdit->getUsername().'\'s profile</p>';
				}else{
					echo '<p>Editting other user\'s profile</p>';
				}
				
			}
		}else{
			echo '<p>You cannot edit profiles other than your own - you don\'t have the privileges</p>';
			$userToEdit = array();
		}
	}else{
		//your profile
		echo '<p>Modifying your own profile</p>';
		$userToEdit = $admin;
	}
	
	if( is_object( $userToEdit ) ){
	
	}else{
		if(  isset($_REQUEST['id']) && $admin->getType() == 1){
			echo '<p>Sorry, that profile could not be loaded.</p>';
		}else{
		
		}
	}
?>



<?php
	pageFooter();
?>