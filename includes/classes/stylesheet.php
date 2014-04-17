<?php
	class stylesheet{
		private $id;
		private $projectID;
		private $CSS;
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
		function setCSS($CSS) { $this->CSS = $CSS; }
		function getCSS() { return $this->CSS; }

		function save(){
			$id = $this->getId();
			$projectID = $this->getProjectID();
			$css = $this->getCSS();
			
			if( $id != "" ){
				//update
				$query = $this->connection->prepare("UPDATE `css` SET `CSS` = :css WHERE  `css`.`id` = :id;");
				$query->bindParam(':css', $css);
				$query->bindParam(':id', $id);
				if( $query->execute() ){
					return $this->getId();
				}else{
					return 0;
				}
			}else{
				//insert
				$query = $this->connection->prepare("INSERT INTO `css` (`id`, `projectID`, `CSS`) VALUES (NULL, :projectID, '');");
				$query->bindParam(':projectID', $projectID);
				if( $query->execute() ){
					$this->setId( $this->connection->lastInsertId() );
					return $this->getId();
				}else{
					return 0;
				}
			}
		}
		
		function loadByProjectID($id = null){
			$query = $this->connection->prepare("SELECT * FROM `css` WHERE `projectID` = :projectID");
			$query->bindParam(':projectID', $id);
			if( $query->execute() ){
				$SS = $query->fetchObject("stylesheet");
				if( is_object( $SS ) ){
					$SS->setConnection( $this->connection );
				}
				return $SS;
			}
		}
		
		function load($id = null){
			$query = $this->connection->prepare("SELECT * FROM `css` WHERE `id` = :id");
			$query->bindParam(':id', $id);
			if( $query->execute() ){
				$SS = $query->fetchObject("stylesheet");
				if( is_object( $SS ) ){
					$SS->setConnection( $this->connection );
				}
				return $SS;
			}
		}
	}
?>