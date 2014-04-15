<?php
	class DecisionTree{
		private $id;
		private $projectID;
		private $note;
		private $step;
		private $title;

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
		function setNote($note) { $this->note = $note; }
		function getNote() { return $this->note; }
		function setStep($step) { $this->step = $step; }
		function getStep() { return $this->step; }
		function setTitle($title) { $this->title = $title; }
		function getTitle() { return $this->title; }	
		
		function load($DecisionTreeID){
		if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `decisiontree` WHERE `id` = :DecisionTreeID");
				$query->bindParam(':DecisionTreeID', $DecisionTreeID);
				if( $query->execute() ){
				$decisionTree = $query->fetchObject("DecisionTree");
					if( is_object( $decisionTree )){
						$decisionTree->setConnection( $this->connection );	
					}
				}
				
				return  $decisionTree;
			}
		
		}
		
		function save(){
			
				$projectID = $this->getProjectID();
				$note = $this->getNote();
				$title = $this->getTitle();
				$step = $this->getStep();
		
		
			if( $this->getId() != "" ){
				//update
				$id =  $this->getId();
				
				$query = $this->connection->prepare("UPDATE `videotour`.`decisiontree` SET `note` = :note, `step` = :step, `title` = :title WHERE `decisiontree`.`id` = :id;");
				$query->bindParam(':note', $note);
				$query->bindParam(':step', $step);
				$query->bindParam(':title', $title);
				$query->bindParam(':id', $id);
				
				if( $query->execute() ){
					return 1;
				}else{
					return -1;
				}
				
			}else{
			
				$query = $this->connection->prepare("INSERT INTO `videotour`.`decisiontree` (`id`, `projectID`, `note`, `step`, `title`) VALUES (NULL, :projectID, :note, :step, :title);");
				$query->bindParam(':projectID', $projectID);
				$query->bindParam(':note', $note);
				$query->bindParam(':step', $step);
				$query->bindParam(':title', $title);
				
				
				$query->execute();	
				

				$this->setId( $this->connection->lastInsertId() );
					
				return $this->getId();
			
			}
		}
		
		function delete($id = null){
			if( $id == null && $this->getID() != "" ){
				//delete $this
				$dtID = $this->getID();
			}else{
				//delete id
				$dtID = $id;
			}
			
			if( $this->connection != null ){
				$deletedDT = 0;
				$deletedDecisions = 0;
				$numDecisions = 0;
			
				$this->connection->beginTransaction();
				try{
					$query = $this->connection->prepare("DELETE FROM `decisiontree` WHERE `id` = :id");
					$query->bindParam(':id', $dtID);
					if( $query->execute() ){
						$deletedDT = 1;
					}
					
					$query = $this->connection->prepare("SELECT COUNT(*) as `uses` FROM `decisions` WHERE `decisionTreeID` = :id");
					$query->bindParam(':id', $dtID);
					if( $query->execute() ){
					$use = $query->fetch();
					$numDecisions = $use['uses'];
					}
					
					$query = $this->connection->prepare("DELETE FROM `decisions` WHERE `decisionTreeID` = :id");
					$query->bindParam(':id', $dtID);
					if( $query->execute() ){
						$deletedDecisions = 1;
					}
				
					$this->connection->commit();
				
				}catch(PDOException $e) {
					// roll back transaction
					$this->connection->rollback();
					$deletedDT = 0;
					$deletedDecisions = 0;
					$numDecisions = 0;
				}
				
				
				return array("DeletedTree"=> $deletedDT, "DeletedDecisions"=> ( $deletedDecisions == 1 ? $numDecisions : 0 ) );
				
			}else{
				return 0;
			}
		}
		
		
		
		function getList($projectID=null){
			if( $this->connection ){
				$decisionTrees = array();
				if( $projectID == null ){
					//list all Decision Trees
					$query = $this->connection->prepare("SELECT * FROM `decisiontree`");
					$query->execute();
					while( $result = $query->fetchObject("DecisionTree") ){
						$decisionTrees[] = $result;
					}
				}else{
					//fetch by project id
					$query = $this->connection->prepare("SELECT * FROM `decisiontree` WHERE `projectID` = :projectID");
					$query->bindParam(':projectID', $projectID);
					$query->execute();
					while( $result = $query->fetchObject("DecisionTree") ){
						$decisionTrees[] = $result;
					}
				}
				return $decisionTrees;
			}else{
				return array();
			}
		}
		
	}
?>