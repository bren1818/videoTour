<?php
	include "../includes/includes.php";
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projID = $_REQUEST['projectID'];
		$conn = getConnection();
		$project = new Projects($conn);
		$project = $project->load($projID);
	}
	
	if( $project->getId() > 0 ){
	
	$analyticData = array();
	
	$analyticData["Project"] = $project;
	$analyticData["Project"]->setConnection( "" );
	
	
	//SELECT * FROM `analytics_visitors` --> analytics_visitors
	$visitors = new analytics_visitors($conn);
	$visitors = $visitors->getList( $project->getId() );
	
	$analyticData["Analytics_visitors"] = $visitors;
	
	//pa( $visitors ); --full list of visitors
	
	
	//SELECT * FROM `analytics_events`   --> analytics_visitor_event
	$events = new analytics_visitor_event( $conn );
	$events = $events->getList( $project->getId() );
	
	$analyticData["Analytics_visitor_event"] = $events;
	
	//pa ( $events );
	
	//SELECT * FROM `form_entry`		 --> entry
	$entries = new entry( $conn );
	$entries = $entries->getList( $project->getId() );
	
	$analyticData["Entries"] = $entries;
	
	//pa( $entries );
	
	
	$data =  serialize( $analyticData );
	ob_clean();
	header('Content-Disposition: attachement; filename="project_'.$projID.'_analytics.projAnalytics";');
	echo $data;
	flush();
	}
?>