<?php
	require_once("includes/includes.php");
	$adminSession = new adminSession();
	
	if( isset( $_REQUEST['logOut'] ) && $_REQUEST['logOut'] == 1 ){
		
		// Unset all of the session variables.
		if( isset( $_SESSION['currentUser'] ) ){
			logMessage( $_SESSION['currentUser']." logged out");
		}
		
		$adminSession->destroy();
		
		echo '<p>You have successfully logged out.</p>';
	}
	
	if( $adminSession->getExpired() == 0   ){
		header("Location: admin.php");
	}
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$admin = new administrator( getConnection() );
	
		$user = (isset($_POST["username"]) ? $_POST["username"] : "");
		$password = (isset($_POST["password"]) ? $_POST["password"] : "");
		
		$verify = $admin->verifyLogin( $user, $password);

		if( $verify > 0 ){
			$adminSession->destroy();
			$adminSession = new adminSession();
			$adminSession->setCurrentUser( $user );
			$adminSession->setCurrentUserID( $verify );
			$adminSession->renew(); 
			//user Update 
			$admin = $admin->load( $verify );
			
			if( is_object( $admin ) ){
				$admin->setLast_login(   $adminSession->getStartTime() );
				$admin->setLast_session( $adminSession->getSessionID() );	
				$saved = $admin->save();
				logMessage( $user." logged in successfully");
				header("Location: admin.php");
			}
			exit;
		}else{
			echo "<p>Incorrect Username or Password</p>";
			logMessage( $_SESSION['currentUser']." logged un-successfully");
		}
	}
?>

<form method="post" action="login.php">
	<label for="username">Username: <input name="username" type="text" value="<?php echo isset( $_POST['username'] ) ? $_POST['username'] : "";  ?>" /></label><br />
	<label for="password">Password: <input name="password" type="password" value="" /></label><br />
	<input type="submit" value="Login" />
</form>