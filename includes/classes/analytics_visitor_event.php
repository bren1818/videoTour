<?php
	
	class analytics_visitor_event{
		private $user_action;
		private $project_id;
		private $event_time;
		private $event_type;
		private $on_step;
		private $segment_id;
		private $clipID;
		private $visitor_id;
		private $connection;
		
		function __construct($db = null) {
			if( $db ){
				$this->connection = $db;
			}
		}
		
		function setConnection($conn){
			$this->connection = $conn;
		}
		
		function save(){
			if( $this->connection ){
				if( $this->getVisitor_id() != "" ){
					$visitor_id = $this->getVisitor_id();
					$type = $this->getEvent_type();
					$step = $this->getOn_step();
					$user_action = $this->getUser_action();
					$segmentID = $this->getSegment_id();
					$clipID = $this->getClipID();
					$projectID = $this->getProjectID();
					
				
					$query = $this->connection->prepare("INSERT INTO `videotour`.`analytics_events` (`event_id`,  `visitor_id`, `project_ID`, `event_time`, `event_type`, `on_step`, `user_action`, `segment_id`, `clipID`) VALUES (NULL, :id, :projID, CURRENT_TIMESTAMP, :type, :step, :action, :segmentID, :clipID);");
					$query->bindParam(':id', $visitor_id);
					$query->bindParam(':projID', $projectID);
					$query->bindParam(':type', $type);
					$query->bindParam(':step', $step);
					$query->bindParam(':action', $user_action);
					$query->bindParam(':segmentID', $segmentID);
					$query->bindParam(':clipID', $clipID);
					if( $query->execute() ){
						return 1;
					}else{
						return -1;
					}
				}else{
					return -1;
				}
			}
		}
		
		function setUser_action($user_action){
			$this->user_action = $user_action;
		}
		
		function getUser_action(){
			return $this->user_action;
		}
	
		function setClipID($clipID){
			$this->clipID = $clipID;
		}
		
		function getClipID(){
			return $this->clipID;
		}
		
		
		function setProjectID($project_id){
			$this->project_id = $project_id;
		}
		
		function getProjectID(){
			return $this->project_id;
		}
	
		function setEvent_time($event_time) { $this->event_time = $event_time; }
		function getEvent_time() { return $this->event_time; }
		function setEvent_type($event_type) { $this->event_type = $event_type; }
		function getEvent_type() { return $this->event_type; }
		function setOn_step($on_step) { $this->on_step = $on_step; }
		function getOn_step() { return $this->on_step; }
		function setSegment_id($segment_id) { $this->segment_id = $segment_id; }
		function getSegment_id() { return $this->segment_id; }
		function setVisitor_id($visitor_id) { $this->visitor_id = $visitor_id; }
		function getVisitor_id() { return $this->visitor_id; }

	
	}


?>