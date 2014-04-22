<?php
	error_reporting(E_ALL);
	$adminSession; //for Global Use
	
	function footerMenu($projID){
		?>
			<p><br />
			<a href="<?php echo fixedPath; ?>/administration/project/settings?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-gear"></i> Project Settings</a>
			<a href="<?php echo fixedPath; ?>/administration/ProjectAnalytics.php?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-bar-chart-o"></i> Project Analytics</a>
			<a href="<?php echo fixedPath; ?>/administration/ProjectContestEntries?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-users"></i> Contest Entries</a>	
			<a href="<?php echo fixedPath; ?>/administration/ProjectContestEntries?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-bar-chart-o"></i> Contest Entries</a>	
			<a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projID; ?>" class="button wa"><i class="fa fa-tachometer"></i> Project Dashboard</a>
			<a href="<?php echo fixedPath; ?>/administration/MapProject.php?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-sitemap"></i> Flow Chart</a>
			
			
			<a class="button wa" href="<?php echo fixedPath; ?>/admin"><i class="fa fa-th-list"></i> Back to Admin</a>
			</p>
	  <?php
	
	}
	
	function curPageURL() {
		$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
		$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
		$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
		$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
		return $url;
	}
	
	function pageHeader($pageTitle = ""){
		//session_start();
		global $adminSession;
		$adminSession = new adminSession();

		if( $adminSession->getExpired() ){
			
			logMessage( $_SESSION['currentUser']." timed out");
			$adminSession->destroy();
			header("Location: ".fixedPath."/login.php?youTimedOut");
		}else{
			$adminSession->renew();
			$userID = $adminSession->getCurrentUserID();
			$admin = new administrator( getConnection() );
			$admin = $admin->load( $userID );
			if( is_object( $admin ) ){  
				if( $admin->getEnabled() ){
					//smooth sailing
					$admin->upateActivity();//update last activity
					
					logMessage("User: ".$admin->getUsername()." Accessed: ".curPageURL(), "access.log");
					
				}else{
					$adminSession->destroy();
					$admin = array();
					header("Location: ".fixedPath."/login.php?yourAccountisDisabled");
				}
			}else{
				$adminSession->destroy();
				header("Location: ".fixedPath."/login.php?couldNotLoadProfile");
			}
		}
		
		ob_clean();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title><?php echo (($pageTitle == "") ? "Video Tour" : $pageTitle); ?></title>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/jquery-ui-1.10.4.custom.min.css"/>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/font-awesome.min.css"/>
			
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/tablesorter/blue/style.css"/>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/colorbox.css"/>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/style.css"/>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/chosen.min.css"/>
			<link rel="shortcut icon" type="image/x-icon" href="<?php echo fixedPath; ?>/favicon.ico">

			<meta http-equiv="expires" content="Sun, 01 Jan 2014 00:00:00 GMT"/>
			<meta http-equiv="pragma" content="no-cache" />
			
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jquery-1.10.2.min.js"></script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jquery-ui-1.10.4.custom.min.js"></script>

			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jQuery.jPlayer.2.5.0/jquery.jplayer.min.js"></script>
			<script>
				var serverHost = "<?php echo fixedPath; ?>";
			</script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/scripts.js"></script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jquery.colorbox-min.js"></script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jquery.tablesorter.min.js"></script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/chosen.jquery.min.js"></script>
			
			<!--
			Video Tour Web Application
			By: Brendon Irwin
			For: Wilfrid Laurier University 
			-->
		</head>
		<body>
		<div id="admin_content">
			<div id="adminBar">
				<a class="wa button" href="<?php echo fixedPath; ?>/login.php?logOut=1"><i class="fa fa-power-off"></i> Log Out</a>
			</div>
		
		<?php
		
		//pa( $adminSession );
		$userID = $adminSession->getCurrentUserID();
		$admin = new administrator( getConnection() );
		$admin = $admin->load( $userID );
		
		if( is_object( $admin ) ){
			echo "<p><b>".$adminSession->getCurrentUser()."</b> active for: ".gmdate("H:i:s", $adminSession->getDuration() ).(($admin->getType() == 1) ? " <a class='button wa' href='".fixedPath."/administration/admin/index.php'><i class='fa fa-cogs'></i> Admin Functions</a>" : " <a class='button wa' href='".fixedPath."/administration/user/update?userID=".$admin->getId()."'><i class='fa fa-user'></i> Update Profile</a>")."</p>";
		}
		
	}
		function checkAccess( $projectID, $return = 0 ){
			//simple security check :D
			global $adminSession;
			$userID = $adminSession->getCurrentUserID();
			$admin = new administrator( getConnection() );
			$admin = $admin->load( $userID );
			if(  ! in_array( $projectID, $admin->getProjectsAsArray() ) && $admin->getType() == 2 ){
				if( $return ){
					return 0;
				}else{
					echo '<h1>Access Denied!</h1>';
					pageFooter();
					exit;
				}
			}
			
			if( $return ){
				return 1;
			}
		}
	
	
		function pageHeaderShow($pageTitle = "", $projectID=0){
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
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/font-awesome.min.css"/>
			<link rel="shortcut icon" type="image/x-icon" href="<?php echo fixedPath; ?>/favicon.ico">
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jquery-1.10.2.min.js"></script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jQuery.jPlayer.2.5.0/jquery.jplayer.min.js"></script>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/playerHome/jplayer.blue.monday.css"/>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/ProjectHome.css" />
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/includes/projectCSS.php?projectID=<?php echo $projectID; ?>"/>
			<meta name="viewport" content="width=480, user-scalable=no">
			<!--<meta id="Viewport" name="viewport" width="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">-->
			
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