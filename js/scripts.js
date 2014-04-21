/*
 * Video Tour Scripts
 * By Brendon Irwin
 */
$(function(){
	 $(".tablesorter").tablesorter(); 
});

function previewClip(clipID, clipType){
	var type;
	if( typeof clipType == "undefined"){
		type = 1;
	}else{
		type = clipType;
	}

	$(function(){
		$.colorbox({href: serverHost + '/administration/clip/preview.php?clip_id=' + clipID + '&type=' + type,  iframe:true, width:"80%", height:"80%"});
	});
}

/*Returns JSON Object(s)*/
function getAjaxHandlerResponse(projectID, object, fx){
	//projectID, what, function
	var ret = null;
	$.ajaxSetup({async: false});
	$.get( serverHost +  "/includes/ajaxHandler.php", { projectID : projectID, object : object, fx:fx }, function( data ) {
		ret =  jQuery.parseJSON( data );
	});
	$.ajaxSetup({async: true});
	return ret;
}

//UPDATEWRITE  projectID, objecttype, object id, data??
function updateItem(objectType, id, fx, newValue){
	var ret = null;
	$.ajaxSetup({async: false});
	
	$.post( serverHost + "/includes/ajaxHandler.php", { objectType : objectType, id : id, fx:fx,newValue:newValue  }, function( data ) {
		ret =  jQuery.parseJSON( data );
	});
	
	$.ajaxSetup({async: true});
	return ret;
}

function closeDialog(){
	$( "#dialog" ).dialog('close');
}

/****************
**
** Decision Tree/Delete
**
****************/

function doDeleteDecisionTree( projectID, decisionTreeID ){
	//window.alert("PID: " + projectID +", " + "DID: " + DecisionID );
	var deleted = updateItem("decisionTree", projectID, "delete", decisionTreeID);
	if( deleted && typeof deleted.data[0] !== "undefined" ){
		
	
		if( deleted.data[0].DeletedTree == 1 ){
			window.alert("Decision Group Deleted! successfully. Subsequently, " + deleted.data[0].DeletedDecisions + " choices were also deleted." );
		}
		
		//if( deleted.data[0].DeletedDecisions > 0 ){
		//	window.alert("Deleted " + deleted.data[0].DeletedDecisions + " decisions");
		//}
	}	
	
	//console.log( deleted);
	
}

//confirmation of choice deletion
function confirmDeleteDecisionTree( projectID, decisionTreeID, button ){
	//window.alert("PID: " + projectID +", " + "DID: " + DecisionID );
	if( !$('#dialog').length ){
		$('body').append('<div id="dialog" title="Confirm Deletion of Decision Group"></div>');
	}
	var html = "";
	html+= "<h2>Are you sure you want to delete this Group?</h2>";
	html+= "<p>Deleting this group will also delete all of it's decisions. Ensure that this Group doesn't continue or end the project, or you'll leave the tour hanging!</p>";

	$('#dialog').html( html );
	$( "#dialog" ).dialog({ title: "Confirm Deletion of Decision Group", width: "600", buttons: {
        "Delete Group": function() {
			$( this ).dialog( "close" );
			$(button).closest('tr').remove();
			doDeleteDecisionTree( projectID, decisionTreeID );
        },
        Cancel: function() {
			$( this ).dialog( "close" );
        }
      } });
	
}




/****************
**
** Decision/Delete
**
****************/

function doDeleteDecision( projectID, DecisionID ){
	//window.alert("PID: " + projectID +", " + "DID: " + DecisionID );
	var deleted = updateItem("decision", projectID, "delete", DecisionID);
	if( deleted && typeof deleted.data[0] !== "undefined" ){
		if( deleted.data[0].deleted == 1 ){
			window.alert("Choice Deleted!");
		}
	}	
	
}

//confirmation of choice deletion
function confirmDeleteDecision( projectID, DecisionID, button ){
	//window.alert("PID: " + projectID +", " + "DID: " + DecisionID );
	if( !$('#dialog').length ){
		$('body').append('<div id="dialog" title="Confirm Deletion of Choice"></div>');
	}
	var html = "";
	html+= "<h2>Are you sure you want to delete this Choice?</h2>";
	html+= "<p>Ensure that this choice doesn't continue or end the project, or you'll leave the tour hanging!</p>";

	$('#dialog').html( html );
	$( "#dialog" ).dialog({ title: "Confirm Deletion of Choice", width: "600", buttons: {
        "Delete Choice": function() {
			$( this ).dialog( "close" );
			$(button).closest('tr').remove();
			doDeleteDecision( projectID, DecisionID );
        },
        Cancel: function() {
			$( this ).dialog( "close" );
        }
      } });
	
}


/****************
**
** Segments/ Delete
**
****************/

function doDeleteSegment( projectID, segmentID ){
	//window.alert("PID: " + projectID +", " + "SID: " + segmentID );
	var deleted = updateItem("segment", projectID, "delete", segmentID);

	if( deleted && typeof deleted.data[0] !== "undefined" ){
		if( deleted.data[0].deleted == 1 ){
			window.alert("Segment Deleted!");
		}
	}	
}

//Confirmation of Delete Segment
function confirmDeleteSegment( projectID, segmentID, button ){
	if( !$('#dialog').length ){
		$('body').append('<div id="dialog" title="Confirm Deletion of Segment"></div>');
	}
	//ok I'm cheating here technically I should use my get method... but then I'd have to add some extra params
	var use = updateItem("segment", projectID, "SegmentUsageCount", segmentID); 
	var html = "";
	html+= "<h2>Are you sure you want to delete this segment?</h2>";
	if( use.data[0] ){
		var uses =  use.data[0].uses
		if( uses != -1 && uses > 0 ){
			//console.log( uses );
			html+= "<p>This segment is used: <b>" + uses + "</b> times(s), so deleting it may have ramifications on your project. Be sure to address these if you proceed!</p>";

		}else{
			if( uses == -1 ){
				//error
				html+= "<p>An error occurred while trying to determine if this segment is being used, deleting it <b>may</b> ramifications on your project.</p>";
			}else{
				//not used
				html+= "<p>This segment is <b>not</b> being used, deleting it should not have any ramifications on your project</p>";
			}
		}
	}
	$('#dialog').html( html );
	$( "#dialog" ).dialog({ title: "Confirm Deletion of Segment", width: "600", buttons: {
        "Delete Segment": function() {
			$( this ).dialog( "close" );
			$(button).parents('tr').remove();
			doDeleteSegment( projectID, segmentID );
        },
        Cancel: function() {
			$( this ).dialog( "close" );
        }
      } });
}

/****************
**
** Segments/ Update Starting Segment
**
****************/

function setStartSegment( projectID, segmentID ){
	//close dialog box
	closeDialog();
	//set the project Starting Clip - have to see if it exists or not first
	
	var resp = updateItem( "project", projectID, "startingSegment", segmentID );
	
	console.log( resp );
	
	if( resp != null ){
		var d = resp.data;
		if( d.length == 1 && d[0].update == 1){
			window.alert("Updated Starting Segment to: " + segmentID);
		}else{
			window.alert("Could not update Starting Segment");
		}
	}else{
		window.alert("Could not update Starting Segment");
	}
}


/*Update Project Starting Segment*/
function updateStartingSegment(projectID){
	if( !$('#dialog').length ){
		$('body').append('<div id="dialog" title="Set Starting Segment"></div>');
	}
	
	var data = getAjaxHandlerResponse(projectID, "segment", "list");
	
	var html = "";

	var items = data.items;
		
	html += "<p>Items: " + items + "</p>";
	html+= '<table class="ajaxTable"><tr><td></td><td>Note</td><td>Decision Group ID:</td><td>Preview</td></tr>';
	
	if( items == 1 ){
		var segment = data.data;
		html+= '<tr><td><a onClick="setStartSegment(' + projectID + ',' + segment.Segmentsid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a></td><td><p>' +segment.Segmentsnote + '</p></td><td>' + segment.SegmentsdecisionTreeID + '</td><td><a onClick="previewClip(' + segment.SegmentsclipID + ')" class="button"><i class="fa fa-play"></i>Preview</a></td></tr>';
	}else{
		for( var c = 0; c < items; c++){
			var segment = data.data[c];
			html+= '<tr><td><a onClick="setStartSegment(' + projectID + ',' + segment.Segmentsid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a></td><td><p>' +segment.Segmentsnote + '</p></td><td>' + segment.SegmentsdecisionTreeID + '</td><td><a onClick="previewClip(' + segment.SegmentsclipID + ')" class="button"><i class="fa fa-play"></i>Preview</a></td></tr>';
		}
	}
	html+= '</table>';
	
	
	$('#dialog').html( html );
	$( "#dialog" ).dialog({ title: "Set Starting Segment", width: "80%" });

}

/****************
**
** Links / Delete
**
****************/


/*Edit Project Functions*/

/*Segments */

function addSegment(projectID){
	window.location = serverHost + "/administration/segments/add?projectID=" + projectID;
}

function editSegment(projectID, segmentID ){
	//window.alert(" Edit Segment: " + segmentID + " from Project " + projectID );
	window.location = serverHost + "/administration/segments/edit?projectID=" + projectID + "&segmentID=" + segmentID;
}

/*Decision Tree*/
function addDecisionGroup(projectID){
	window.location = serverHost + "/administration/decisionTree/add?projectID=" + projectID;
}

function editDecisionTree(projectID, DecisionTreeID ){
	//window.alert(" Edit Decision Tree: " + DecisionTreeID + " from Project " + projectID );
	window.location = serverHost + "/administration/decisionTree/edit?projectID=" + projectID + "&decisionTreeID=" +DecisionTreeID;
}

/*Decisions */
function editDecision(projectID, DecisionID){
	window.location = serverHost + "/administration/decision/edit?projectID=" + projectID + "&decisionID=" + DecisionID;
}

function addDecision(projectID, DecisionTreeID){
	window.location = serverHost + "/administration/decision/add?projectID=" + projectID + "&decisionTreeID=" + DecisionTreeID;
}

function uploadClip(projectID){
	window.location = serverHost + "/administration/clip/upload?projectID=" + projectID;
}

function addBadge(projectID){
	window.location = serverHost + "/administration/badge/add?projectID=" + projectID;
}

function reload(projectID){
	window.location = window.location + "?projectID=" + projectID;
}

/****************
**
** Clips
**
****************/

function confirmDeleteClip(clipID, button){
	if( !$('#dialog').length ){
		$('body').append('<div id="dialog" title="Are you sure you want to Delete the Clip?"></div>');
	}

	$( "#dialog" ).hide();
	var data = getAjaxHandlerResponse(clipID, "clip", "isUsed");
	var html = "";
	
	html+= "<h2>Are you sure you want to delete this Clip?</h2>";
	
	if( data ){
		if( data.data ){
			var info = data.data.count;
			var total = info.total;
			var segmentUse = info.segments;
			var decisionUse = info.decisions;
			//console.log( total +" , " + segmentUse +" , " + decisionUse );
			html += "This clip is currently being used in a total of <b>" + total + "</b> places (<b>" + segmentUse + "</b>) segments and (<b>" + decisionUse + "</b>) decisions. If the segments/decisions are left using a deleted video, it will break the tour. Be sure to correct these later if you proceed.";
		}
	}
	
	$('#dialog').html( html );
	$( "#dialog" ).dialog({ title: "Are you sure you want to Delete the Clip?", width: "600", buttons: {
        "Delete Clip": function() {
			$( this ).dialog( "close" );
			$(button).parents('tr').remove();
			deleteClip( clipID );
        },
        Cancel: function() {
			$( this ).dialog( "close" );
        }
      } });
}


function deleteClip(clipID){
	var deleted = updateItem("clip", clipID, "delete", "");
	if( deleted && typeof deleted.data[0] !== "undefined" ){
		if( deleted.data[0].deleted == 1 ){
			window.alert("Clip Deleted! (" + deleted.data[0].clipsDeleted + ") conversions were also deleted");
		}
	}	
}

function editClip( projectID, clipID ){
	if( !$('#dialog').length ){
		$('body').append('<div id="dialog" title="Update Clip"></div>');
	}
	$( "#dialog" ).hide();

	var noteText = getAjaxHandlerResponse( clipID, "clip", "getNote");
	var noteName = getAjaxHandlerResponse( clipID, "clip", "getName");
	
	if( noteText && noteName ){
	
		noteText = noteText.data;
		noteText = noteText.Note;
		
		noteName = noteName.data;
		noteName = noteName.Name;
	}
	
	var html = "";
	html += "<h2>Edit Clip</h2>";
	html += "<table><tr>";
	html += "<td>Name: </td><td><input type='text' id='newNoteName' value='" + noteName + "' /></td></tr>";
	html += "<tr><td>Note: </td><td><textArea id='newNoteText'>" + noteText + "</textarea></td></tr>";
	html += "<table>";
	//console.log ( noteText );
	
	$('#dialog').html( html );
	
	$( "#dialog" ).dialog({ title: "Update Clip", width: "600", buttons: {
        "Save": function() {
			$( this ).dialog( "close" );
			
			var d = $('#newNoteText').val();
			var n = $('#newNoteName').val();
			
			if( updateItem("clip", clipID, "setNote", d) &&  updateItem("clip", clipID, "setName", n)){
				window.alert("Update ok!");
			}

        },
        Cancel: function() {
			$( this ).dialog( "close" );
        }
      } });

}


/*TODO*/

function updateDesc(objectType, caller){

	console.log("Update Description for: " + $(caller).attr('id') );
	console.log("Get Description for: " + objectType + ", where obj ID = " + $(caller).val() );
	
	

}
/****************
**
** Decision/Add Page
**
****************/

function setSegment( segmentID ){
	closeDialog();
	if( $('#segmentID').length ){
	$('#segmentID').val( segmentID );
	//$('#segmentID').change(); //trigger the onchange
	}
}

//essentially same as: updateStartingSegment
function selectSegment(projectID){
	$(function(){
		if( !$('#dialog').length ){
			$('body').append('<div id="dialog" title="Select a Segment (Video Segway)"></div>');
		}

		$( "#dialog" ).hide();
		var data = getAjaxHandlerResponse(projectID, "segment", "list");
		var html = "";
		var items = data.items;
		
		html += "<p>Items: " + items + "</p>";
		html+= '<table class="ajaxTable"><tr><td>ID</td><td> </td><td>Note</td><td>Goes to Decision Group ID:</td><td>Preview</td></tr>';
		
		if( items == 1 ){
				var segment = data.data;
				html+= '<tr><td>' + segment.Segmentsid + '</td><td><a onClick="setSegment(' + segment.Segmentsid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a> </td><td><p>' +segment.Segmentsnote + '</p></td><td>' + segment.SegmentsdecisionTreeID + '</td><td><a onClick="previewClip(' + segment.SegmentsclipID + ')" class="button"><i class="fa fa-play"></i>Preview</a></td></tr>';
		}else{
			for( var c = 0; c < items; c++){
				var segment = data.data[c];
				html+= '<tr><td>' + segment.Segmentsid + '</td><td><a onClick="setSegment(' + segment.Segmentsid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a> </td><td><p>' +segment.Segmentsnote + '</p></td><td>' + segment.SegmentsdecisionTreeID + '</td><td><a onClick="previewClip(' + segment.SegmentsclipID + ')" class="button"><i class="fa fa-play"></i>Preview</a></td></tr>';
			}
		}
		html+= '</table>';
		
		
		$('#dialog').html( html );
		
		$( "#dialog" ).dialog({ width: "80%", title: "Select a Segment (Video Segway)" });
	});
}


/****************
**
** Segments/Add Page
**
****************/


function setClip(id){
	closeDialog();
	if( $('#clipID').length ){
		$('#clipID').val( id );
		//$('#clipID').change(); //trigger the onchange
	}
}

function selectClip(projectID){
	$(function(){
		if( !$('#dialog').length ){
			$('body').append('<div id="dialog" title="Select a Clip"></div>');
		}
		
		$( "#dialog" ).hide();
		var data = getAjaxHandlerResponse(projectID, "clip", "list");
		var html = "";
		var items = data.items;
		
		html += "<p>Items: " + items + "</p>";
		html+= '<table  class="ajaxTable"><tr><td>id</td><td></td><td>Note</td><td>Name</td><td>Preview</td></tr>';
		if( items == 1 ){
			//var obj = data.data;
			var clip = data.data;
			html+= '<tr><td>'+ clip.Clipid +'</td><td><a onClick="setClip(' + clip.Clipid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a></td><td>' +clip.Clipnote + '</td><td>' + clip.Clipname + '</td><td><a onClick="previewClip(' + clip.Clipid + ')" class="button"><i class="fa fa-play"></i>Preview</a></td></tr>';
		}else{
			for( var c = 0; c < items; c++){
				var clip = data.data[c];
				html+= '<tr><td>'+ clip.Clipid +'</td><td><a  onClick="setClip(' + clip.Clipid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a></td><td>' +clip.Clipnote + '</td><td>' + clip.Clipname + '</td><td><a onClick="previewClip(' + clip.Clipid + ')" class="button"><i class="fa fa-play"></i>Preview</a></td></tr>';
			}
		}
		html+= '</table>';
		
		html += '<hr /><a class="button wa" onClick="closeDialog()">Cancel</a>';
		
		$('#dialog').html( html );
		$( "#dialog" ).dialog({ width: "80%", title: "Select a Clip" });
	});
}

function setTree(id){
	closeDialog();
	if( $('#decisionTreeID').length ){
		$('#decisionTreeID').val( id );
		$('#decisionTreeID').change(); //trigger the onchange
	}
}


function selectDecisionTree(projectID){
	//window.alert("Choose Decision Tree in: " + projectID);
	//get list of decision trees for the project
	if( !$('#dialog').length ){
		$('body').append('<div id="dialog" title="Select a Decision Group"></div>');
	}
	
	$( "#dialog" ).hide();
		
	var data = getAjaxHandlerResponse(projectID, "decisionTree", "list");
	var html = "";
	var items = data.items;	
	
	html += "<p>Items: " + items + "</p>";
	html+= '<table class="ajaxTable"><tr><td></td><td>ID</td><td>Title</td><td>Step</td><td>Note</td></tr>';
	
	console.log( data );
	
	if( items == 1 ){
		var dt = data.data;
		
		//console.log( dt );
		
		html+= '<tr><td><a onClick="setTree(' + dt.DecisionTreeid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a></td><td>' + dt.DecisionTreeid + '</td><td>' +   dt.DecisionTreetitle + '</td><td>' + dt.DecisionTreestep + '</td><td>' + dt.DecisionTreenote + '</td></tr>';
	
	}else{
		for( var c = 0; c < items; c++){
			var dt = data.data[c];
			
			
			html+= '<tr><td><a onClick="setTree(' + dt.DecisionTreeid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a></td><td>' + dt.DecisionTreeid + '</td><td>' +   dt.DecisionTreetitle + '</td><td>' + dt.DecisionTreestep + '</td><td>' + dt.DecisionTreenote + '</td></tr>';
			
		}
	}
	html+= '</table>';
	html += '<hr /><a class="button wa" onClick="closeDialog()">Cancel</a>';
	$('#dialog').html( html );
	$( "#dialog" ).dialog({ width: "80%", title: "Select a Decision Group" });

}

function setBadge( badgeID ){
	closeDialog();
	$('#badgeID').val( badgeID );
}

function selectBadge(projectID){
	if( !$('#dialog').length ){
		$('body').append('<div id="dialog" title="Select a Completion Badge"></div>');
	}
	$( "#dialog" ).hide();

	
	var data = getAjaxHandlerResponse(projectID, "badge", "list");
	var html = "";
	var items = data.data.length;	
	
	html += "<p>Items: " + items + "</p>";
	html+= '<table class="ajaxTable"><tr><td></td><td>ID</td><td>note</td><td>preview</td></tr>';
	console.log( data );
	
	
	if( items == 1 ){
		var badge = data.data[0];
		html+= '<tr><td><a onClick="setBadge(' + badge.Badgeid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a></td><td>' + badge.Badgeid + '</td><td>' +   badge.Badgenote + '</td><td><a onClick="previewBadge(' + badge.Badgeid + ')"><i class="fa fa-shield"></i> Preview</a></td></tr>';
	}else{
		for( var c = 0; c < items; c++){
			var badge = data.data[c];
			html+= '<tr><td><a onClick="setBadge(' + badge.Badgeid + ')" class="button"><i class="fa fa-hand-o-right"></i> Select</a></td><td>' + badge.Badgeid + '</td><td>' +   badge.Badgenote + '</td><td><a onClick="previewBadge(' + badge.Badgeid + ')"><i class="fa fa-shield"></i> Preview</a></td></tr>';
		}
	}
	html+= '</table>';
	html += '<hr /><a class="button wa" onClick="closeDialog()">Cancel</a>';
	$('#dialog').html( html );
	$( "#dialog" ).dialog({ width: "80%", title: "Select a Completion Badge" });
}


/****************
**
** Badges / Add Page
**
****************/

function confirmDeleteBadge(projectID, badgeID, button ){
	var r=confirm("Are you sure you wish to delete the badge?");
	if( r == true ){
		var del  = getAjaxHandlerResponse(badgeID, "badge", "delete");
		if( del.data.Deleted == 1 ){
			window.alert("Badge Deleted");
			$(button).closest('tr').remove();
		}
	}
}

function previewBadge(badgeId){
	var badgePath = getAjaxHandlerResponse(badgeId, "badge", "getPath");
	if( badgePath.data ){
		badgePath = badgePath.data;
	
		$(function(){
			$.colorbox({href: serverHost + badgePath,  iframe:true, width:"80%", height:"80%"});
		});
	}
}

/****************
**
** Contest Entries / Project Page
**
****************/



function clearContestEntries(projectID ){
	var r=confirm("Are you positive you wish to delete the contest entries data for this project?");
	if (r==true){
		var doublecheck = prompt("Please type: CONFIRM to continue with deletion. This process CANNOT be undone!", "" );
		if( doublecheck != null && doublecheck == "CONFIRM" ){
			var del  = getAjaxHandlerResponse(projectID, "project", "clearContestEntries");
			if( del ){
				if( del.data ){
					var d = del.data;
					if( d.Deleted == 1 ){
						window.alert("Deleted: Contest Entries successfully");
					}else{
						window.alert("Deletion Failed!");
					}
				}
			}
		}else{
			window.alert("Deletion of Entries Cancelled!");
		}
	}
}

/****************
**
** Analytics / Project Page
**
****************/

function clearAnalytics(projectID ){
	var r=confirm("Are you positive you wish to delete the analytic data for this project?");
	if (r==true){
		var doublecheck = prompt("Please type: CONFIRM to continue with deletion. This process CANNOT be undone!", "" );
		if( doublecheck != null && doublecheck == "CONFIRM" ){
			var del  = getAjaxHandlerResponse(projectID, "project", "clearAnalytics");
			if( del ){
				if( del.data ){
					var d = del.data;
					if( d.Deleted == 1 ){
						window.alert("Deleted: Analytics successfully");
						window.location = window.location;
					}else{
						window.alert("Deletion Failed!");
					}
				}
			}
		}else{
			window.alert("Deletion of Analytics Cancelled!");
		}
	}
}



/****************
**
** Edit / Project Page
**
****************/



/*Turn the Project On or Off*/
function activateProject(projectID, flag, btn){
	var resp = updateItem("project", projectID, "active", flag);
	if( resp != null ){
		var d = resp.data;
		if( d.length == 1 && d[0].update == 1){
			var html = $(btn).html();
			if( html.indexOf("Deactivate") >= 0 ){
				$(btn).replaceWith('<a onClick="activateProject(' + projectID +',1, this)" class="button wa"><i class="fa fa-thumbs-up"></i> Activate Project</a>');
				window.alert("Project De-activated");
			}else{
				$(btn).replaceWith('<a onClick="activateProject(' + projectID + ',0, this)" class="button wa"><i class="fa fa-thumbs-down"></i> Deactivate Project</a>');
				window.alert("Project Activated");
			}
		}
	}
}


function deleteProject(projectID ){
	var r=confirm("Are you positive you wish to delete this project?");
	if (r==true){
		var doublecheck = prompt("Please type: CONFIRM to continue with deletion. This process CANNOT be undone!", "" );
		if( doublecheck != null && doublecheck == "CONFIRM" ){
			//window.alert("Doing Delete!");
			var del  = getAjaxHandlerResponse(projectID, "project", "delete");
			if( del ){
				if( del.data ){
					var d = del.data;
					if( d.deletionSucceeded == 1 ){
						window.alert("Deleted: " + d.NumBadges + " badges, " + d.NumClips + " clips " );
						window.location = serverHost + "/admin";
					}else{
						window.alert("Detetion Failed!");
					}
				
				}
			}
		}else{
			window.alert("Deletion Cancelled!");
		}
	}
}
