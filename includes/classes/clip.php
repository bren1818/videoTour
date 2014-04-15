<?php
	require_once("clips.php");

	class Clip{
		private $id;
		private $projectID;
		private $note;
		private $path;
		private $duration;
		private $name;
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
		function getPath($type= null) { 
			//return $this->path; 
			if( $this->getId() !="" ){
				if( $this->connection ){
					
					$clips = new Clips($this->connection);
					if( $type == null ){
						$type = 1; //default original
					}
				
					return $clips->getVidPath($this->getId(), $type);
				}
			}
		}
		function setDuration($duration) { $this->duration = $duration; }
		function getDuration() { return $this->duration; }
		function setName($name) { $this->name = $name; }
		function getName() { return $this->name; }
		function setConnection( $conn ){ $this->connection = $conn; }
		
		
		function getUsageCount(){
			if( $this->getId() != "" &&  $this->connection ){
				$clipId = $this->getId();

				
				$total = 0;
				$segments = 0;
				$decisions = 0;
				
				$query = $this->connection->prepare("SELECT COUNT(*) as `usage` FROM `segments` WHERE `clipID` = :clipID");
				$query->bindParam(':clipID', $clipId  );

				
				if( $query->execute()){
					$segments = $query->fetch();
					$segments = $segments['usage'];
				}
				
				$query = $this->connection->prepare("SELECT COUNT(*) as `usage` FROM `decisions` WHERE `clipID` = :clipID");
				$query->bindParam(':clipID', $clipId  );

				
				if( $query->execute()){
					$decisions = $query->fetch();
					$decisions = $decisions['usage'];
				}
				
				$total = $segments + $decisions;
			
				
				
				return array("total"=> $total, "segments"=>$segments, "decisions" => $decisions);
				
				
			
				
			}else{
				return 0;
			}
		}
		
		function save(){
			//check for id, if empty save else update
			$projectID = $this->getProjectID();
			$clipName = $this->getName();
			$clipNote = $this->getNote();
				
			if( $this->getId() == "" ){
				//new Clip
				$query = $this->connection->prepare("INSERT INTO `videotour`.`clip` (`id`, `projectID`, `name`, `note`) VALUES (NULL, :projectID, :clipName, :clipNote)");
				
				
				//$clipDuration = $this->getDuration();
				//$clipPath = $this->getPath();
				
				$query->bindParam(':projectID', $projectID  );
				$query->bindParam(':clipName', $clipName );
				$query->bindParam(':clipNote', $clipNote );
				$query->execute();
				
				$this->setId( $this->connection->lastInsertId() );
				
				return $this->getId(); 

			}else{
				//Update
				$clipID = $this->getId();
				
				$query = $this->connection->prepare("UPDATE `videotour`.`clip` SET `name` = :clipName, `note` = :clipNote WHERE `clip`.`id` = :clipID;");
				
				$query->bindParam(':clipName', $clipName );
				$query->bindParam(':clipNote', $clipNote );
				$query->bindParam(':clipID', $clipID  );
				
				if( $query->execute() ){
					return $this->getId(); 
				}else{
					return -1;
				}
				
			}
			
		}
		
		function load( $id ){
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `clip` WHERE `id` = :clipID");
				$query->bindParam(':clipID', $id);
				$query->execute();
				$clip = $query->fetchObject("Clip");
				if( is_object( $clip ) ){
				$clip->setConnection( $this->connection );
				}
				
				
				//$clip->setPath(  );
				
				return  $clip;
			}
		}
		
		function getList($projectID=null){
			if( $this->connection ){
				$clips = array();
				if( $projectID == null ){
					//list all Clips
					$query = $this->connection->prepare("SELECT * FROM `clip`");
					$query->execute();
					while( $result = $query->fetchObject("Clip") ){
						$clips[] = $result;
					}
				}else{
					//fetch by project id
					$query = $this->connection->prepare("SELECT * FROM `clip` WHERE `projectID` = :projectID");
					$query->bindParam(':projectID', $projectID);
					$query->execute();
					while( $result = $query->fetchObject("Clip") ){
						$clips[] = $result;
					}
				}
				return $clips;
			}else{
				return array();
			}
		}
		
		function getVersions($clipID=null){
			if( $this->connection ){
				$clips = array();
				if( $clipID == null ){
					//list all Clips
					$query = $this->connection->prepare("SELECT * FROM `clips`");
					$query->execute();
					while( $result = $query->fetchObject("Clips") ){ //will it throw a warning?
						$clips[] = $result;
					}
				}else{
					//fetch by project id
					$query = $this->connection->prepare("SELECT * FROM `clips` WHERE `clipID` = :clipID");
					$query->bindParam(':clipID', $clipID);
					$query->execute();
					while( $result = $query->fetchObject("Clips") ){
						$clips[] = $result; //will it throw a warning?
					}
				}
				return $clips;
			}else{
				return array();
			}
		}
		
			function delete(){
				if( $this->getId() != "" ){
					$clipID = $this->getId();
					$clips = $this->getVersions($clipID ); //array of clips
					$toDelete = array();
					$deletions = 0;
					foreach( $clips as $c ){
						$toDelete[] = $c->getPath();
						if( unlink( '../'.$c->getPath()) ){
							$deletions++;
						}
					}
					$deletedClip = 0;
					$deletedClips = 0;
					$deleted = 0;
					
					 $this->connection->beginTransaction();
					 try{
						$query = $this->connection->prepare("DELETE FROM `clips` where `clipID` = :clipID");
						$query->bindParam(':clipID', $clipID  );
						if( $query->execute() ){
							$deletedClips = 1;
						}
						
						$query = $this->connection->prepare("DELETE FROM `clip` where `id` = :clipID");
						$query->bindParam(':clipID', $clipID  );
						 if( $query->execute() ){
							$deletedClip = 1;
						}
						
						$this->connection->commit();
					}catch(PDOException $e) {
						// roll back transaction
						$this->connection->rollback();
						$deletedClip = 0;
						$deletedClips = 0;
					}
					//return $toDelete;
					
					if( $deletedClip == 1 && $deletedClips == 1 ){
						$deleted = 1;
					}
					
					return array("deleted" => 1, "clipsDeleted"=> $deletions);
			
				}else{
					return -1;
				}
			}
		
		
	}
?>























