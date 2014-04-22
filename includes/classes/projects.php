<?php
	class Projects{
		private $id;
		private $startingSegmentID;
		private $title;
		private $connection;
		private $active;
		
		private $showBadge;
		private $showCount;
		private $hasForm;
		private $formURL;
		private $redirect;
		private $redirectURL;
		
		private $badgeMode;
		
		private $showPoster;
		private $posterFile;
		
		
		
		function __construct($dbc=null) {
			$this->connection = $dbc;
		}
		
		function setConnection($con){
			$this->connection = $con;
		}
		
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setStartingSegmentID($startingSegmentID) { $this->startingSegmentID = $startingSegmentID; }
		function getStartingSegmentID() { return $this->startingSegmentID; }
		function setTitle($title) { $this->title = $title; }
		function getTitle() { return $this->title; }
		
		function setActive($active) { $this->active = $active; }
		function getActive() { return $this->active; }
		function isActive(){ return $this->getActive(); } //alias
		
		
		function setShowBadge($showBadge) { $this->showBadge = $showBadge; }
		function getShowBadge(){ return $this->showBadge; } 
		function showBadge(){ return $this->getShowBadge(); } //alias
		
		
		function setBadgeMode($badgeMode) { $this->badgeMode = $badgeMode; }
		function getBadgeMode(){ return $this->badgeMode; } 
		
		
		
		function setShowPoster($showPoster) { $this->showPoster = $showPoster; }
		function getShowPoster(){ return $this->showPoster; } 
		function setPosterFile($posterFile) { $this->posterFile = $posterFile; }
		function getPosterFile(){ return $this->posterFile; } 
		
		
		
		

		function setShowCount($showCount) { $this->showCount = $showCount; }
		function getShowCount(){ return $this->showCount; } 
		function showCount(){ return $this->showCount(); } //alias	
		
		function setHasForm($hasForm) { $this->hasForm = $hasForm; }
		function getHasForm() { return $this->hasForm; }
		function setFormURL($formURL) { $this->formURL = $formURL; }
		function getFormURL() { return $this->formURL; }
		function setRedirect($redirect) { $this->redirect = $redirect; }
		function getRedirect() { return $this->redirect; }
		function setRedirectURL($redirectURL) { $this->redirectURL = $redirectURL; }
		function getRedirectURL() { return $this->redirectURL; }


		function listProjects(){
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `projects`");
				$query->execute();
				$projects = array();
				$result = $query->fetchAll(PDO::FETCH_CLASS, "Projects");
				foreach( $result as $Obj ){
					$projects[] = $Obj;
				}
				
				return $projects;
				
			}else{
				return array();
			}
		}
		
		function save(){
			if( $this->getId() != "" ){
				//update
				$pid = $this->getId();
				$active = $this->isActive();
				$startingSegmentID = $this->getStartingSegmentID();
				$title = $this->getTitle();
				
				$showCount = $this->getShowCount();
				$showBadge = $this->getShowBadge();
				$hasForm = $this->getHasForm();
				$formURL = $this->getFormURL();
				$redirect = $this->getRedirect();
				$redirectURL = $this->getRedirectURL();
				$badgeMode = $this->getBadgeMode();
				
				$showPoster = $this->getShowPoster();
				$posterFile = $this->getPosterFile();
				
				
				
				$query = $this->connection->prepare("UPDATE `projects` SET `title` = :title, `startingSegmentID` = :ssid, `active` = :active, `showBadge` = :showBadge, `badgeMode` = :badgeMode, `showCount` = :showCount, `hasForm` = :hasForm, `formURL` = :formURL, `redirect` = :redirect, `redirectURL`= :redirectURL, `showPoster` = :showPoster, `posterFile` = :posterFile WHERE `projects`.`id` = :id;");
				
				
				$query->bindParam(':title', $title);
				$query->bindParam(':ssid', $startingSegmentID);
				$query->bindParam(':active', $active);
				
				$query->bindParam(':showCount', $showCount);
				$query->bindParam(':showBadge', $showBadge);
				$query->bindParam(':hasForm', $hasForm);
				$query->bindParam(':formURL', $formURL);
				$query->bindParam(':redirect', $redirect);
				$query->bindParam(':redirectURL', $redirectURL);
				$query->bindParam(':badgeMode', $badgeMode);
				$query->bindParam(':showPoster', $showPoster);
				$query->bindParam(':posterFile', $posterFile);
				
				
				
				
				$query->bindParam(':id', $pid);
				if( $query->execute() ){
					return 1;
				}else{
					return 0;
				}

			}else{
				//insert
				//return pid;
				$title = $this->getTitle();
				$query = $this->connection->prepare("INSERT INTO `projects` (`id`, `title`, `startingSegmentID`, `active`, `showBadge`, `showCount`, `hasForm`, `formURL`, `redirect`, `redirectURL`, `badgeMode`, `showPoster`, `posterFile` ) VALUES (NULL, :title , NULL, '0', 0, 1, 0, '', 0, '', 0,0,'' );");
				$query->bindParam(':title', $title);
				if( $query->execute() ){
					$this->setId( $this->connection->lastInsertId() );
					return $this->getId();
				}else{
					return -1;
				}
				
			}
		}
		
		function load($id){
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `projects` WHERE `id` = :id");
				$query->bindParam(':id', $id);
				$query->execute();
				$proj = $query->fetchObject("Projects");
				if( is_object( $proj ) ){
				$proj->setConnection( $this->connection );
				}
				return $proj;
			}else{
				return new Projects();
			}
		}
		
	}
?>