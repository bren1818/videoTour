<?php
	class Decisions{
		private $id;
		private $projectID;
		private $clipID;
		private $decisionTreeID;
		private $segmentID;
		private $continues;
		private $ends;
		private $note;
		private $text;
		private $connection;
		private $order;
		private $forcedBadgeID;
		
		
		function __construct($db = null) {
			if( $db ){
				$this->connection = $db;
			}
		}
		
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setProjectID($projectID) { $this->projectID = $projectID; }
		function getProjectID() { return $this->projectID; }
		function setClipID($clipID) { $this->clipID = $clipID; }
		function getClipID() { return $this->clipID; }
		function setDecisionTreeID($decisionTreeID) { $this->decisionTreeID = $decisionTreeID; }
		function getDecisionTreeID() { return $this->decisionTreeID; }
		function setSegmentID($segmentID) { $this->segmentID = $segmentID; }
		function getSegmentID() { return $this->segmentID; }
		function setContinues($continues) { $this->continues = $continues; }
		function getContinues() { return $this->continues; }
		function setEnds($ends) { $this->ends = $ends; }
		function getEnds() { return $this->ends; }
		function setNote($note) { $this->note = $note; }
		function getNote() { return $this->note; }
		
		function setText($text) { $this->text = $text; }
		function getText() { return $this->text; }
		
		function setOrder($order) { $this->order = $order; }
		function getOrder() { return $this->order; }
		
		function setForcedBadgeID($forcedBadgeID) { $this->forcedBadgeID = $forcedBadgeID;  }
		function getForcedBadgeID() { return $this->forcedBadgeID; }
		
		
		function setConnection( $conn ){
			$this->connection = $conn;
		}
		
		function save(){
		
			$projectID = $this->getProjectID();
			$decisionTreeID =  $this->getDecisionTreeID();
			$clipID = $this->getClipID();
			$segmentID = $this->getSegmentID();
			$continues = $this->getContinues();
			$ends = $this->getEnds();
			$note = $this->getNote();
			$text = $this->getText();
			$order = $this->getOrder();
			$forcedBadgeID = $this->getForcedBadgeID();
		
			if( $this->getId() == "" ){
				//insert
				$query = $this->connection->prepare("INSERT INTO `decisions` (`id`, `projectID`, `decisionTreeID`, `clipID`, `segmentID`, `continues`, `ends`, `note`, `text`, `order`) VALUES (NULL, :projectID, :decisionTreeID, :clipID, :segmentID, :continues, :ends, :note, :text, :order);");

				$query->bindParam(':projectID', $projectID  );
				$query->bindParam(':decisionTreeID', $decisionTreeID );
				$query->bindParam(':clipID', $clipID );
				$query->bindParam(':segmentID',$segmentID  );
				$query->bindParam(':continues', $continues  );
				$query->bindParam(':ends',$ends  );
				$query->bindParam(':note',$note  );
				$query->bindParam(':text', $text  );
				$query->bindParam(':order', $order  );
				
				
				$query->execute();
				$this->setId( $this->connection->lastInsertId() );
				return $this->getId(); 
			}else{
				//update
				
				$query = $this->connection->prepare("UPDATE `decisions` SET `clipID` = :clipID, `segmentID` = :segmentID, `continues` = :continues, `ends` = :ends, `note` = :note, `text` = :text, `order` = :order, `forcedBadgeID` = :forcedBadgeID WHERE `decisions`.`id` = :id;");
				
				$id = $this->getId();
				
				$query->bindParam(':clipID', $clipID );
				$query->bindParam(':segmentID',$segmentID  );
				$query->bindParam(':continues', $continues  );
				$query->bindParam(':ends',$ends  );
				$query->bindParam(':note',$note  );
				$query->bindParam(':text', $text  );
				$query->bindParam(':id', $id  );
				$query->bindParam(':order', $order  );
				$query->bindParam(':forcedBadgeID', $forcedBadgeID  );
				if( $query->execute() ){
					return 1;
				}else{
					return 0;
				}
				
			}
		}
		
		function load($id){
		
		
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `decisions` WHERE `id` = :decisionID");
				$query->bindParam(':decisionID', $id);
				$query->execute();
				$decision = $query->fetchObject("Decisions");
				$decision->setConnection( $this->connection );
				return $decision;
			}
		}
		
		function delete($id = null){
			if( $id == null && $this->getID() != "" ){
				//delete $this
				$decisionID = $this->getID();
			}else{
				//delete id
				$decisionID = $id;
			}
			if( $this->connection ){
				$query = $this->connection->prepare("DELETE FROM `decisions` WHERE `decisions`.`id` = :id");
				$query->bindParam(':id', $decisionID);
				if( $query->execute() ){
					return 1;
				}else{
					return -1;
				}
			}else{
				return -1;
			}
		}
		
		function getListbyProject($projectID=null){
			if( $this->connection ){
				$decisions = array();
				if( $projectID != null ){
					//list all Decision Trees
					$query = $this->connection->prepare("SELECT * FROM `decisions` WHERE `projectID` =  :PID");
					$query->bindParam(':PID', $projectID);
					$query->execute();
					while( $result = $query->fetchObject("Decisions") ){
						$decisions[] = $result;
					}
				}
				return $decisions;
			}else{
				return array();
			}
		}
		
		
		function getList($decisionTreeID=null){
			if( $this->connection ){
				$decisions = array();
				if( $decisionTreeID == null ){
					//list all Decision Trees
					$query = $this->connection->prepare("SELECT * FROM `decisions` order by `order`");
					$query->execute();
					while( $result = $query->fetchObject("Decisions") ){
						$decisions[] = $result;
					}
				}else{
					//fetch by project id
					$query = $this->connection->prepare("SELECT * FROM `decisions` WHERE `decisionTreeID` = :decisionTreeID order by `order`");
					$query->bindParam(':decisionTreeID', $decisionTreeID);
					$query->execute();
					while( $result = $query->fetchObject("Decisions") ){
						if( $this->connection ){
							$result->setConnection( $this->connection );	
						}
						
						$decisions[] = $result;
					}
				}
				return $decisions;
			}else{
				return array();
			}
		}
	}
?>