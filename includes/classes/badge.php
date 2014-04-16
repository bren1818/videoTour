<?php
	class Badge{
		private $id;
		private $projectID;
		private $note;
		private $path;
		private $connection;
		
		function __construct($dbc=null) {
			$this->connection = $dbc;
		}
	
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setProjectID($projectID) { $this->projectID = $projectID; }
		function getProjectID() { return $this->projectID; }
		function setNote($note) { $this->note = $note; }
		function getNote() { return $this->note; }
		function setPath($path) { $this->path = $path; }
		function getPath() { return $this->path; }
		function setConnection( $conn ){ $this->connection = $conn; }
		
		function load( $id ){
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `badges` WHERE `id` = :badgeID");
				$query->bindParam(':badgeID', $id);
				$query->execute();
				$badge = $query->fetchObject("Badge");
				if( is_object( $badge ) ){
					$badge->setConnection( $this->connection );
				}
				return  $badge;
			}
		}
		
		function delete(){
			if( $this->getId() != "" ){
				
				$path = $this->getPath();
				$deleted = 0;
				if( unlink( '..'.$path ) ){
					$deleted = 1;
				}
				if( $this->connection ){
					$query = $this->connection->prepare("DELETE FROM `badges` WHERE `id` = :badgeID");
					$id = $this->getId();
					$query->bindParam(':badgeID', $id);
					if( $query->execute() && $deleted ){
						return 1;
					}
				}
			}else{
				return 0;
			}
		}
		
		
		function save(){
			//check for id, if empty save else update
			$projectID = $this->getProjectID();
			$note = $this->getNote();
			$path = $this->getPath();
			
			if( $this->getId() == "" ){
				//new Badge
				$query = $this->connection->prepare("INSERT INTO `badges` (`id`, `projectID`, `note`, `path`) VALUES (NULL, :projectID, :note, :path)");
				
				$query->bindParam(':projectID', $projectID  );
				$query->bindParam(':note', $note );
				$query->bindParam(':path', $path );
				
				if( $query->execute() ){
					$this->setId( $this->connection->lastInsertId() );
					return $this->getId(); 
				}else{
					return -1;
				}
			}else{
				//Update

				$badgeID = $this->getId();
				
				$query = $this->connection->prepare("UPDATE `badges` SET `path` = :path, `note` = :note WHERE `badges`.`id` = :badgeID;");
				
				$query->bindParam(':path', $path );
				$query->bindParam(':note', $note );
				$query->bindParam(':badgeID', $badgeID  );
				
				if( $query->execute() ){
					return $this->getId(); 
				}else{
					return -1;
				}
				
				
			}
			
		}
		
		function getList($projectID=null){
			if( $this->connection ){
				$badges = array();
				if( $projectID == null ){
					//list all Badges
					$query = $this->connection->prepare("SELECT * FROM `badges`");
					$query->execute();
					while( $result = $query->fetchObject("Badge") ){
						$badges[] = $result;
					}
				}else{
					//fetch by Badges by project ID
					$query = $this->connection->prepare("SELECT * FROM `badges` WHERE `projectID` = :projectID");
					$query->bindParam(':projectID', $projectID);
					$query->execute();
					
				
					while( $result = $query->fetchObject("Badge") ){
						$badges[] = $result;
					}
					
				}
				return $badges; 
			}else{
				return array();
			}
		}
		
	
	
	}

?>