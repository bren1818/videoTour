<?php
	error_reporting(E_ALL);
	
	
	function footerMenu($projID){
		?>
			<p><br />
			<a href="<?php echo fixedPath; ?>/administration/project/edit?id=<?php echo $projID; ?>" class="button wa"><i class="fa fa-tachometer"></i> Project Dashboard</a>
			<a href="<?php echo fixedPath; ?>/administration/project/settings?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-gear"></i> Project Settings</a>
			<a href="<?php echo fixedPath; ?>/administration/ProjectAnalytics.php?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-bar-chart-o"></i> Project Analytics</a>
			<a href="<?php echo fixedPath; ?>/administration/ProjectContestEntries?projectID=<?php echo $projID; ?>" class="button wa"><i class="fa fa-users"></i> Contest Entries</a>	
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
			
			<!--<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/tablesorter/blue/style.css"/>-->
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/demo_table_jui.css"/>
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
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jquery.dataTables.js"></script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/chosen.jquery.min.js"></script>
			<!--
			Video Tour Web Application
			By: Brendon Irwin
			For:
			 __      __.__.__   _____       .__    .___   
			/  \    /  \__|  |_/ ____\______|__| __| _/   
			\   \/\/   /  |  |\   __\\_  __ \  |/ __ |    
			 \        /|  |  |_|  |   |  | \/  / /_/ |    
			  \__/\  / |__|____/__|   |__|  |__\____ |    
				   \/                               \/    
			.____                        .__              
			|    |   _____   __ _________|__| ___________ 
			|    |   \__  \ |  |  \_  __ \  |/ __ \_  __ \
			|    |___ / __ \|  |  /|  | \/  \  ___/|  | \/
			|_______ (____  /____/ |__|  |__|\___  >__|   
					\/    \/                     \/  
			
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
		
	
		function pageHeaderShow($pageTitle = "", $projectID=0){
		ob_clean();
		?>
		<!DOCTYPE html>
		<html lang="en"> 
		<head>
			<!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
			<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
			<!--
				Video Tour Web Application
				By: Brendon Irwin
				For:
				 __      __.__.__   _____       .__    .___   
				/  \    /  \__|  |_/ ____\______|__| __| _/   
				\   \/\/   /  |  |\   __\\_  __ \  |/ __ |    
				 \        /|  |  |_|  |   |  | \/  / /_/ |    
				  \__/\  / |__|____/__|   |__|  |__\____ |    
					   \/                               \/    
				.____                        .__              
				|    |   _____   __ _________|__| ___________ 
				|    |   \__  \ |  |  \_  __ \  |/ __ \_  __ \
				|    |___ / __ \|  |  /|  | \/  \  ___/|  | \/
				|_______ (____  /____/ |__|  |__|\___  >__|   
						\/    \/                     \/  
				
			-->
			<title><?php echo (($pageTitle == "") ? "Video Tour" : $pageTitle); ?></title>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/font-awesome.min.css"/>
			<link rel="shortcut icon" type="image/x-icon" href="<?php echo fixedPath; ?>/favicon.ico">
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jquery-1.10.2.min.js"></script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/jQuery.jPlayer.2.5.0/jquery.jplayer.min.js"></script>
			<script type="text/javascript" src="<?php echo fixedPath; ?>/js/modernizr.js"></script>	
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/playerHome/jplayer.blue.monday.css"/>
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/css/ProjectHome.css" />
			<link rel="stylesheet" href="<?php echo fixedPath; ?>/includes/projectCSS.php?projectID=<?php echo $projectID; ?>"/>
			<meta id="Viewport" name="viewport" content="user-scalable=no, width=480"/>
			<meta name="HandheldFriendly" content="true"/>
			<meta name="MobileOptimized" content="480"/> 
			
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