function closeModal(){
	if( $('#modalOverlay').length ){
		$('#modalOverlay').remove();
	}
}

function showAlert(message,title,buttonText){
	$(function(){
		var wh = $(window).height();
		var ww = $(window).width();
	
		if( $('#modalOverlay').length ){
			$('#modalOverlay').remove();
		}
		
		var html= "<div style='height: " + wh + "px; width: " + ww + "px;' id='modalOverlay'><div id='alert' style='top: " + ((wh /2 ) - 100) + "px;'><div class='titlebar'>" + title + "</div><div class='alertMessage'><p>" + message + "</p></div><div class='buttonHolder'><p style='text-align:center;'><a onClick='closeModal()' class='button'>" + buttonText + "</a></p></div></div></div>"; 
		$('body').append( html );
	});
}

function showLinkAlert(message,title,buttonText,link){
	$(function(){
		var wh = $(window).height();
		var ww = $(window).width();
	
		if( $('#modalOverlay').length ){
			$('#modalOverlay').remove();
		}
		
		var html= "<div style='height: " + wh + "px; width: " + ww + "px;' id='modalOverlay'><div id='alert' style='top: " + ((wh /2 ) - 100) + "px;'><div class='titlebar'>" + title + "</div><div class='alertMessage'><p>" + message + "</p></div><div class='buttonHolder'><p style='text-align:center;'><a href='" + link + "' class='button wa'>" + buttonText + "</a></p></div></div></div>"; 
		$('body').append( html );
	});
}


function getAjaxHandlerResponse(fx, id){
	//projectID, what, function
	var ret = null;
	$.ajaxSetup({async: false});
	$.get( serverHost +  "/requestHandler.php", { fx : fx, id : id }, function( data ) {
		ret =  jQuery.parseJSON( data );
	});
	$.ajaxSetup({async: true});
	return ret;
}

function getClipPath( clipID, clipType ){
	var clip = getAjaxHandlerResponse("getClip", clipID);
	var path = "";
	var thetype;
	if( typeof clipType == "undefined"){
		thetype = type; //fall back to the global type
	}else{
		thetype = clipType;
	}
	
	if( clip ){
		if( clip.Clips ){
			if( clip.Clips.length > 0 ){
				for( var c = 0; c < clip.Clips.length; c++ ){
					if( clip.Clips[c].ClipType == thetype ){
						path = clip.Clips[c].ClipPath;
						break;
					}
				}
			}else{
				
			}
		}
	
	}
	return path;	
}

function overlayDecisions(decisions){
	
	if( shownD == 1 ){
	
	}else{
		shownD = 1;
		logger(" In overlayDecisions");
		$('#clickActions').remove();
		var html = "<div id='clickActions' style='height: " + getJPlayerHeight() + "px; width: 100%;' class='actions'>";
		//logger( currentSegmentData );
		if( currentSegmentData && currentSegmentData.Question ){
			if( currentSegmentData.Question != "" ){
				html+= '<div id="currentQuestion" style="width:' + windowWidth + 'px">' + currentSegmentData.Question + '</div>';
			}
		}
		
		if (decisions instanceof Array) {
			logger("Showing : " + decisions.length + " decisions");
			
			
			
			html+= "<ul class='decisions decisions-" + decisions.length + "'>";
			decisions.forEach(function(choice) { //foreach
				html+= '<li id="choice_' + choice.id +'" class="actionButton" onClick="doAction(' + choice.goToSegment + "," + choice.PlayClip + "," + choice.continues + "," + choice.ends  + "," + choice.ForcedBadgeID + ')"><strong>' + choice.text + '</strong></li>';
			});
			html+="</ul>";
			
		}
		html+="</div>";
		if( type == 1 ){
			$('.jp-jplayer').append(html);
		}else{
			$('body').append(html);
		}
	}
}


function logAction(actionEvent){
	var saved;
	$.ajaxSetup({async: false});
	$.get( serverHost + "/requestHandler.php", { fx : "recordEvent", userID : userID, projectID: PROJECT_ID, eventType : actionEvent, step :  currentStep , actionCount : nthAction, SegmentID :currentSegmentID, clipID : currentClipID  }, function( data ) {
		saved =  jQuery.parseJSON( data );
	});
	$.ajaxSetup({async: true});
}

function playAgain(){
	window.location = window.location;
}

function finished(){
	if( enteredContest == 0 ){
	
		if( showForm == 1 ){
			showAlert("You have what it takes to be a Laurier Golden Hawk!<br />Please complete this form for a chance to win 1 of 10 $200 Laurier Bookstore gift certificates.", "Congratulations!", "Enter Contest");
			logAction("Finished");
			
			//hide the other stuff
			$('#currentStep, #badge, #jp_container_1').hide();
			//load the registration form
			$('body').append('<div id="entryForm"></div>');
			$( "#entryForm" ).load( formURL, function() {
				//alert( "Load was performed." );
				$('#registrationForm').append('<input type="hidden" name="visitorID" value="' + userID +'" /><input type="hidden" name="projectID" value="' + PROJECT_ID + '" /><input type="hidden" name="silentSave" value="1" />');
				$('#registrationForm').submit(function(event) {
				  event.preventDefault();
					var datastring = $('#entryForm form').serialize();
					$.ajax({
						type: "POST",
						url: formURL,
						data: datastring,
						dataType: "json",
						success: function(data) {
							if( data ){
								if( data.Saved ){
									if( data.Saved == 1 ){
										if( redirect == 1 ){
											//window.location = redirectURL;
											showLinkAlert("Thanks for entering our contest! Winners will be announced the week of May 26, 2014.", "Good Luck! Entry recorded!", "Return to website", redirectURL );
											
											
											
										}else{
											$('body #entryForm').remove();
											$('body').html("<marquee><h1>Thanks for Playing!</h1></marquee><center><blink><a class='playAgain' onClick='playAgain()'>Play again?</a></blink></center>");
											showAlert("Thank you for participating! You will be notified if you're a winner.", "Good Luck! Entry recorded!", "Close");
										}
										
									}else{
										window.alert("There appears to be something wrong with the data you entered. Please verify and try again");
										window.alert( data );
									}
								}
							}
						},
						error: function(){
							  window.alert("An error has occurred. Your entry has not been recorded. Please play again later or contact the CMS Administrator");
							  //alert('error handing here');
						}
					});
					return false;
				});
			});
		}
		
		if( redirect == 1 && showForm == 0 ){
			//check if re-directs?
			logger("Redirecting");
			window.location = redirectURL;
		}
		
	}else{
		logAction("Finished");
		$('#currentStep, #badge, #jp_container_1').hide();
		$('body').html("<marquee><h1>Thanks for Playing!</h1></marquee><center><blink><a class='playAgain' href='" + window.location + "'>Play again?</a></blink></center>");
		showAlert("You have what it takes! You've already entered the contest so we wont show you the form again.", "Thanks for playing!", "Close");
	}
}

function playSegment( segmentID ) {

	shownD = 0;
	$('#clickActions').remove();

	logger("In PlaySegment: " + segmentID );
	var segmentData = "";
	var decisions = new Array();
	if( segmentID != 0 && segmentID != "" ){
		currentSegmentID  = segmentID;
		segmentData = getAjaxHandlerResponse("getSegment", segmentID );
		if( segmentData ){
			currentSegmentData =  segmentData;
			
			console.log( currentSegmentData );
			
			var clipID = segmentData.StartingClipID;
		}
		//same function in index
		if( clipID != "" && clipID != 0 ){
			//logger( segmentData );
			if(  segmentData.Decisions ){
				if( segmentData.Decisions.length > 0 ){
					for( var d = 0; d < segmentData.Decisions.length; d++ ){
						logger( segmentData.Decisions[d] );
					
						decisions[d] = { "id" : segmentData.Decisions[d].DecisionID, "text" : segmentData.Decisions[d].ButtonText, "continues" :  segmentData.Decisions[d].Continues  , "ends" : segmentData.Decisions[d].Ends , "goToSegment" : segmentData.Decisions[d].NextSegmentID  , "PlayClip" : segmentData.Decisions[d].PlaysClip, "ForcedBadgeID" : segmentData.Decisions[d].forcedBadge } 
					}
				}
				
				currentDecisions = decisions;
			}
		}	
		if( decisions != ""   ){
			//logger( "set decisions continuing ");
			nextStep =  function(){ overlayDecisions( decisions ); }
			if( clipID != -1 ){
				//come here if the decisiontree doesn't have a clip and you just want to show options
				playClip(clipID );
				
				
			}else{
				nextStep();
			}
		}else{
			nextStep = function(){ window.alert("No decisions setup beyond this point... error... or finished"); }
			finished();
		}
	}
}

function getJPlayerHeight(){
	logger("Height: " + (type == 1 ? windowHeight :  "auto" ) );
	return (type == 1 ? windowHeight :  "auto" );
}

function getJPlayerWidth(){
	//logger( "width: " + windowWidth ); //check if portait or landscape
	if( windowWidth == undefined  ){
		windowWidth = $(window).width();
	}
	logger( "width: " + windowWidth ); //check if portait or landscape
	return windowWidth;
}

function triggerPlay(){
	if( type != 1 ){
		setTimeout(function() {$("#jquery_jplayer_1").jPlayer("play"); }, 500);
	}
}


function playClip(clipID ){
	clipPath = getClipPath( clipID, type );
	clipPath = serverHost + clipPath;
	
	logger("Destroy & remove jPLayer");
	
	$("#jquery_jplayer_1").jPlayer("destroy");

	
	$('#jquery_jplayer_1').height( windowHeight );
	$('#jquery_jplayer_1').width( windowWidth );
	$('div.jp-video-play').css('height',  windowHeight + 'px' );
	logger("Bind PLayer");
	
	$("#jquery_jplayer_1").jPlayer( "stop" );
	$("#jquery_jplayer_1").jPlayer( "clearFile" );
	
	$("#jquery_jplayer_1").jPlayer({
		ready: function () {
			$(this).jPlayer("setMedia", {
				m4v: clipPath
			}); 
			currentClipID = clipID;
			if( started == 0 ){
				logAction("Start");
				started = 1;
			}
		  
			if( type == 1 ){
				$(this).jPlayer("play");
			}else{
				jQuery('video').width(windowWidth);
				jQuery('video').height(windowHeight);
			}
			$('#clickActions').remove();
			shownD = 0;
		},
		ended: function(){
			updateBadge();
			if( forceABadge != 0 ){
				forceDisplayBadge( forceABadge );
			}
			
			if( typeof decisions === 'undefined' ){
				logger( "home.js, ended function, nextStep() current state: " + state + " set to tryagain");
				state = "tryagain";
				
				nextStep();	
			}else{
				state = "tryagain";
				logger( "home.js, ended function, overlayDecisions() current state: " + state + " set to tryagain");
				overlayDecisions( decisions );
			}
		},
		size: { width: getJPlayerWidth(), height: getJPlayerHeight() },
		preload : (type == 1 ? "auto" : "none"),
		volume: 1,
		swfPath: serverHost + "/js",
		supplied: "m4v",
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
		timeupdate: function(event) {
			
			var d =  Math.floor($("#jquery_jplayer_1").data("jPlayer").status.duration);
			var ct = parseInt(Math.floor(event.jPlayer.status.currentTime));
			var showAt = parseInt( d - 8 );
			
			if( ct > 0 && ct >= showAt  && event.jPlayer.status.paused===false ){
				//logger( "home.js - In timeupdate, state: " + state );
				if( state == "tryagain" || state == ""){
					$.jPlayer.pause();
					if( typeof decisions === "undefined" ){
						logger( "home.js pause clip, next step");
						nextStep();	
					}else{
						logger( "home.js pause clip, show decisions");
						overlayDecisions( decisions );
					}
				}
			}
		}
	});
	
}			

function updateBadge(){
	if( showBadge ){
		if( currentSegmentData && currentSegmentData.BadgePath != "" ){
			//console.log( currentSegmentData );
			$(function(){
				if( badgeMode == 1 ){//append
					$('#badge').html( $('#badge').html() + '<img id="badge_' + currentSegmentData.BadgeID + '" class="badge" src="' + serverHost + currentSegmentData.BadgePath + '" />');
				}else{
					$('#badge').html( '<img id="badge_' + currentSegmentData.BadgeID + '" class="badge" src="' + serverHost + currentSegmentData.BadgePath + '" />');
				}
				logger("Updated Badge to: " + serverHost + currentSegmentData.BadgePath);
				$('#badge_' + currentSegmentData.BadgeID).hide();
				$('#badge_' + currentSegmentData.BadgeID).fadeIn(1000); 
			});
		}
	}
}


function forceDisplayBadge( forceBadge ){
	if( typeof forceBadge !== "undefined" && forceBadge != 0 ){
		logger("Show that forced Badge " );
		var badgePath = getAjaxHandlerResponse("getBadge", forceBadge );
		if( badgePath && badgePath.path != "" ){
			if( badgeMode == 1 ){//append
				$('#badge').html( $('#badge').html() + '<img id="badge_' + forceBadge + '" class="badge" src="' + serverHost + badgePath.path + '" />');
			}else{
				$('#badge').html( '<img id="badge_' + forceBadge + '" class="badge" src="' + serverHost + badgePath.path + '" />');
			}
			logger("Updated Badge to: " + serverHost + badgePath.path);
			$('#badge_' + forceBadge).hide();
			$('#badge_' + forceBadge).fadeIn(1000); 
		}
	}
}


function doAction(goTosegmentID, playClipID, continues, ends, forceBadge ){
	nthAction ++;
	logger("In doAction, nthAtction: " + nthAction);
	nextSegment = goTosegmentID;
	
	var clipPath = "";
	var hasClip = false;
	if( playClipID != 0 && playClipID != "" ){
		hasClip = true;
	}
	
	shownD = 0;
	$('#clickActions').remove();
	
	if( continues == 1  ){
		state = "continue";
		logger("Set state: continue" );
		//update Step
		if( showCount ){
			currentStep = parseInt(currentStep) + 1
			$('#currentStep').html(  currentStep );
		}
	
		if( hasClip ) {
			currentClipID = playClipID;
			logAction("Continue");
			
			if( typeof forceBadge !== "undefined" && forceBadge != 0 ){
				forceABadge = forceBadge
			}else{
				forceABadge = 0;
			}
			
			
			nextStep = function(){ playSegment( goTosegmentID );  }
			
			playClip( playClipID  );
			
			
			
		}else{
			logAction("Continue");
			playSegment( goTosegmentID );
		}
		
	}else if( ends == 1  ){
		state = "ends";
		logger("Set state: ends" );

		if( hasClip ) {
			currentClipID = playClipID;
			logAction("Finished");
			nextStep = function(){ finished();  } //finished
			playClip( playClipID  );
			
		}else{
			logAction("Finished");
			playSegment( goTosegmentID );
			
		}

	}else{
		state = "tryagain";
		logger("Set state: tryagain" );
		if( goTosegmentID != currentSegmentID ){
			//this could be an error... or a branching wrong
			nextStep = function(){  playSegment( goTosegmentID );  }	
			if( hasClip ) {
				playClip( playClipID  );
			}else{
				nextStep();
			}
		}else{	
			nextStep = function(){ overlayDecisions( currentDecisions ); }
			if( hasClip ) {
				currentClipID = playClipID;
				logAction("Try again");
				playClip( playClipID );
			}else{
				logAction("Try again");
				nextStep();
			}
		}
	}
	
	if( type == 3){ //the ipad doesn't allow this?
		triggerPlay();
	}
}

//responsive aid
function setSize(){
	windowWidth =  $(window).width() -5;
	windowHeight =  $(window).height() -5 ;
}
	
var waitForFinalEvent = (function () {
  var timers = {};
  return function (callback, ms, uniqueId) {
	if (!uniqueId) {
	  uniqueId = "resize";
	}
	if (timers[uniqueId]) {
	  clearTimeout (timers[uniqueId]);
	}
	timers[uniqueId] = setTimeout(callback, ms);
  };
})();			
	
