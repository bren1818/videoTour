<?php
	require_once("../../includes/includes.php");
	pageHeader();
	
	$adminSession = new adminSession();
	$userID = $adminSession->getCurrentUserID();
	$admin = new administrator( getConnection() );
	$admin = $admin->load( $userID );
	
	
	if( $admin->getType() == 1 ){
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$errors = array();
			
			if( isset( $_POST["userID"] ) &&  $_POST["userID"] != "" &&	isset( $_POST["deletor"] ) &&  $_POST["deletor"] != "" && isset( $_POST["username"] ) &&  $_POST["username"] != ""){
				if( $_POST["deletor"] == $admin->getId() ){
					
					$user = new administrator( getConnection() );
					$user = $user->load( $_POST["userID"] );
			
					if( is_object( $user ) ){
						if( $user->delete() ){
							$errors[] = "User (".$_POST["username"].") was deleted successfully";
						}else{
							$errors[] = "Could not Delete profile";
						}
					}else{
						$errors[] = "Could not load profile";
					}
				}else{
					$errors[] = "Deletor ID doesn't match you. Deletion terminated.";
				}
			}else{
				$errors[] = "Deletion terminated. Information missing";
			}
			
			bulletArray( $errors );
			
			
		}
		
		
		if( isset($_REQUEST['id']) && $_REQUEST['id'] != "" ){
			$id = $_REQUEST['id'];
			
			$user = new administrator( getConnection() );
			$user = $user->load( $id );
			if( is_object( $user ) ){
				echo "<h3>Are you sure you wish to delete: ".$user->getUsername()."'s account?<h3>";
				?>
				<form name="confirm" method="post" action="delete.php">
					<input type="submit" value="Delete it" />
					<input type="hidden" name="userID" value="<?php echo $user->getId(); ?>" />
					<input type="hidden" name="username" value="<?php echo $user->getUsername(); ?>" />
					<input type="hidden" name="deletor" value="<?php echo $admin->getId(); ?>" />
					
				</form>
				<?php
			}else{
				echo "<p><b>Cannot modify or load User.</b></p>";
			}
		}
	}else{
		echo "<h2>You don't have the privileges to delete accounts</h2>";
	}
	echo  "<p><a class='button wa' href='".fixedPath."/administration/admin/index.php'>Back to Admin Functions</a></p>";
	pageFooter();
?>