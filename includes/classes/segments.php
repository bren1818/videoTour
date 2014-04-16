<?php
	class Segments{
		private $id;
		private $projectID;
		private $clipID;
		private $decisionTreeID;
		private $note;
		private $connection = null;
		private $awardedBadgeID = "";
		
	
		function __construct($db = null) {
			if( $db ){
				$this->connection = $db;
			}
		}
		
		function setConnection($conn){
			$this->connection = $conn;
		}
		
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setProjectID($projectID) { $this->projectID = $projectID; }
		function getProjectID() { return $this->projectID; }
		function setClipID($clipID) { $this->clipID = $clipID; }
		function getClipID() { return $this->clipID; }
		function setDecisionTreeID($decisionTreeID) { $this->decisionTreeID = $decisionTreeID; }
		function getDecisionTreeID() { return $this->decisionTreeID; }
		function setNote($note) { $this->note = $note; }
		function getNote() { return $this->note; }

		function setBadge($awardedBadgeID) { $this->awardedBadgeID = $awardedBadgeID; }
		function getBadge() { return $this->awardedBadgeID; }
		
		
		function save(){
		
			$clipID = $this->getClipID();
			$projectID = $this->getProjectID();
			$decisionTreeID = $this->getDecisionTreeID();
			$note =  $this->getNote();
			$badgeID = $this->getBadge();
		
			if( $this->getId() != "" ){
				$id = $this->getId();
			
				//update
				$query = $this->connection->prepare("UPDATE `segments` SET `clipID` = :clipID, `decisionTreeID` = :decisionTreeID, `note` = :note, `awardedBadgeID` = :badgeID WHERE `segments`.`id` =:id;");
				
				$query->bindParam(':clipID', $clipID);
				$query->bindParam(':decisionTreeID', $decisionTreeID);
				$query->bindParam(':note', $note);
				$query->bindParam(':badgeID', $badgeID);
				$query->bindParam(':id', $id);
				
				if( $query->execute() ){
					return $this->getId();
				}else{
					return -1;
				}
				
			}else{
				//insert
			
				$query = $this->connection->prepare("INSERT INTO `segments` (`id`, `projectID`, `clipID`, `decisionTreeID`, `note`, `awardedBadgeID`) VALUES (NULL, :projectID, :clipID, :decisionTreeID, :note, :badge);");
				$query->bindParam(':projectID', $projectID);
				$query->bindParam(':clipID', $clipID);
				$query->bindParam(':decisionTreeID', $decisionTreeID);
				$query->bindParam(':note', $note);
				$query->bindParam(':badge', $badgeID);
				
				$query->execute();	
			

				$this->setId( $this->connection->lastInsertId() );
				
				return $this->getId();
			}
		
		}
		
		function delete($id = null){
			if( $id == null && $this->getID() != "" ){
				//delete $this
				$segmentID = $this->getID();
			}else{
				//delete id
				$segmentID = $id;
			}
			
			if( $this->connection != null ){
				$query = $this->connection->prepare("DELETE FROM `segments` WHERE `segments`.`id` = :id");
				$query->bindParam(':id', $segmentID);
				if( $query->execute() ){
					return 1;
				}else{
					return 0;
				}	
			}else{
				return 0;
			}
		}
		
		function load($segmentID){
			if( $segmentID != "" ){
				if( $this->connection ){
					$query = $this->connection->prepare("SELECT * FROM `segments` WHERE `id` = :segmentID");
					$query->bindParam(':segmentID', $segmentID);
					if( $query->execute() ){
						$segment = $query->fetchObject("Segments");
						if( is_object( $segment ) ){
						$segment->setConnection( $this->connection );
						}
						return $segment;
					}else{
					
					}
				}
			}
		}
		
		function  getbyDecisionTreeID($decisionTreeID){
			if( $this->connection ){
					$query = $this->connection->prepare("SELECT * FROM `segments` WHERE `decisionTreeID` = :decisionTreeID");
					$query->bindParam(':decisionTreeID', $decisionTreeID);
					$query->execute();
					$segment = $query->fetchObject("Segments");
					
					if( is_object( $segment ) ){
						$segment->setConnection( $this->connection );
						}
						return $segment;
					
			}
		}
		
		
		function getList($projectID){
			if( $this->connection ){
				$segments = array();
				if( $projectID == null ){
					//list all Clips
					$query = $this->connection->prepare("SELECT * FROM `segments`");
					$query->execute();
					while( $result = $query->fetchObject("Segments") ){
						$segments[] = $result;
					}
				}else{
					//fetch by project id
					$query = $this->connection->prepare("SELECT * FROM `segments` WHERE `projectID` = :projectID");
					$query->bindParam(':projectID', $projectID);
					$query->execute();
					while( $result = $query->fetchObject("Segments") ){
						$segments[] = $result;
					}
				}
				return $segments;
			}else{
				return array();
			}
		
		
		}
		
		
		function getUsageCount($projectID = null, $segmentID=null){
			if( $projectID == null && $this->getProjectID != "" ){
				$pid = $this->getProjectID();
			}else{
				$pid = $projectID;
			}
			
			if( $segmentID == null && $this->getId != "" ){
				$sid = $this->getId();
			}else{
				$sid = $segmentID;
			}
		
			if( $this->connection ){
				
				$query = $this->connection->prepare("SELECT SUM( `uses` ) as `uses` FROM (
					SELECT COUNT(*) as `uses` FROM `decisions` WHERE `projectID` = :projectID AND `segmentID` = :segmentID
					UNION ALL
					SELECT COUNT(*) as `uses` FROM `projects` WHERE `id` = :projectID AND `startingSegmentID` = :segmentID
				)a");
				$query->bindParam(':projectID', $pid);
				$query->bindParam(':segmentID', $sid);
			
				$query->execute();
				if( $query->rowCount() ){
					$use = $query->fetch();
					$use = $use["uses"];
					return $use;
				}else{
					return -1;
				}
			}else{
				return -1;
			}
			
		}
		
	}
?>