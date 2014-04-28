<?php
	error_reporting(E_ALL);
	include "../includes/includes.php";
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projID = $_REQUEST['projectID'];
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projID);
		pageHeader();
		echo '<h1>"' .$project->getTitle() .'" Project Analytics</h1>';
	?>
	<style>
		p{
			font-size: 16px;
		}
	</style>
	 <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
		function viewEntry( entryID ){
			//use the ajax handler for this one
			var ret;
			$.ajaxSetup({async: false});
			$.get( "<?php echo fixedPath; ?>/includes/ajaxHandler.php", { fx : "getEntry", projectID : entryID, object : "entry" }, function( data ) {
				ret =  jQuery.parseJSON( data );
			});
			$.ajaxSetup({async: true});

			var html = "";

			if( ret ){
				if( ret.data ){
					var entry = ret.data;
					console.log( entry );
					html += "<table>";
					html += "<tr><td>First Name</td><td>" + entry.entryfirstName + "</td></tr>";
					html += "<tr><td>Last Name</td><td>" + entry.entrylastName + "</td></tr>";
					html += "<tr><td>Email</td><td>" +  entry.entryemail + "</td></tr>";
					html += "<tr><td>Telephone</td><td>" + entry.entrytelephone + "</td></tr>";
					html += "<tr><td>Twitter</td><td>" +  (entry.entrytwitter == null ? "" : entry.entrytwitter) + "</td></tr>";
					html += "<tr><td>Offer</td><td>" + entry.entryother + "</td></tr>";
					html += "<tr><td>Other Reason</td><td>" + (entry.entryother_reason == null ? "" : entry.entryother_reason) + "</td></tr>";
					html += "<tr><td>Time of Entry</td><td>" + entry.entrytimestamp + "</td></tr>";
					html += "<tr><td>Entry ID</td><td>" + entry.entryentryID + "</td></tr>";
					html += "<tr><td>Visitor ID</td><td>" + entry.entryvisitorID + "</td></tr>";
					html += "</table>";
				}else{
					html += "<p>Could not locate record</p>";
				}
			}
			
			if( !$('#dialog').length ){
				$('body').append('<div id="dialog" title="Entry"></div>');
			}
			$( "#dialog" ).hide();
			$('#dialog').html( html );
		
			$( "#dialog" ).dialog({ width: "80%", title: "Entry" });
			
		
		}
	
	
		function viewPath( visitorID ){
			//window.alert("view path for: " + visitorID );
			var ret;
			$.ajaxSetup({async: false});
			$.get( "<?php echo fixedPath; ?>/requestHandler.php", { fx : "userEvents", projectID : <?php echo $projID; ?>, userID : visitorID }, function( data ) {
				ret =  jQuery.parseJSON( data );
			});
			$.ajaxSetup({async: true});
			var html = "<table cellpadding=3 cellspacing=3><tr><td>Time</td><td>Event Type</td><td>Tour Step: </td><td>User's Step</td><td>Clip</td></tr>";
			if( ret ){
				if( ret.UserEvents ){
					if( ret.UserEvents.length > 1 ){
						for( var e = 0; e <  ret.UserEvents.length; e++){
							var event = ret.UserEvents[e];
							html += "<tr><td>" + event.event_time + "</td><td>" + event.event_type + "</td><td>" + event.on_step + "</td><td>" + event.user_action + "</td><td><a onClick='previewClip(" + event.clipID + ")' class='button'><i class='fa fa-play'></i>Preview</a></td></tr>";
						}
					}else{
						var event = ret.UserEvents[0];
						html += "<tr><td>" + event.event_time + "</td><td>" + event.event_type + "</td><td>" + event.on_step + "</td><td>" + event.user_action + "</td><td><a onClick='previewClip(" + event.clipID + ")' class='button'><i class='fa fa-play'></i>Preview</a></td></tr>";
					}
				}
			}
			html += "</table>";
			
			if( !$('#dialog').length ){
				$('body').append('<div id="dialog" title="User Events"></div>');
			}
			$( "#dialog" ).hide();
			$('#dialog').html( html );
		
			$( "#dialog" ).dialog({ width: "80%", title: "User Events" });
		}
		
	
	
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
	  
	  </script>
	
	<?php
		/*Viewers*/
		$repeatViewText = "";
		
		$totalViews = 0;
		$query = $conn->prepare("SELECT COUNT(*) as `count` FROM  `analytics_visitors` WHERE `project_id` = :projectID");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			if( $query->rowCount() ){
				$count = $query->fetch();
				$count = $count["count"];
				$totalViews= $count;
				$repeatViewText.= '<p><b>'.$count.'</b> people have viewed this project</p>';
			}
		}
		
		/*Repeat Viewers*/
		$repeatViews = 0;
		$query = $conn->prepare("SELECT COUNT(*) as `count` FROM  `analytics_visitors` WHERE `project_id` = :projectID AND `has_returned` > 0");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			if( $query->rowCount() ){
				$count = $query->fetch();
				$count = $count["count"];
				
			
				$repeatViews = $count;
			if( $totalViews > 0 ){
			
			$singleViews = $totalViews - $repeatViews;
			
			
		?>
				<!--<div id="chart_repeat_use"></div>-->
				<script>
				 // Set a callback to run when the Google Visualization API is loaded.
				  google.setOnLoadCallback(drawViewsChart);
				  // Callback that creates and populates a data table,
				  // instantiates the pie chart, passes in the data and
				  // draws it.
				  function drawViewsChart() {
					// Create the data table.
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Views');
					data.addColumn('number', 'Views');
					data.addRows([
					  ['Single Views', <?php echo $singleViews; ?>],
					  ['Repeat Views', <?php echo $repeatViews; ?>]
					]);
					// Set chart options
					var options = {'title':'Breakdown of Repeat Views',
								   'width':400,
								   'height':400};

					// Instantiate and draw our chart, passing in some options.
					var chart = new google.visualization.PieChart(document.getElementById('chart_repeat_use'));
					chart.draw(data, options);
				  }
				</script>
			
		<?php
				$repeatViewText.= '<p><b>'.$count.'</b> people have returned to this project</p>';
				
				
				}
			}
		}
		
		
		
		
		/*Unique IPs*/
		$query = $conn->prepare("SELECT COUNT( DISTINCT(`ip`)  ) as `count`   FROM  `analytics_visitors` WHERE `project_id` = :projectID ");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			if( $query->rowCount() ){
				$count = $query->fetch();
				$count = $count["count"];
				$repeatViewText.= '<p><b>'.$count.'</b> unique IPs have visited this project</p>';
			}
		}
		
		/*Most Recent View */
		
		$query = $conn->prepare("SELECT max(`start_time`) as `mostRecent` FROM `analytics_visitors` WHERE `project_id` = :projectID LIMIT 1");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			if( $query->rowCount() ){
				$mostRecent = $query->fetch();
				$mostRecent = $mostRecent["mostRecent"];
				$repeatViewText.= '<p><b>'.$mostRecent.'</b> is the most recent visit</p>';
			}
		}
		
		/*Views by device */
			$query = $conn->prepare("SELECT 
				( SELECT COUNT(`device_type`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 1) as `desktop`, 
				( SELECT COUNT(`device_type`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 2) as `tablet`,
				( SELECT COUNT(`device_type`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 3) as `mobile`
				;");
			$query->bindParam(':projectID', $projID);
			if( $query->execute() ){
				if( $query->rowCount() ){
					$devices = $query->fetch();
					$desktop = $devices["desktop"];
					$tablet = $devices["tablet"];
					$mobile = $devices["mobile"];
					$total = ( $desktop + $mobile + $tablet );
					if( $total > 0 ){
						//echo '<p><b>'.$desktop.'</b> ('.(round( ($desktop/$total) ,2) * 100).'%) desktop user(s), <b>'.$tablet.'</b> ('.(round( ($tablet/$total) ,2) * 100).'%) tablet user(s), <b>'.$mobile.'</b> ('.(round( ($mobile/$total) ,2) * 100).'%) mobile user(s)</p>';
						?>
						
						<script>
						 // Set a callback to run when the Google Visualization API is loaded.
						  google.setOnLoadCallback(drawDeviceChart);
						  // Callback that creates and populates a data table,
						  // instantiates the pie chart, passes in the data and
						  // draws it.
						  function drawDeviceChart() {
							// Create the data table.
							var data = new google.visualization.DataTable();
							data.addColumn('string', 'Devices');
							data.addColumn('number', 'Devices');
							data.addRows([
							  ['Desktop', <?php echo $desktop; ?>],
							  ['Mobile', <?php echo $mobile; ?>],
							  ['Tablet', <?php echo $tablet; ?>]
							 
							]);
							// Set chart options
							var options = {'title':'Breakdown of User Devices',
										   'width':400,
										   'height':400};

							// Instantiate and draw our chart, passing in some options.
							var chart = new google.visualization.PieChart(document.getElementById('chart_device_use'));
							chart.draw(data, options);
						  }
						</script>
						<?php
					}
				}
			}
		?>
			<table>
				<tr>
					<td valign="top">
						<div id="chart_repeat_use"></div>
					</td>
					<td valign="top">
						<div id="chart_device_use"></div>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<?php echo $repeatViewText;	?>
					</td>
					<td valign="top">
						<?php if( $total > 0 ){ echo '<p><b>'.$desktop.'</b> ('.(round( ($desktop/$total) ,2) * 100).'%) desktop user(s), <b>'.$tablet.'</b> ('.(round( ($tablet/$total) ,2) * 100).'%) tablet user(s), <b>'.$mobile.'</b> ('.(round( ($mobile/$total) ,2) * 100).'%) mobile user(s)</p>'; } ?>
					</td>
				</tr>
			</table>
		<?php
			/*Fewest Steps */
			$query = $conn->prepare("SELECT MAX(`user_action`) as `mostActions`, MIN(`user_action`) as `fewestActions` FROM `analytics_events`
									WHERE `event_type` = 'Finished' AND `project_id` = :projectID");
			$query->bindParam(':projectID', $projID);
			if( $query->execute() ){
				if( $query->rowCount() ){
					$actions = $query->fetch();
					$max = $actions["mostActions"];	
					$min = $actions["fewestActions"];	
					echo '<hr /><p>The user who took the most actions to complete this tour took: <b>'.$max.'</b> actions, while the person to complete it in the fewest steps took only: <b>'.$min.'</b> steps to complete</p><hr />';
				}
			}
		
			$svf = "";
		
			/*Fewest Steps */
			$query = $conn->prepare("SELECT COUNT(`user_action`) as `count`, COUNT( DISTINCT(`visitor_id`) ) as `totalVisitors` FROM `analytics_events` WHERE `project_id` = :projectID");
			$query->bindParam(':projectID', $projID);
			if( $query->execute() ){
				if( $query->rowCount() ){
					$actions = $query->fetch();
					$numActions = $actions["count"];	
					$visitors = $actions["totalVisitors"];	
					if( $visitors > 0 ){
						$svf.= '<p><b>'.$visitors.'</b> visitors performed<b> '.$numActions.'</b> total actions, or roughly: <b>'.(round( ($numActions/$visitors) ,0) ).'</b> actions each.</p>';
					}
				}
			}
		
			/*People who start vs finish */
			
				
			
			$query = $conn->prepare("SELECT ( SELECT COUNT(`user_action`) FROM `analytics_events` WHERE `event_type` = 'START' AND `project_id` = :projectID) as `started`, (SELECT COUNT(`user_action`) FROM `analytics_events` WHERE `event_type` = 'Ends' AND `project_id` = :projectID ) as `ends`, (SELECT COUNT(*) FROM `analytics_visitors` WHERE `project_id` = :projectID) as `numUsers`, (SELECT COUNT(*) FROM `analytics_visitors` WHERE `project_id` = :projectID AND `end_time` IS NOT NULL ) as `oneTimeFinish`");
			$query->bindParam(':projectID', $projID);
			if( $query->execute() ){
				if( $query->rowCount() ){
					$actions = $query->fetch();
					$started = $actions["started"];	
					$finished = $actions["ends"];
					$numUsers =  $actions["numUsers"];
					$one_time_finish_count = $actions["oneTimeFinish"];
					
					$justStarted = $started - $finished;
					
					if( $finished > 0 ){
						$svf.= '<p><b>'.(round( ($finished/$started) ,2) * 100).'%</b> of users finish the whole tour for each time they start.</p>';
						?>
						
						<script>
						 // Set a callback to run when the Google Visualization API is loaded.
						  google.setOnLoadCallback(drawCompletionChart);
						  // Callback that creates and populates a data table,
						  // instantiates the pie chart, passes in the data and
						  // draws it.
						  function drawCompletionChart() {
							// Create the data table.
							var data = new google.visualization.DataTable();
							data.addColumn('string', 'Starts vs Finishes - Replayability');
							data.addColumn('number', 'Starts vs Finishes - Replayability');
							data.addRows([
							  ['Starts', <?php echo $justStarted; ?>],
							  ['Ends', <?php echo $finished; ?>]
							]);
							// Set chart options
							var options = {'title':'User Starts Vs Finishes',
										   'width':400,
										   'height':400};

							// Instantiate and draw our chart, passing in some options.
							var chart = new google.visualization.PieChart(document.getElementById('chart_user_completion'));
							chart.draw(data, options);
						  }
						
						
						</script>
						
						<?php
						}
						//count of entrys, vs entries by UNIQUE IP
						$query = $conn->prepare("SELECT ( SELECT COUNT(`entryID`) FROM `analytics_visitors` WHERE `project_id` = 18 AND `entryID` IS NOT NULL ) as `totalEntries`, ( SELECT COUNT(DISTINCT(`ip`)) FROM `analytics_visitors` WHERE `project_id` = 18 AND `entryID` IS NOT NULL ) as `UniqueIPEntries`");
						$query->bindParam(':projectID', $projID);
						if( $query->execute() ){
							if( $query->rowCount() ){
							$res = $query->fetch();
							$entries = $res['totalEntries'];
							$uniqueIP = $res['UniqueIPEntries'];
							
							?>
								<script>
								 // Set a callback to run when the Google Visualization API is loaded.
								  google.setOnLoadCallback(drawUniqueIPChart);
								  // Callback that creates and populates a data table,
								  // instantiates the pie chart, passes in the data and
								  // draws it.
								  function drawUniqueIPChart() {
									// Create the data table.
									var data = new google.visualization.DataTable();
									data.addColumn('string', 'Unique Entries by IP');
									data.addColumn('number', 'Unique Entries by IP');
									data.addRows([
									  ['Repeat IP Entries', <?php echo ($entries - $uniqueIP); ?>],
									  ['Unique IP Entries', <?php echo $uniqueIP; ?>]
									]);
									// Set chart options
									var options = {'title':'Unique Entries by IP',
												   'width':400,
												   'height':400};

									// Instantiate and draw our chart, passing in some options.
									var chart = new google.visualization.PieChart(document.getElementById('chart_uniqueIP'));
									chart.draw(data, options);
								  }
								
								
								</script>
						
						
						
							<?php
							}
						}
					
				}
			}
			
			
			
			

	?>
	
		<table>
				<tr>
					<td valign="top">
						<!--<div id="chart_completion"></div>-->
						completions by unique ip
						
						<div id="chart_uniqueIP"></div>
					</td>
					<td valign="top">
						<div id="chart_user_completion"></div>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<?php if( $numUsers > 0 ){ echo '<p><b>Started: </b>'.($numUsers - $one_time_finish_count).' <b>Finished: </b>'.$one_time_finish_count.', Total Users: '.$numUsers.' - '.(round( ($one_time_finish_count/$numUsers) ,2) * 100).'% completion rate.</p>'; } ?>
					</td>
					<td valign="top">
						<?php echo $svf; ?>
					</td>
				</tr>
			</table>
	
	
	
		<hr />
		<?php
			$query = $conn->prepare("SELECT 
			( SELECT COUNT(`device_type`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 1) as `desktop`, 
			( SELECT COUNT(`entryID`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 1 AND `entryID` IS NOT NULL)  as `desktopEntries`, 
			( SELECT COUNT(`end_time`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 1 AND `end_time` IS NOT NULL)  as `desktopCompletions`, 
			
			
			( SELECT COUNT(`device_type`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 2) as `tablet`,
			( SELECT COUNT(`entryID`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 2 AND `entryID` IS NOT NULL)  as `tabletEntries`,
			( SELECT COUNT(`end_time`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 2 AND `end_time` IS NOT NULL)  as `tabletCompletions`, 							
			
			( SELECT COUNT(`device_type`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 3) as `mobile`,
			( SELECT COUNT(`entryID`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 3 AND `entryID` IS NOT NULL)  as `mobileEntries`,
			( SELECT COUNT(`end_time`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `device_type` = 3 AND `end_time` IS NOT NULL)  as `mobileCompletions`,
			
			( SELECT COUNT(`visitor_id`)  FROM `analytics_visitors` WHERE `project_id` = :projectID  AND `end_time` IS NOT NULL AND `filled_out_entry` IS NOT NULL)  as `CompletionEntries`
			
			
			;");
			$query->bindParam(':projectID', $projID);
			if( $query->execute() ){
				if( $query->rowCount() ){
					$completions = $query->fetch();
					//echo '<pre>'.print_r( $completions, true ).'</pre>';
					$desktopUserCnt = $completions['desktop'];
					$desktopEntries = $completions['desktopEntries'];
					$desktopCompletions = $completions['desktopCompletions'];
					
					$tabletUserCnt = $completions['tablet'];
					$tabletEntries = $completions['tabletEntries'];
					$tabletCompletions = $completions['tabletCompletions'];
					
					
					$mobileUserCnt = $completions['mobile'];
					$mobileEntries = $completions['mobileEntries'];
					$mobileCompletions = $completions['mobileCompletions'];
					
					$totalEntries =	( $mobileEntries + $tabletEntries + $tabletEntries );
					$totalCompletions = ( $mobileCompletions + $tabletCompletions + $desktopCompletions );
					
					$completionEntries = $completions['CompletionEntries'];
					
					//echo $totalEntries;
					//echo "Completion: D".$desktopCompletions.", T:". $tabletCompletions.", M: ".$mobileCompletions;
					
					?>
					
					<table>
						<tr>
							<td valign="top">
								<h4>Completions by Device</h4>
								<div id="chart_completion_by_device"></div>
							</td>
							<td valign="top">
								<h4>Entries by Device</h4>
								<div id="chart_entries_by_device"></div>
							</td>
						</tr>
						<tr>
							<td>
								<h4>Completions vs Entries (People who finish tour and submit entry)</h4>
								<div id="drawCompletionsVsEntriesChart"></div>
							</td>
							<td></td>
						
						</tr>
					
					</table>
					
					
						<script>
						 // Set a callback to run when the Google Visualization API is loaded.
						  google.setOnLoadCallback(drawCompletionbyDeviceChart);
						  // Callback that creates and populates a data table,
						  // instantiates the pie chart, passes in the data and
						  // draws it.
						  function drawCompletionbyDeviceChart() {
							// Create the data table.
							var data = new google.visualization.DataTable();
							data.addColumn('string', 'Tour Completion by Device');
							data.addColumn('number', 'Tour Completion by Device');
							data.addRows([
							  ['Desktop', <?php echo $desktopCompletions; ?>],
							  ['Tablet', <?php echo $tabletCompletions; ?>],
							  ['Mobile', <?php echo $mobileCompletions; ?>]
							]);
							// Set chart options
							var options = {'title':'Device Type',
										   'width':400,
										   'height':400};

							// Instantiate and draw our chart, passing in some options.
							var chart = new google.visualization.PieChart(document.getElementById('chart_completion_by_device'));
							chart.draw(data, options);
						  }

						 // Set a callback to run when the Google Visualization API is loaded.
						  google.setOnLoadCallback(drawEntriesbyDeviceChart);
						  // Callback that creates and populates a data table,
						  // instantiates the pie chart, passes in the data and
						  // draws it.
						  function drawEntriesbyDeviceChart() {
							// Create the data table.
							var data = new google.visualization.DataTable();
							data.addColumn('string', 'Entries by Device');
							data.addColumn('number', 'Entries by Device');
							data.addRows([
							  ['Desktop', <?php echo $desktopEntries; ?>],
							  ['Tablet', <?php echo $tabletEntries; ?>],
							  ['Mobile', <?php echo $mobileEntries; ?>]
							]);
							// Set chart options
							var options = {'title':'Device Type',
										   'width':400,
										   'height':400};

							// Instantiate and draw our chart, passing in some options.
							var chart = new google.visualization.PieChart(document.getElementById('chart_entries_by_device'));
							chart.draw(data, options);
						  }
						  
						  
						  
						  
						  
						   // Set a callback to run when the Google Visualization API is loaded.
						  google.setOnLoadCallback(drawCompletionsVsEntriesChart);
						  // Callback that creates and populates a data table,
						  // instantiates the pie chart, passes in the data and
						  // draws it.
						  function drawCompletionsVsEntriesChart() {
							// Create the data table.
							var data = new google.visualization.DataTable();
							data.addColumn('string', 'Completions vs Entries');
							data.addColumn('number', 'Completions vs Entries');
							data.addRows([
							  ['Finished %', <?php echo (($totalCompletions - $completionEntries) > 0 ?  ($totalCompletions - $completionEntries) : 0); ?>],
							  ['Finished And Entered %', <?php echo $completionEntries; ?>]
							  
							]);
							// Set chart options
							var options = {'title':'',
										   'width':400,
										   'height':400};

							// Instantiate and draw our chart, passing in some options.
							var chart = new google.visualization.PieChart(document.getElementById('drawCompletionsVsEntriesChart'));
							chart.draw(data, options);
						  }
						  
						  
						  
						  
						</script>
					
					<?php
				}
			}
		?>
		
	
	
		<hr />
	<h3> Contest Entries </h3>
	
	<p><a href="<?php echo fixedPath; ?>/administration/ProjectContestEntries?projectID=<?php echo $projID; ?>" class="button wa">View All Entries</a></p>
	
	<h3>User Trails</h3>
	<?php
		$query = $conn->prepare("SELECT * FROM `analytics_visitors` WHERE `project_id` = :projectID");
		$query->bindParam(':projectID', $projID);
		if( $query->execute() ){
			$result = $query->fetchAll();
			if( is_array( $result ) ){
				?>
				<table class="tablesorter">
				<thead> 
					<tr> 
						
						<th><sup>nth</sup>Visitor</th>
						<th>UID</th>
						<th>Path</th>
						<th>Start Time</th>
						<th>End Time</th>
						<th>IP</th>
						<th>Device Type</th>
						<th>Returned?</th>
						<th>Entered Contest</th>
					</tr> 
				</thead>
				<tbody>
				<?php
				$count = 1;
				
				$computers = 0;
				$tablets = 0;
				$mobiles = 0;
				$returns = 0;
				$entries = 0;
				
				foreach( $result as $row ){
					//echo print_r( $row, true ).'<br />';
					$DT = $row['device_type'];
					
					
					echo '<tr><td>'.$count.'</td><td>'.$row['visitor_id'].'</td><td><a class="button wa" onClick="viewPath('.$row['visitor_id'].')"><i class="fa fa-sitemap"></i>View Path</a></td><td>'.$row['start_time'].'</td><td>'.$row['end_time'].'</td><td>'.$row['ip'].'</td><td>'.($DT == 1 ? "<i class='fa fa-desktop'></i> Desktop" : ($DT == 2 ? "<i class='fa fa-tablet'></i> Tablet" : "<i class='fa fa-mobile'></i> Mobile") ).'</td><td>'.$row['has_returned'].'</td><td>'.(($row['filled_out_entry'] == 1) ? "Yes <a onClick='viewEntry(".$row['entryID'].")' class='button wa'>View Entry</a>" : "No").'</td></tr>';
					
					$returns+= $row['has_returned'];
					if( $DT == 1 ){ $computers++; }elseif( $DT == 2 ){  $tablets++; }elseif($DT == 3 ){  $mobiles++; }
					if( $row['filled_out_entry'] == 1){ $entries++; }
					
					
					$count++;
				}
				?>
				</tbody>
				</table>
				<p>Total Returns : <?php echo $returns; ?></p>
				<p>Total Computer Visitors: <?php echo $computers; ?></p>
				<p>Total Tablet Visitors: <?php echo $tablets; ?></p>
				<p>Total Mobile Visitors: <?php echo $mobiles;  ?></p>
				<p>Total Contest Entries: <?php echo $entries; ?></p>
				<br />
				<button id="clearAnalytics" onClick="clearAnalytics(<?php echo $projID; ?>)" class="button wa"><i class="fa fa-eraser"></i> Clear Analytics</button>
				<a class="button wa" href="<?php echo fixedPath; ?>/administration/ProjectAnalyticsExport.php?projectID=<?php echo $projID; ?>"><i class="fa fa-bolt"></i> Export Analytics</a>
				<a class="button wa" href="<?php echo fixedPath; ?>/administration/ProjectAnalyticsImport.php?projectID=<?php echo $projID; ?>"><i class="fa fa-level-up"></i> Import Analytics (TBD)</a>
				<br />
				<?php
			}
		}
	?>
	
	
	<?php footerMenu($projID); ?>

	
	<?php
		}else{
		echo "<h1>Error: a project ID is required</h1>";
	}
	pageFooter();
?>