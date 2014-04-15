<?php

	class analytics_visitors{
		private $visitor_id;
		private $project_id;
		private $start_time;
		private $end_time;
		private $ip;
		private $device_type;
		private $has_returned;
		private $connection;
		
		private $entryID;
		private $filled_out_entry;
		
		
		function __construct($db = null) {
			if( $db ){
				$this->connection = $db;
			}
		}
		
		function setConnection($conn){
			$this->connection = $conn;
		}
		
		function exists($visitor_id){
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT COUNT(*) as `count` FROM `analytics_visitors` WHERE `visitor_id` = :id");
				$query->bindParam(':id', $visitor_id);
				if( $query->execute() ){
					if( $query->rowCount() ){
						$count = $query->fetch();
						$count = $count["count"];
					
						return $count;
						
					}
				}
				
				
			}else{
				return -1;
			}
		}
		
		function load($id){
			if( $this->connection ) {
				if( $id != "" ){
					$query = $this->connection->prepare("SELECT * FROM `analytics_visitors` WHERE `visitor_id` = :ID");
					$query->bindParam(':ID', $id);
					if( $query->execute() ){
						$analytics_visitors = $query->fetchObject("analytics_visitors");
						if( is_object( $analytics_visitors ) ){
						$analytics_visitors->setConnection( $this->connection );
						}
						return $analytics_visitors;
					}
				}
				
			}
		}
		
		function save(){
			$projectID = $this->getProjectId();
			$ip = $this->getIp();
			$type = $this->getDeviceType();
			if( $this->connection ){
				if( $this->getId() != "" ){
					$id = $this->getId();
					
					//$endTime = ($this->getEndTme() == "" ? "CURRENT_TIMESTAMP" : $this->getEndTme());
					$returned = $this->getHasReturned();
					$startTime = $this->getStartTime();
				
					$query = $this->connection->prepare("UPDATE `videotour`.`analytics_visitors` SET `end_time` = CURRENT_TIMESTAMP, `start_time` = :STARTTIME, `device_type` = :DEVICETYPE, `has_returned` = :RETURNED WHERE `analytics_visitors`.`visitor_id` = :ID;");
					$query->bindParam(':STARTTIME', $startTime);
					$query->bindParam(':DEVICETYPE', $type);
					$query->bindParam(':RETURNED', $returned);
					$query->bindParam(':ID', $id);
					
					if( $query->execute() ){
						$this->setId( $this->connection->lastInsertId() );
						return $this->getId();
					}else{
						return -1;
					}

				}else{
					
					$query = $this->connection->prepare("INSERT INTO `videotour`.`analytics_visitors` (`visitor_id`, `project_id`, `start_time`, `end_time`, `ip`, `device_type`, `has_returned`) VALUES (NULL, :projectID, CURRENT_TIMESTAMP, NULL, :IP, :type, '0');");
					$query->bindParam(':projectID', $projectID);
					$query->bindParam(':IP', $ip);
					$query->bindParam(':type', $type);
					
					if( $query->execute() ){
						$this->setId( $this->connection->lastInsertId() );
						return $this->getId();
					}else{
						return -1;
					}
				}
			}
		}
		
		function saveEnded(){
			if( $this->connection ){
				if( $this->getId() != "" ){
					$id = $this->getId();
					$query = $this->connection->prepare("UPDATE `videotour`.`analytics_visitors` SET `end_time` = CURRENT_TIMESTAMP WHERE `analytics_visitors`.`visitor_id` = :id;");
					$query->bindParam(':projectID', $id);
					if( $query->execute() ){
						return 1;
					}else{
						return 0;
					}
				}
			}
		}
		
		
		function setId($visitor_id) { $this->visitor_id = $visitor_id; }
		function getId() { return $this->visitor_id; }
		function setProjectId($project_id) { $this->project_id = $project_id; }
		function getProjectId() { return $this->project_id; }
		function setStartTime($start_time) { $this->start_time = $start_time; }
		function getStartTime() { return $this->start_time; }
		function setEndTime($end_time) { $this->end_time = $end_time; }
		function getEndTme() { return $this->end_time; }
		function setIp($ip) { $this->ip = $ip; }
		function getIp() { return $this->ip; }
		function setDeviceType($device_type) { $this->device_type = $device_type; }
		function getDeviceType() { return $this->device_type; }
		function setHasReturned($has_returned) { $this->has_returned = $has_returned; }
		function getHasReturned() { return $this->has_returned; }
	
		function setEntryID($entryID) { $this->entryID = $entryID; }
		function getEntryID() { return $this->entryID; }
		function setFilled_out_entry($filled_out_entry) { $this->filled_out_entry = $filled_out_entry; }
		function getFilled_out_entry() { return $this->filled_out_entry; }
	
	
	
	}
?>