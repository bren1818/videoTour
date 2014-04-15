<?php
	error_reporting(E_ALL);

	function pageHeader($pageTitle = ""){
		//session_start();
		$adminSession = new adminSession();

		if( $adminSession->getExpired() ){
			$adminSession->destroy();
			logMessage( $_SESSION['currentUser']." timed out");
			header("Location: /login.php?youTimedOut");
		}else{
			$adminSession->renew();
			$userID = $adminSession->getCurrentUserID();
			$admin = new administrator( getConnection() );
			$admin = $admin->load( $userID );
			if( is_object( $admin ) ){  
				if( $admin->getEnabled() ){
					//smooth sailing
					$admin->upateActivity();//update last activity
				}else{
					$adminSession->destroy();
					$admin = array();
					header("Location: /login.php?yourAccountisDisabled");
				}
			}else{
				$adminSession->destroy();
				header("Location: /login.php?couldNotLoadProfile");
			}
		}
		
		ob_clean();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title><?php echo (($pageTitle == "") ? "Video Tour" : $pageTitle); ?></title>
			<link rel="stylesheet" href="/css/jquery-ui-1.10.4.custom.min.css"/>
			<link rel="stylesheet" href="/css/font-awesome.min.css"/>
			
			<link rel="stylesheet" href="/css/tablesorter/blue/style.css"/>
			<link rel="stylesheet" href="/css/colorbox.css"/>
			<link rel="stylesheet" href="/css/style.css"/>
			<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

			<meta http-equiv="expires" content="Sun, 01 Jan 2014 00:00:00 GMT"/>
			<meta http-equiv="pragma" content="no-cache" />
			
			<script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
			<script type="text/javascript" src="/js/jquery-ui-1.10.4.custom.min.js"></script>
			<!--http://jplayer.org/download/, http://jplayer.org/latest/quick-start-guide/-->
			<script type="text/javascript" src="/js/jQuery.jPlayer.2.5.0/jquery.jplayer.min.js"></script>
			<script type="text/javascript" src="/js/scripts.js"></script>
			<script type="text/javascript" src="/js/jquery.colorbox-min.js"></script>
			<script type="text/javascript" src="/js/jquery.tablesorter.min.js"></script>
			
			<!--
			Video Tour Web Application
			By: Brendon Irwin
			For: Wilfrid Laurier University 
			-->
		</head>
		<body>
		<div id="admin_content">
			<div id="adminBar">
				<a class="wa button" href="/login.php?logOut=1">Log Out</a>
			</div>
		
		<?php
		
		//pa( $adminSession );
		echo "<p>".$adminSession->getCurrentUser()." active for: ".$adminSession->getDuration()."(s)".(($admin->getType() == 1) ? " <a href='/administration/admin/index.php'>Admin Functions</a>" : "")."</p>";
		
		
	}
	
		function pageHeaderShow($pageTitle = ""){
		ob_clean();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<!--
				Video Tour Web Application
				By: Brendon Irwin
				For: Wilfrid Laurier University 
			-->
			<title><?php echo (($pageTitle == "") ? "Video Tour" : $pageTitle); ?></title>
			<link rel="stylesheet" href="/css/font-awesome.min.css"/>
			<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
			<script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
			<script type="text/javascript" src="/js/jQuery.jPlayer.2.5.0/jquery.jplayer.min.js"></script>
			<link rel="stylesheet" href="/css/playerHome/jplayer.blue.monday.css"/>
			<link rel="stylesheet" href="/css/ProjectHome.css" />
			<meta id="Viewport" name="viewport" width="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
		</head>
		<body>
		
		<?php
	}
	
	function pageFooterShow(){
		?>
		<div class="clear"></div>
		
		</body>
		</html>
		<?php
	}
	
	
	function pageFooter(){
		?>
		<div class="clear"></div>
		</div>
		</body>
		</html>
		<?php
	}
?>