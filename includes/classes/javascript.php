<?php
	class javascript{
		private $id;
		private $projectID;
		private $JAVASCRIPT;
		private $connection = null;
		
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
		function setJS($JS) { $this->JAVASCRIPT = $JS; }
		function getJS() { return $this->JAVASCRIPT; }

		function save(){
			$id = $this->getId();
			$projectID = $this->getProjectID();
			$JS = $this->getJS();
			
			if( $id != "" ){
				//update
				$query = $this->connection->prepare("UPDATE `js` SET `JAVASCRIPT` = :JS WHERE  `js`.`id` = :id;");
				$query->bindParam(':JS', $JS);
				$query->bindParam(':id', $id);
				if( $query->execute() ){
					return $this->getId();
				}else{
					return 0;
				}
			}else{
				//insert
				$query = $this->connection->prepare("INSERT INTO `js` (`id`, `projectID`, `JAVASCRIPT`) VALUES (NULL, :projectID, :JS);");
				$query->bindParam(':projectID', $projectID);
				$query->bindParam(':JS', $JS);
				if( $query->execute() ){
					$this->setId( $this->connection->lastInsertId() );
					return $this->getId();
				}else{
					return 0;
				}
			}
		}
		
		function loadByProjectID($id = null){
			$query = $this->connection->prepare("SELECT * FROM `js` WHERE `projectID` = :projectID");
			$query->bindParam(':projectID', $id);
			if( $query->execute() ){
				$SS = $query->fetchObject("javascript");
				if( is_object( $SS ) ){
					$SS->setConnection( $this->connection );
				}
				return $SS;
			}
		}
		
		function load($id = null){
			$query = $this->connection->prepare("SELECT * FROM `js` WHERE `id` = :id");
			$query->bindParam(':id', $id);
			if( $query->execute() ){
				$SS = $query->fetchObject("javascript");
				if( is_object( $SS ) ){
					$SS->setConnection( $this->connection );
				}
				return $SS;
			}
		}
	}
?>