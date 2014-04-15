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
		
			if( $this->getId() == "" ){
				//insert
				$query = $this->connection->prepare("INSERT INTO `videotour`.`decisions` (`id`, `projectID`, `decisionTreeID`, `clipID`, `segmentID`, `continues`, `ends`, `note`, `text`) VALUES (NULL, :projectID, :decisionTreeID, :clipID, :segmentID, :continues, :ends, :note, :text);");

				$query->bindParam(':projectID', $projectID  );
				$query->bindParam(':decisionTreeID', $decisionTreeID );
				$query->bindParam(':clipID', $clipID );
				$query->bindParam(':segmentID',$segmentID  );
				$query->bindParam(':continues', $continues  );
				$query->bindParam(':ends',$ends  );
				$query->bindParam(':note',$note  );
				$query->bindParam(':text', $text  );
				$query->execute();
				$this->setId( $this->connection->lastInsertId() );
				return $this->getId(); 
			}else{
				//update
				
				$query = $this->connection->prepare("UPDATE `videotour`.`decisions` SET `clipID` = :clipID, `segmentID` = :segmentID, `continues` = :continues, `ends` = :ends, `note` = :note, `text` = :text WHERE `decisions`.`id` = :id;");
				
				$id = $this->getId();
				
				$query->bindParam(':clipID', $clipID );
				$query->bindParam(':segmentID',$segmentID  );
				$query->bindParam(':continues', $continues  );
				$query->bindParam(':ends',$ends  );
				$query->bindParam(':note',$note  );
				$query->bindParam(':text', $text  );
				$query->bindParam(':id', $id  );
				
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
				$query = $this->connection->prepare("DELETE FROM `videotour`.`decisions` WHERE `decisions`.`id` = :id");
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
		
		
		function getList($decisionTreeID=null){
			if( $this->connection ){
				$decisions = array();
				if( $decisionTreeID == null ){
					//list all Decision Trees
					$query = $this->connection->prepare("SELECT * FROM `decisions`");
					$query->execute();
					while( $result = $query->fetchObject("Decisions") ){
						$decisions[] = $result;
					}
				}else{
					//fetch by project id
					$query = $this->connection->prepare("SELECT * FROM `decisions` WHERE `decisionTreeID` = :decisionTreeID");
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