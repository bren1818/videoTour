<?php
	class entry{
		private $entryID;
		private $projectID;
		private $visitorID;
		private $firstName;
		private $lastName;
		private $email;
		private $telephone;
		private $twitter;
		private $other;
		private $other_reason;
		private $timestamp;
		private $connection;
		
		function __construct($db = null) {
			if( $db ){
				$this->connection = $db;
			}
		}
		
		function setConnection($conn){
			$this->connection = $conn;
		}
		
		function save(){
			if( $this->connection ) {
				$projectID = $this->getProjectID();
				$visitorID = $this->getVisitorID();
				$firstName = $this->getFirstName();
				$lastName = $this->getLastName();
				$email = $this->getEmail();
				$phone = $this->getTelephone();
				$twitter = $this->getTwitter();
				$offer = $this->getOther();
				$other = $this->getOther_reason();
			
				$query = $this->connection->prepare("INSERT INTO `form_entry` (`entryID`, `projectID`, `visitorID`, `firstName`, `lastName`, `email`, `telephone`, `twitter`, `other`, `other_reason`, `timestamp`) VALUES (NULL, :projectID, :visitorID, :firstName, :lastName, :email, :phone, :twitter, :offer, :other, CURRENT_TIMESTAMP);");
				$query->bindParam(':projectID', $projectID);
				$query->bindParam(':visitorID', $visitorID);
				$query->bindParam(':firstName', $firstName);
				$query->bindParam(':lastName', $lastName);
				$query->bindParam(':email', $email);
				$query->bindParam(':phone', $phone);
				$query->bindParam(':twitter', $twitter);
				$query->bindParam(':offer', $offer);
				$query->bindParam(':other', $other);
			
			
				if( $query->execute() ){
					$entryID = $this->connection->lastInsertId();
					$this->setEntryID( $entryID );
					return $entryID;
				
				}else{
					return -1;
				}
			}else{
				return 0;
			}
		}
		
		function load($id){
			if( $this->connection ) {
				if( $id != "" ){
					$query = $this->connection->prepare("SELECT * FROM `form_entry` WHERE `entryID` = :ID");
					$query->bindParam(':ID', $id);
					if( $query->execute() ){
						$entry = $query->fetchObject("entry");
						if( is_object( $entry ) ){
							$entry->setConnection( $this->connection );
						}
						return $entry;
					}
				}
			}
		}
		
		function getList($projectID = null){
			if( $this->connection ){
					
				if( $projectID == null ){
					//list all Clips
					$query = $this->connection->prepare("SELECT * FROM `form_entry`");
					$query->execute();
					while( $result = $query->fetchObject("entry") ){
						$events[] = $result;
					}
				}else{
					//fetch by project id
					$query = $this->connection->prepare("SELECT * FROM `form_entry` WHERE `projectID` = :projectID");
					$query->bindParam(':projectID', $projectID);
					$query->execute();
					if( $query->rowCount() > 1 ){
						while( $result = $query->fetchObject("entry") ){
							$events[] = $result;
						}
					}else{
						$events =  $query->fetchObject("entry");
					}
				}
				return $events;
			}else{
				return array();
			}
		}
		
	
		function setEntryID($entryID) { $this->entryID = $entryID; }
		function getEntryID() { return $this->entryID; }
		function setProjectID($projectID) { $this->projectID = $projectID; }
		function getProjectID() { return $this->projectID; }
		function setVisitorID($visitorID) { $this->visitorID = $visitorID; }
		function getVisitorID() { return $this->visitorID; }
		function setFirstName($firstName) { $this->firstName = $firstName; }
		function getFirstName() { return $this->firstName; }
		function setLastName($lastName) { $this->lastName = $lastName; }
		function getLastName() { return $this->lastName; }
		function setEmail($email) { $this->email = $email; }
		function getEmail() { return $this->email; }
		function setTelephone($telephone) { $this->telephone = $telephone; }
		function getTelephone() { return $this->telephone; }
		function setTwitter($twitter) { $this->twitter = $twitter; }
		function getTwitter() { return $this->twitter; }
		function setOther($other) { $this->other = $other; }
		function getOther() { return $this->other; }
		function setOther_reason($other_reason) { $this->other_reason = $other_reason; }
		function getOther_reason() { return $this->other_reason; }
		function setTimestamp($timestamp) { $this->timestamp = $timestamp; }
		function getTimestamp() { return $this->timestamp; }
	}

?>