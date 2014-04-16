<?php
	require_once("../../includes/includes.php");
	pageHeader();
	
	
	$adminSession = new adminSession();
	$userID = $adminSession->getCurrentUserID();
	$admin = new administrator( getConnection() );
	$admin = $admin->load( $userID );
	
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		//pa( $_POST );
		$username =  (isset($_POST['username']) && $_POST['username'] != "" ) ? preg_replace('/\s+/', '',$_POST['username']) : ""; //error  strip spaces!
		$email =	 (isset($_POST['email']) && $_POST['email'] != "" ) ? preg_replace('/\s+/', '', $_POST['email']) : "";			 //error
		$type = 	 (isset($_POST['type']) && $_POST['type'] != "" ) ? $_POST['type'] : 2;
		$enabled =	 (isset($_POST['enabled']) && $_POST['enabled'] != "" ) ? $_POST['enabled'] : 0;
		$password =  (isset($_POST['password']) && $_POST['password'] != "" ) ? $_POST['password'] : ""; //error
		$confirm =   (isset($_POST['confirm']) && $_POST['confirm'] != "" ) ? $_POST['confirm'] : ""; 	 //error
		$nuserID =	 (isset($_POST['userID']) && $_POST['userID'] != "" ) ? $_POST['userID'] : 0; 	     //error
		$updateType = (isset($_POST['updateType']) && $_POST['updateType'] != "" ) ? $_POST['updateType'] : ""; 	     //error
		$updatePassword =   (isset($_POST['updatePassword']) && $_POST['updatePassword'] == "update" ) ? 1 : 0;
		
		
		$errors = array(); $messages =  array();
		
		if( $nuserID == "" && $updateType == "NEW" ){
			//everything required
			if( $username != "" && $email != "" && ($password == $confirm ) && $password != ""){
				//ensure $username is available!
				$user = new administrator( getConnection() );
				if( $user->userNameExists($username) > 0 ){
					$errors[] = "Username (".$username.") already Exists";
				}else{
					$user->setUsername( $username);
					$user->setEmail(	$email   );
					$user->setEnabled(  $enabled );
					$user->setType(		$type    );
					$user->save();
					
					if( $user->getId() > 0 ){
						$messages[] = "User ".$user->getUsername()." created ok with ID: ".$user->getId();
						$setPass = $user->updatePassword( $password );

						if( $setPass ){
							$messages[] = "Password set successfully";
						}else{
							$errors[] = "Could not set new user's password...";
						}
					}else{
						$errors[] = "An error has occurred, could not create new user";
					}
				}
			}else{
				$errors[] = "A Required field was not input";
			}
		}else if( $userID == $nuserID && $updateType == "SELF" ){
			//modifying your own profile
			
			$admin->setEmail(	$email   );
			$admin->setEnabled(  $enabled );
			$admin->setType(		$type    );
			$updated = $admin->save();
			if( $updated > 0 ){
				$messages[] = "Profile updated successfully";
			}else{
				$errors[] = "Could not update User...";
			}
			
			if( $updatePassword ){
				if( ($password == $confirm) && $password != "" ){
					$setPass = $admin->updatePassword( $password );
					if( $setPass ){
						$messages[] = "Password updated successfully";
					}else{
						$errors[] = "Could not update password...";
					}
				}else{
					$errors[] = "Passwords do not match, and cannot be blank";
				}
			}
		
		
		}else if( $nuserID != "" && $nuserID != $userID && $updateType == "OTHER"){
			if( $admin->getType() == 1 ){
				//modifying another user
				$user  = new administrator( getConnection() );
				$user = $user->load( $nuserID );
			
			
				$user->setEmail(	$email   );
				$user->setEnabled(  $enabled );
				$user->setType(		$type    );
				$updated = $user->save();
				if( $updated > 0 ){
					$messages[] = "Profile updated successfully";
				}else{
					$errors[] = "Could not update User...";
				}
				
				if( $updatePassword ){
					if( ($password == $confirm) && $password != "" ){
						$setPass = $user->updatePassword( $password );
						if( $setPass ){
							$messages[] = "Password updated successfully";
						}else{
							$errors[] = "Could not update password...";
						}
					}else{
						$errors[] = "Passwords do not match, and cannot be blank";
					}
				}
			
			}else{
				$errors[] = "You do not have the privilege of modifying other's accounts.";
			}
		}else{
			$errors[] = "An error has occurred or an invalid operation was attempted.";
		}
		
		
		if( sizeof( $errors ) > 0 ){
			echo "<p>Errors:</p>";
			bulletArray( $errors );
		}
		
		if( sizeof( $messages ) > 0 ){
			echo "<p>Messages: </p>";
			bulletArray( $messages );
		}
		
		echo  "<a href='".fixedPath."/administration/admin/index.php'>Back to Admin Functions</a>";
		
		pageFooter();
		exit;
	}
	
	
	$userToEdit = new  administrator( getConnection() );
	
	$updateType = 0;
	//check to see what type / what user can do
	if( isset($_REQUEST['new'] ) && $_REQUEST['new'] == 1 ){
		//new user
		echo '<p>Creating a new profile</p>';
		$updateType = "NEW";
	
	}else if( isset($_REQUEST['id'] ) && $_REQUEST['id'] != "" ){
		$id = $_REQUEST['id'];
		//other members profile
		if( $admin->getType() == 1 ){
			if( $id == $userID ){
				echo '<p>Modifying your own profile</p>';
				$userToEdit = $admin;
				$updateType = "SELF";
			}else{
				$userToEdit = $userToEdit->load( $id );
				if( is_object( $userToEdit ) ){
					echo '<p>Editting '.$userToEdit->getUsername().'\'s profile</p>';
					$updateType = "OTHER";
					
				}
			}
		}else{
			echo '<p>You cannot edit profiles other than your own - you don\'t have the privileges</p>';
			$userToEdit = array();
			
			pageFooter();
			exit;
		}
	}else{
		//your profile
		echo '<p>Modifying your own profile</p>';
		$userToEdit = $admin;
		$updateType = "SELF";
	}
	
	if( is_object( $userToEdit ) ){
	
	}else{
		if(  isset($_REQUEST['id']) && $admin->getType() == 1){
			echo '<p>Sorry, that profile could not be loaded.</p>';
		}else{
		
		}
	}
?>
	<form method="post" action="update.php">
		<table>
			<tr><td><label for="username">Username:</label></td><td><input type="text" name="username" id="username" value="<?php echo $userToEdit->getUsername(); ?>" <?php if($userToEdit->getId() != ""){ echo "disabled='disabled'"; }else{ echo 'required="required"'; } ?>/></td></tr>
			<tr><td><label for="email">Email:</label></td><td><input type="email" name="email" id="email" value="<?php echo $userToEdit->getEmail(); ?>" required="required"/></td></tr>
			<?php if( $admin->getType() == 1 ){ ?>
				<tr><td><label for="type">Type:</label></td><td><input type="number" name="type" id="type" value="<?php echo ($userToEdit->getType() != "") ? $userToEdit->getType() : "2"; ?>" min="1" max="2" required="required"/>(1=SA,2=A)</td></tr>
				<tr><td><label for="enabled">Enabled:</label></td><td><input type="checkbox" name="enabled" id="enabled" value="1" <?php if( $userToEdit->getEnabled() == 1){ echo " checked"; }?>/></td></tr>
			<?php } ?>
		
			<?php if( $userToEdit->getId() != "" ){ ?>
				<tr><td>Update Password:</td><td><input type="password" name="password" id="password" value="" /> </td></tr>
				<tr><td>Confirm Password:</td><td><input type="password" name="confirm" id="confirm" value="" /></td></tr>
				<tr><td>Update Password:</td><td><input name="updatePassword" type="checkbox" value="update" /></td></tr>
			<?php }else{ ?>
				<tr><td>Password:</td><td><input type="password" name="password" id="password" required="required" value=""/></td></tr>
				<tr><td>Confirm Password:</td><td><input type="password" name="confirm" id="confirm" required="required" value=""/></td></tr>
			<?php } ?>
		</table>
		<input type="submit" value="Save" class="button wa" />
		<input type="hidden" name="userID" value="<?php echo $userToEdit->getId(); ?>">
		<input type="hidden" name="updateType" value="<?php echo $updateType; ?>" />
	</form>

	<p><a class="button wa" href="<?php echo fixedPath; ?>/admin"><i class="fa fa-th-list"></i> Back to Admin</a></p>
<?php
	pageFooter();
?>