<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	pageHeader();
?>
<script>
	$(function(){
		$('.showHideVersions').click(function(event){
			event.stopPropagation();
			//$(this).toggleClass('hide');
			$(this).removeClass('hide');
		});
		
		$('.highlightSegment').hover(function() {
			var id= $(this).attr('data-attr-segmentid');
			$('[data-attr-segmentid="' + id + '"]').addClass('highlightMe');
		  }, function() {
			var id= $(this).attr('data-attr-segmentid');
			$('[data-attr-segmentid="' + id + '"]').removeClass('highlightMe');
		  });

		$('.highlightDecisionTree').hover(function() {
			var id= $(this).attr('data-attr-decisionid');
			$('[data-attr-decisionid="' + id + '"]').addClass('highlightMe');
		  }, function() {
			var id= $(this).attr('data-attr-decisionid');
			$('[data-attr-decisionid="' + id + '"]').removeClass('highlightMe');
		  });
		
		$('.highlightBadge').hover(function() {
			var id= $(this).attr('data-attr-badgeid');
			$('[data-attr-badgeid="' + id + '"]').addClass('highlightMe');
		  }, function() {
			var id= $(this).attr('data-attr-badgeid');
			$('[data-attr-badgeid="' + id + '"]').removeClass('highlightMe');
		  });

		function scrollToItem( item ){
			var scrollpos = $(item).offset().top; 
     		$('html,body').animate({scrollTop: scrollpos },'slow');
			$(item).each(function () { this.scale = 0.5; }).animate({ 
				scale: 1,
			}, {
				duration: 1000,
				step: function (now) {
					$(this).css('font-size',  ( 1 + (now * 1) ) + 'em' );
				}
			}).each(function () { this.scale = 1; }).animate({ 
				scale: 0,
			}, {
				duration: 1000,
				step: function (now) {
					$(this).css('font-size',  ( 1 + (now * 1) ) + 'em' );
				}
			});
		}		
		  
		function jumpToNext(what,id, thisItem){
			what = what.toLowerCase();
			var item = $('[data-attr-' + what + 'id="' + id + '"]'); //should be an array of items
			
			if( item.length != 0 ){
				for(var i =0; i < item.length; i++ ){
					var itemO = $(thisItem)[0];
					if( itemO == item[i]  ){
						i++;
						if( i == item.length ) {
							scrollToItem( item[0] );
						}else{
							
							scrollToItem( item[i] );
						}
					}else{
						console.log( " no "  );
					}
				}
			}
		}

		$('.highlightBadge').click(function(event){  event.preventDefault(); var id= $(this).attr('data-attr-badgeid'); jumpToNext("Badge", id, $(this) ); });
		$('.highlightSegment').click(function(event){  event.preventDefault(); var id= $(this).attr('data-attr-segmentid'); jumpToNext("Segment", id, $(this) ); });
		$('.highlightDecisionTree').click(function(event){  event.preventDefault(); var id= $(this).attr('data-attr-decisionid'); jumpToNext("Decision", id, $(this) ); });
	});
</script>

<style>
	.highlightMe{
		color: #f00;
		background-color: #ff0;
		font-weight: bold;
		cursor: pointer;
	}
</style>


<?php
	//check if user has access to the project?
	
	
	
	


	if( isset($_REQUEST['id']) && $_REQUEST['id'] != ""){
		$id = $_REQUEST['id'];
		
		$conn = getConnection();
		$project = new Projects($conn);
		$segments = new Segments($conn);
		

		$project = $project->load($id);
		
		if( is_object($project ) ){
		
		checkAccess( $project->getId() );

			
		
		$segments = $segments->getList( $project->getId() );
		
?>
	<h1>Edit Project: <?php echo $project->getTitle(); ?></h1>
	<!-- Associated Segments -->
	<p><a href="<?php echo fixedPath; ?>/administration/project/settings?projectID=<?php echo $id; ?>" class="button wa"><i class="fa fa-cog"></i> Edit Settings</a> <a class="button wa" target="_blank" href="<?php echo fixedPath; ?>/administration/project/css?projectID=<?php echo $id;?>">Edit CSS</a> <a class="button wa" target="_blank" href="<?php echo fixedPath; ?>/administration/project/js?projectID=<?php echo $id;?>">Edit JS</a></p>
	
	
	<!--Starting Segment -->
	<?php if( $project->getStartingSegmentID() == null || $project->getStartingSegmentID() == 0){
	?>
		<p>This Project does not have a starting segment. <?php if( sizeof($segments) > 0 ) { ?>You should assign one.<br /><a onClick="updateStartingSegment(<?php echo $project->getId();  ?>)" class="button wa"><i class="fa fa-plus-circle"></i> Pick Segment</a><?php }else{ echo "<br />No segments are available. Please Create one. ";
?><a class="button wa" onClick="addSegment(<?php echo $project->getId(); ?>)"><i class="fa fa-plus-circle"></i> Add Segment</a><?php } ?>
		</p>
	<?php
	}else{
	?>
	<p>Starting Segment Id: <span class="highlightSegment" data-attr-segmentid="<?php echo $project->getStartingSegmentID(); ?>"><?php echo $project->getStartingSegmentID(); ?></span>
	<?php
		$segmentsInfo = new Segments($conn);
		if( $project->getStartingSegmentID() != 0 ){
			$segmentsInfo = $segmentsInfo->load( $project->getStartingSegmentID() );
			if( is_object($segmentsInfo ) ){
				echo "(\"".$segmentsInfo->getNote()."\")";
			}else{
				echo "Could not find / load Segment: ".$project->getStartingSegmentID();
			}
		}
	?>
	
	<br /><br /><a onClick="updateStartingSegment(<?php echo $project->getId();  ?>)" class="button wa"><i class="fa fa-pencil-square-o"></i> Change Staring Segment</a></p>
	
	<?php
	}
	
	$clips = new Clip($conn);
	$clips = $clips->getList( $id);
	
	?>
	
	<h4>Step 1. Upload Clips - <?php echo sizeof($clips); ?> available</h4>
	<?php
		
		
		if( sizeof( $clips ) > 0 ){
			?>
			<div class="showHideVersions hide">
			<table class="tablesorter">
				<thead> 
					<tr> 
						<th>Clip ID</th>
						<th>Name</th>
						<th>Note</th> 
						<th>Versions</th>
						<th></th>
					</tr> 
				</thead>
				<tbody>
			<?php
				foreach( $clips as $clip ){
					echo '<tr><td>'.$clip->getId().' <a class="button" onClick="previewClip('.$clip->getId().')"><i class="fa fa-play"></i> Preview</a></td><td>'.$clip->getName().'</td><td>'.$clip->getNote().'</td><td>';
						$versions = new Clip( $conn );
						$versions = $versions->getVersions( $clip->getId() );

						?>
						<div class="showHideVersions hide">
						<table class="tablesorter">
						<thead> 
							<tr> 
								<th>Preview</th>
								<th>ID</th>
								<th>Type</th> 
								<th>Converted</th>
							</tr> 
						</thead>
						<tbody>
						<?php
							foreach( $versions as $version ){
								
								$type = "";
								if( $version->getType() == 0 ){
									$type = "Source";
								}else if( $version->getType() == 1 ){
									$type = "Original/Desktop";
								}else if( $version->getType() == 2 ){
									$type = "Mobile";
								}else if( $version->getType() == 3 ){
									$type = "Small Mobile";
								}
								
								echo '<tr><td>'.($version->getType() == 0 ? '' : ''.($version->getConverted() == 0 ? "" : '<a class="button" onClick="previewClip('.$clip->getId().','.$version->getType().')"><i class="fa fa-play"></i> Preview</a>')).'</td><td>'.$version->getId().'</td><td>'.$type.'</td><td>'.($version->getType() == 0 ? '<i class="fa fa-sort-amount-asc"></i> Source Video is used for subsequent conversions.' : ''.($version->getConverted() == 0 ? '<i class="fa fa-refresh fa-spin"></i>' : '<i class="fa fa-check"></i>')).'</td></tr>';
						
							}
						?>
						</tbody>
						</table>
						</div>
					<?php
					
					echo '</td><td><a class="button" onClick="editClip('.$project->getId().','.$clip->getId().')"><i class="fa fa-trash-o"></i> Edit</a> <a class="button" onClick="confirmDeleteClip('.$clip->getId().', this)"><i class="fa fa-trash-o"></i> Delete</a></td></tr>';
				}
			?>
				</tbody>
				</table>
				</div>
			
			<?php
		}else{
		?>
			<p>There are no clips in the project. You should probably upload some...</p>
		<?php
		}	
	?>
	<a class="button wa" onClick="uploadClip(<?php echo $project->getId(); ?>)"	id="upload"><i class="fa fa-upload"></i> Upload Clips</a>
	
	
	<h4>Step 2. Create Segways (Video which leads to Decision Group) - <?php echo sizeof($segments); ?> available</h4>
	
	<?php
		if( sizeof($segments) > 0 ){
			
			if( sizeof($segments) > 10 ){
			?>
			<div class="showHideVersions hide">
			<?php } ?>
			<table class="tablesorter">
			<thead> 
				<tr> 
					<th>SegmentID</th>
					<th>Plays Clip</th> 
					<th>Note</th>
					<th>Goes To Decision Group</th>
					<th>Awarded Badge</th>
					<th>Options</th>
				</tr> 
			</thead>
			<tbody>
			<?php
			foreach( $segments as $segment ){
			
				echo '<tr><td><span class="highlightSegment" data-attr-segmentid="'.$segment->getId().'">'.$segment->getId().'</span></td><td><a class="button" onClick="previewClip('.$segment->getClipID().')"><i class="fa fa-play"></i> Preview</a> Clip ID: '.$segment->getClipID().'</td><td>'.$segment->getNote().'</td><td><span class="highlightDecisionTree" data-attr-decisionid="'.$segment->getDecisionTreeID().'">'.$segment->getDecisionTreeID().'</span></td><td>'.($segment->getBadge() == 0 ? 'Not Assigned' : '<a onClick="previewBadge('.$segment->getBadge().')"><i class="fa fa-shield"></i> Preview</a> id: <span class="highlightBadge" data-attr-badgeid="'.$segment->getBadge().'">'.$segment->getBadge().'</span>' ).'</td><td><a onClick="editSegment('.$project->getId().','.$segment->getId().')" class="button"><i class="fa fa-pencil-square-o"></i> Edit</a> <a onClick="confirmDeleteSegment('.$project->getId().','.$segment->getId().', this)" class="button"><i class="fa fa-trash-o"></i> Delete</a></td></tr>';
			
			}
			?>
			</tbody>
			</table> 
			<?php
			if( sizeof($segments) > 10 ){
			?>
			</div>
			<?php } ?>
			<?php
		}else{
			?>
			<p>No Segments have been created in this project. You need to add some...</p>
			<?php
		}
	?>
	<p><a class="button wa" onClick="addSegment(<?php echo $project->getId(); ?>)"><i class="fa fa-plus-circle"></i> Add Segment</a></p>
	
	
	<h4>Decision Group (Question -> And Answers) </h4>
	<?php
		//for each group,  list note followed by things...
		$DecisionTree = new DecisionTree($conn);
		
		$DecisionTrees = $DecisionTree->getList( $project->getId() );
		if( sizeof( $DecisionTrees ) > 0 ){
			echo '<p>'.sizeof($DecisionTrees).' Decisions found</p>';
			?>
			<table class="tablesorter">
				<thead> 
					<tr> 
						<th>Step</th> 
						<th>ID</th>
						<th>Question & Possible Answers</th> 
						<th>Note</th> 
						<th>Options</th>
						
					</tr> 
				</thead>
				<tbody>
			<?php
		}
		
		$choice = new Decisions($conn);
		foreach( $DecisionTrees as $dt ){
			echo '<tr><td>'.$dt->getStep().'</td><td><span class="highlightDecisionTree" data-attr-decisionid="'.$dt->getId().'">'.$dt->getId().'</span></td><td><b>'.$dt->getTitle().'</b><br />';
				$choices = $choice->getList( $dt->getId() );
				if( sizeof( $choices ) > 0 ){
					?>
						
						<table class="tablesorter">
						<thead> 
							<tr>
								<th>Order</th>
								<th>Starts Clip id</th> 
								<th>Continues/Ends</th> 
								<th>Note</th> 
								<th>Go to Segment</th>
								<th>-</th>
							</tr> 
						</thead>
						<tbody> 
						<?php
							foreach( $choices as $c ){
								echo '<tr><td>'.$c->getOrder().'</td><td>'.( $c->getClipID() != 0 ? ' <a class="button" onClick="previewClip('.$c->getClipID().')"><i class="fa fa-play"></i> Preview</a> | Clip ID: '.$c->getClipID() : '').'</td><td>'.( $c->getContinues() == true ? "Continues" : (   $c->getEnds()  == true ? "Ends" : "Incorrect Answer go back" )).($c->getForcedBadgeID() != 0 ? '<br /><i class="fa fa-shield"></i> Forces Badge <span class="highlightBadge" data-attr-badgeid="'.$c->getForcedBadgeID().'">('.$c->getForcedBadgeID().')</span>' : '').'</td><td>'.$c->getNote().'</td><td><span class="highlightSegment" data-attr-segmentid="'.$c->getSegmentID().'">'.$c->getSegmentID().'</span></td><td><a onClick="editDecision('.$project->getId().','.$c->getId().')" class="button"><i class="fa fa-pencil-square-o"></i> Edit</a> <a onClick="confirmDeleteDecision('.$project->getId().','.$c->getId().', this)" class="button"><i class="fa fa-trash-o"></i> Delete</a></td></tr>';
							
							}
						?>
						</tbody>
						</table> 
					<?php
				}
				
				if( sizeof( $choices ) < 3 ){
					?>
					<p>Add a Decision - a choice/direction in the Video. Min&Max of 3!</p><br />
					<p><a class="button wa" onClick="addDecision(<?php echo $project->getId(); ?>,<?php echo $dt->getId(); ?>)"><i class="fa fa-exchange"></i> Add Choice</a></p>
					<?php
				}
				
			echo '</td><td>'.$dt->getNote().'</td><td><a onClick="editDecisionTree('.$project->getId().','.$dt->getId().', this)" class="button"><i class="fa fa-pencil-square-o"></i> Edit</a> <a onClick="confirmDeleteDecisionTree('.$project->getId().','.$dt->getId().', this)" class="button"><i class="fa fa-trash-o"></i> Delete</a></td></tr>';
		}
		if( sizeof( $DecisionTrees ) > 0 ){
			?>
			</tbody>
			</table>
			<?php
		}
	?>
	<a class="button wa" onClick="addDecisionGroup(<?php echo $project->getId(); ?>)"><i class="fa fa-plus-circle"></i> Add Decision Group</a>

	
	<h3>Badges</h3>
	<?php
		if( $project->getShowBadge() ){
	
		$badges = new Badge( $conn );
		$badges = $badges->getList( $project->getId() );
	?>
	<?php if( sizeof($badges) > 10 ){ ?>
	<div class="showHideVersions hide">
	<?php } ?>
	<table class="tablesorter">
		<thead> 
			<tr> 
				<th>Badge ID</th> 
				<th>Note</th>
				<th>Image</th> 
				<th>Options</th>
			</tr> 
		</thead>
		<tbody>
		<?php
			foreach( $badges as $b ){
				echo '<tr><td><span class="highlightBadge" data-attr-badgeid="'.$b->getId().'">'.$b->getId().'</span></td><td>'.$b->getNote().'</td><td><a href="'.fixedPath.$b->getPath().'" target="_blank"><img src="'.fixedPath.$b->getPath().'" height="50" width="50" /></a></td><td><a onClick="confirmDeleteBadge('.$project->getId().','.$b->getId().', this)" class="button"><i class="fa fa-trash-o"></i> Delete</a> <a href="'.fixedPath.'/administration/badge/edit?projectID='.$project->getId().'&badgeID='.$b->getId().'" class="button"><i class="fa fa-edit"></i> Update</a></td></tr>';
			}
		?>
		</tbody>
	</table>
	<?php if( sizeof($badges) > 10 ){ ?>
	</div>
	<?php } ?>
	<a class="button wa" onClick="addBadge(<?php echo $project->getId(); ?>)"><i class="fa fa-plus-circle"></i> Add Badge</a>
	
	<?php }else{ echo '<p>Badges are not enabled, enable them in project settings</p>'; } ?>

	<hr />
	<?php
		if( $project->isActive() ){
			echo '<p><a onClick="activateProject('.$project->getId().',0, this)" class="button wa"><i class="fa fa-thumbs-down"></i> Deactivate Project</a> - Hide this project from the world!</p>';
			?>
			<p>Project URL: <a target="_blank" href="<?php echo fixedPath; ?>/?tourID=<?php echo $project->getId(); ?>"><?php echo fixedPath; ?>/?tourID=<?php echo $project->getId(); ?></a></p>
			<?php
		}else{
			echo '<p><a onClick="activateProject('.$project->getId().',1, this)" class="button wa"><i class="fa fa-thumbs-up"></i> Activate Project</a> - Make this Project Live!</p>';
			
		}
	?>
	
	<hr />
	DELETE PROJECT
	<?php
		echo '<p><a onClick="deleteProject('.$project->getId().')" class="button wa"><i class="fa fa-trash-o"></i> Delete Project</a> - Delete this Project</p>';
	?>
	<hr />
<?php	
	}
	
	}else{
?>	
	<h3>No Project Specified...</h3>
	<p><a class="button wa" href="<?php echo fixedPath; ?>/admin"><i class="fa fa-th-list"></i> Back to Admin</a></p>
	
<?php	
	}

	
	
	if( is_object ($project ) ){
		$projID =  $project->getId(); 
		footerMenu($projID);
	}else{
		?>
		<a href="<?php echo fixedPath; ?>/admin">Back to Admin</a>
		<?php
	}
	
	
	pageFooter();
?>