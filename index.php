<?php
	require_once("includes/includes.php");
	require_once("includes/Mobile_Detect.php");
	
	$session = session_id();
	if ($session == ''){ session_start(); }
	$_SESSION['sessionid'] = session_id();
	
	$detect = new Mobile_Detect();
	//$deviceType = ($detect->isMobile() ? (  $detect->isTablet() ? 2 : 3 ) : 1);
	
	if( isset( $_REQUEST['tourID'] ) && $_REQUEST['tourID'] != "" ){

		$tourID =  $_REQUEST['tourID'];
	
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($tourID);
		
		if( is_object($project) ){
			$title = $project->getTitle();
			
			if( $project->isActive() ){
				pageHeaderShow( $title, $project->getId() ); 
				if( $project->getStartingSegmentID() == null || $project->getStartingSegmentID() == ""){
					echo "<p>Error - This project doesn't have a start</p>";
				}else if(  $project->getStartingSegmentID() != "" ){
					$segments = new Segments($conn);
					$segment = $segments->load( $project->getStartingSegmentID() );
					
					if( is_object($segment) && $segment->getClipID() != "" ){
					//$clip = new Clip($conn);

					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
						$ip = $_SERVER['HTTP_CLIENT_IP'];
					} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
						$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
					} else {
						$ip = $_SERVER['REMOTE_ADDR'];
					}
				?>	
					<?php if( $project->getShowCount() ){ ?><div id="currentStep">1</div><?php } ?>
					<?php if( $project->getShowBadge() ){ ?><div id="badge"></div><?php } ?>
					
					<div id="jp_container_1" class="jp-video ">
					<div class="jp-type-single">
					  <div id="jquery_jplayer_1" class="jp-jplayer"></div>
					  <div class="jp-gui">
						<div class="jp-video-play">
						  <a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
						</div>
						<div class="jp-interface">
						  <div class="jp-progress">
							<div class="jp-seek-bar">
							  <div class="jp-play-bar"></div>
							</div>
						  </div>
						  <div class="jp-current-time"></div>
						  <div class="jp-duration"></div>
						  <div class="jp-controls-holder">
							<ul class="jp-controls">
							  <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
							  <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
							  <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
							  <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
							  <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
							  <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
							</ul>
							<div class="jp-volume-bar">
							  <div class="jp-volume-bar-value"></div>
							</div>
							<ul class="jp-toggles">
							  <li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
							  <li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
							  <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
							  <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
							</ul>
						  </div>
						  <div class="jp-title">
							<ul>
							  <li><?php
								//echo $clip->getNote();
							  ?></li>
							</ul>
						  </div>
						</div>
					  </div>
					  <div class="jp-no-solution">
						<!--<span>Update Required</span>
						To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.-->
					  </div>
					</div>
					<div class="clear"></div>
					</div>

					<script type="text/javascript">
						var debug = 0; //should load from settings
						
						function logger( msg  ){
							var callerFunc = arguments.callee.caller.toString();
								callerFuncName = (callerFunc.substring(callerFunc.indexOf("function") + 8, callerFunc.indexOf("(")) || "anoynmous")
							
							if( debug){
								
								console.log( "Logger - in function:" + callerFuncName + " - Message: " +  msg );
							}
						}
					
						var state = "";
					
						var serverHost = "<?php echo fixedPath; ?>"; //"http://205.189.20.167"; //could be blank...
						var formURL = "<?php echo $project->getFormURL(); ?>";
						var showForm = <?php echo $project->getHasForm(); ?>;
						var showPoster = <?php echo $project->getShowPoster(); ?>;
						var posterFile = "<?php echo $project->getPosterFile(); ?>";
						
						var redirect = <?php echo $project->getRedirect(); ?>;
						var redirectURL = "<?php echo $project->getRedirectURL(); ?>";
						
						var windowWidth;
						var windowHeight;
						var currentStep = 1;
						var projectTitle = "<?php echo $project->getTitle(); ?>";
						var showCount = <?php echo $project->getShowCount(); ?>;
						var showBadge = <?php echo $project->getShowBadge(); ?>;
						var badgeMode = <?php echo $project->getBadgeMode(); ?>;
						
						var isTablet = <?php echo ($detect->isTablet() == "" ? 0 : $detect->isTablet()); ?>;
						var isMobile = <?php echo ($detect->isMobile() == "" ? 0 : $detect->isMobile()); ?>;
						var type = 1;
						if( isTablet == 1 && isMobile == 1 ){
							type = 2;
						}else if( isTablet == 0 && isMobile == 1){
							type = 3;
						}
						var nextStep;
						var player;
						<?php
						//new user?
							$tablet = ($detect->isTablet() == "" ? 0 : $detect->isTablet());
							$mobile = ($detect->isMobile() == "" ? 0 : $detect->isMobile());
							
							if( $mobile == 1 ){
								if( $tablet == 1 ){
									$deviceType = 2;
								}else{
									$deviceType = 3;
								}
							}else{
								$deviceType = 1;
							}
							
							$analyticUser = new analytics_visitors($conn);
							$id = 0;
							if( isset( $_SESSION['analyticUserId'] ) && $_SESSION['analyticUserId'] != "" ){
								$id = $_SESSION['analyticUserId'];
							}
							
							$userExists = $analyticUser->exists($id );

							if( $userExists ){
								$analyticUser = $analyticUser->load( $id );
								
								if( is_object( $analyticUser ) ){
									$hasEnteredContest = $analyticUser->getFilled_out_entry();
									if( $hasEnteredContest == 1 ){
									?>
						var enteredContest = 1;
									<?php
									}else{
									?>
						var enteredContest = 0;
									<?php
									}
								
									$analyticUser->setId( $id );
									$count = $analyticUser->getHasReturned();
									$analyticUser->setDeviceType($deviceType);
									$count++;
									$analyticUser->setHasReturned( $count );
									$analyticUser->save();
								}
							}else{
								$analyticUser->setIp( $ip );
								$analyticUser->setDeviceType($deviceType);
								$analyticUser->setProjectId( $tourID );
								$id = $analyticUser->save();
								$_SESSION['analyticUserId'] = $id;
								?>
						var enteredContest = 0;
								<?php
							}
						?>
						var userID = "<?php echo $id; ?>";
						var nthAction = 0;
						var PROJECT_ID = <?php echo $tourID; ?>;
						var currentSegmentID = <?php echo $project->getStartingSegmentID(); ?>;
						var currentSegmentData;
						var currentDecisions;
						var currentClipID;
						var nextStep;  //function holder...
						var started = 0;
						var shownD = 0;
						
						
					</script>
					<script type="text/javascript" src="<?php echo fixedPath; ?>/js/home.js" ></script>
					<script type="text/javascript">
						$(function(){
							
							if( isTablet == 0 && isMobile == 0 ){
								type = 1;
								
								setSize(); 
							
								$('body').attr('class', 'desktop');
								$(window).resize(function () {
									waitForFinalEvent(function(){
										//setSize();
										
									}, 500, "WindowResize");
								});
							 
							 	$('#jquery_jplayer_1').height( windowHeight );
								$('#jquery_jplayer_1').width( windowWidth );
								$('div.jp-video-play').css('height',  windowHeight + 'px' );
								$('div.jp-video-play').css('margin-top', ( -1 * windowHeight )  + 'px' );
							 
							 
							 }else{
								$('body').attr('class', 'mobile');
								type = 2;
								$('#Viewport').attr('content', 'width=480'); //user-scalable=no, maximum-scale=1, minimum-scale=1,
                                
                                $('#jquery_jplayer_1').height( windowHeight );
                                $('#jquery_jplayer_1').width( windowWidth );
                                //$('#jquery_jplayer_1').width( 480 );
                                $('div.jp-video-play').css('height',  '480px' );
							 }

							var clipPath = "";
							var segmentData = "";
							var decisions = new Array();
							
							if( currentSegmentID != 0 && currentSegmentID != "" ){
								segmentData = getAjaxHandlerResponse("getSegment", currentSegmentID );
								
								if( segmentData ){
									currentSegmentData =  segmentData;
									var clipID = segmentData.StartingClipID;
									if( clipID != 0 && clipID != "" ){
										clipPath =	getClipPath( clipID, type);
									}
								}
							
								if( clipPath != "" ){
									if(  segmentData.Decisions ){
										if( segmentData.Decisions.length > 0 ){
											for( var d = 0; d < segmentData.Decisions.length; d++ ){
												decisions[d] = { "id" : segmentData.Decisions[d].DecisionID, "text" : segmentData.Decisions[d].ButtonText, "continues" :  segmentData.Decisions[d].Continues  , "ends" : segmentData.Decisions[d].Ends , "goToSegment" : segmentData.Decisions[d].NextSegmentID  , "PlayClip" : segmentData.Decisions[d].PlaysClip } 
											}
										}
										currentDecisions = decisions;
									}	

									$("#jquery_jplayer_1").jPlayer({
										ready: function () {
											if( showPoster == 1 ){
												$(this).jPlayer("setMedia", {
													title: projectTitle,
													m4v: serverHost + clipPath,
													poster: serverHost + posterFile
												}); 
											}else{
												$(this).jPlayer("setMedia", {
													m4v: serverHost + clipPath
												}); 

											}
											
											currentClipID = clipID;
											logAction("Start");

											if( type == 1 ){
												if( showPoster != 1 ){
													$(this).jPlayer("play");
												}
												shownD = 0;
											}else{
												jQuery('video').width(windowWidth);
												jQuery('video').height(windowHeight);
											}
										},
										ended: function(){		
											if( decisions ){
												logger("index.php > ended > overlay decisions");
												overlayDecisions( decisions );
											}
										},
										size: { width: getJPlayerWidth(), height: getJPlayerHeight() },
										preload : "auto",
										volume: 1,
										swfPath: "/js",
										keyEnabled : true,
										keyBindings: {
										  play: {
											key: 32, // space
											fn: function(f) {
											  if(f.status.paused) {
												f.play();
											  } else {
												f.pause();
											  }
											}
										  },
										  fullScreen: {
											key: 13, // enter
											fn: function(f) {
											  if(f.status.video || f.options.audioFullScreen) {
												f._setOption("fullScreen", !f.options.fullScreen);
											  }
											}
										  }
										},
										supplied: "m4v",
										
										timeupdate: function(event) {
										
											//Is at end of clip?
											var d =  parseInt( Math.floor($("#jquery_jplayer_1").data("jPlayer").status.duration));
											var ct = parseInt( Math.floor(event.jPlayer.status.currentTime) );
											if( ct > 0 &&   ct >= (d - 8) && event.jPlayer.status.paused===false ){
												logger( "In timeupdate trigger - State: " + state);
												if( typeof decisions === "undefined" ){
														//let ended call this
														//nextStep();	
												}else{
													if( state == "tryagain" || state == "" ){
														$.jPlayer.pause();
														logger( "index.php - timeupdate function, overlayDecisions ");
														overlayDecisions( decisions );
													}
												}
											}
										}
										
									});
									
									
									
									if( isMobile ){ //mobile devices wont auto-play video
										showAlert('Welcome to the Mobile Tour. This tour streams video. Click the Golden Hawk to begin each clip! This content is best viewed in portrait mode.','Welcome, Do you have what it takes?');
										
										
									}
								}
							}
						});
						
						//console.log( player );
						
					</script>
					<script type="text/javascript" src="<?php echo fixedPath; ?>/includes/projectJS.php?projectID=<?php echo $tourID; ?>"></script>
				<?php
				}else{
						echo "Could not load project - missing start segment or clip id";
						logMessage( "Could not load project - missing start segment or clip id, Segment ID: ".$project->getStartingSegmentID().", projectID: ".$tourID, "user.log");
					}
				}
			}else{
				pageHeaderShow("Contest No Longer Available"); 
				logMessage( "User tried to access non available Tour ID: ".$tourID, "user.log");
				?>
				<h1>Sorry this contest is no longer running...</h1>
				<p>Please check again later!</p>
				<?php
			}
		}else{
			pageHeaderShow("Unknown Project"); 
			logMessage( "Unknown Project ".$tourID, "user.log");
			?>
			<h2>Sorry....</h2>
			<p>We don't have a project by that name...</p>
		<?php
		}
	}else{
		pageHeaderShow("Unknown Project"); 
		if( isset($tourID) ){
			logMessage( "Unknown Project ".$tourID, "user.log");
		}else{
			logMessage( "Unknown Project ", "user.log");
		}
		?>
		<h2>Sorry no tourID set.</h2>
		<p>There should be a ?tourID= someID</p>
		<?php
		logMessage( "Unknown Project no tourID provided", "user.log");
	}

	pageFooterShow();
?>