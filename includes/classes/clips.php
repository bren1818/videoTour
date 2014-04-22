<?php

	class Clips{
		private $id;
		private $clipID;
		private $projectID;

		private $path;
		private $type;
		private $converted;
		private $connection;
		

		function __construct($dbc=null) {
			$this->connection = $dbc;
		}
		
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setProjectID($projectID) { $this->projectID = $projectID; }
		function getProjectID() { return $this->projectID; }
		
		function setClipID($clipID) { $this->clipID = $clipID; }
		function getClipID() { return $this->clipID; }
		
		function setPath($path) { $this->path = $path; }
		function getPath() { return $this->path; }
		
		function setType($type) { $this->type = $type; }
		function getType() { return $this->type; }
		
		function setConverted($converted) { $this->converted = $converted; }
		function getConverted() { return $this->converted; }

		function setConnection($conn){
			$this->connection = $conn;
		}
		
		function save(){
			$projectID = $this->getProjectID();
			$clipID = $this->getClipID();
			$path = $this->getPath();
			$type = $this->getType();
			$converted = $this->getConverted();

			if( $this->getId() == "" ){

				//new Clip
				$query = $this->connection->prepare("INSERT INTO `clips` (`id`, `clipID`, `projectID`, `path`, `type`, `converted`) VALUES (NULL, :clipID, :projectID, :path, :type, :converted);");
				
				$query->bindParam(':clipID', $clipID  );
				$query->bindParam(':projectID', $projectID  );
				$query->bindParam(':path', $path  );
				$query->bindParam(':type', $type  );
				$query->bindParam(':converted', $converted  );

				if( $query->execute() ){
				
					$this->setId( $this->connection->lastInsertId() );
					return $this->getId(); 
				}else{
					return -1;
				}
			}else{
				//Update
				$id = $this->getId();
				$query = $this->connection->prepare("UPDATE `clips` SET `path` = :path, `type` = :type, `converted` = :converted WHERE `clips`.`id` = :id;");
				$query->bindParam(':path', $path  );
				$query->bindParam(':type', $type  );
				$query->bindParam(':converted', $converted  );
				$query->bindParam(':id', $id  );

				if( $query->execute() ){
					return $this->getId(); 
				}else{
					return -1;
				}
			}
			
		}
		
		function load( $id ){
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `clips` WHERE `id` = :clipsID");
				$query->bindParam(':clipsID', $id);
				$query->execute();
				$clip = $query->fetchObject("Clips");
				
				if( is_object( $clip ) ){
					$clip->setConnection( $this->connection );
				}
				return  $clip;
			}
		}
		
		function getVidPath($clipID, $type){
			if( $type > 3 || $type < 1 || $type == "" ){
				$type = 1;
			}
		
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT `path` FROM `clips` WHERE `clipID` = :clipsID AND `type` = :type");
				$query->bindParam(':clipsID', $clipID);
				$query->bindParam(':type', $type);
				if( $query->execute() ){
					$clipPath = $query->fetch();
					$clipPath = $clipPath["path"];
					return $clipPath;
				}else{
					return "";
				}
			}
		}
		
		function getProjectClips( $projectID ){
			$clips = array();
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `clips` WHERE `projectID` = :projectID");
				$query->bindParam(':projectID', $projectID);
				$query->execute();
				if( $query->rowCount() > 1 ){
					while( $result = $query->fetchObject("Clips") ){
						$clips[] = $result;
					}
				}else if( $query->rowCount() == 1 ){
					$clips[] = $query->fetchObject("Clips");
				}
			}
			return $clips;
		
		}
		
		
		function getList($clip=null){
			if( $this->connection ){
				
				if( $clip == null ){
					//list all Clips
					$query = $this->connection->prepare("SELECT * FROM `clips`");
					$query->execute();
					while( $result = $query->fetchObject("Clips") ){
						$clips[] = $result;
					}
				}else{
					//fetch by project id
					$query = $this->connection->prepare("SELECT * FROM `clips` WHERE `clipID` = :clipID");
					$query->bindParam(':clipID', $clip);
					$query->execute();
					if( $query->rowCount() > 1 ){
						while( $result = $query->fetchObject("Clips") ){
							$clips[] = $result;
						}
					}else{
						$clips =  $query->fetchObject("Clips");
					}
				}
				return $clips;
			}else{
				return array();
			}
		}
	}
?>