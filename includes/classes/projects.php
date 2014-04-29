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
		private $mobilePopUpButtonText;
		private $mobilePopUpTitle;
		private $mobilePopUpText;
		
		private $ShowFormAlertText;
		private $ShowFormAlertTitle;
		private $ShowFormAlertButtonText;
		private $RedirectAlertText;
		private $RedirectAlertTitle;
		private $RedirectButtonText;
		private $FinishAlertText;
		private $FinishAlertTitle;
		private $FinishAlertButtonText;
		private $RepeatAlertText;
		private $RepeatAlertTitle;
		private $RepeatAlertButtonText;
		
		
		
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
		function setMobilePopUpButtonText($mobilePopUpButtonText) { $this->mobilePopUpButtonText = $mobilePopUpButtonText; }
		function getMobilePopUpButtonText() { return $this->mobilePopUpButtonText; }
		function setMobilePopUpTitle($mobilePopUpTitle) { $this->mobilePopUpTitle = $mobilePopUpTitle; }
		function getMobilePopUpTitle() { return $this->mobilePopUpTitle; }
		function setMobilePopUpText($mobilePopUpText) { $this->mobilePopUpText = $mobilePopUpText; }
		function getMobilePopUpText() { return $this->mobilePopUpText; }

		function setShowFormAlertText($ShowFormAlertText) { $this->ShowFormAlertText = $ShowFormAlertText; }
		function getShowFormAlertText() { return $this->ShowFormAlertText; }
		function setShowFormAlertTitle($ShowFormAlertTitle) { $this->ShowFormAlertTitle = $ShowFormAlertTitle; }
		function getShowFormAlertTitle() { return $this->ShowFormAlertTitle; }
		function setShowFormAlertButtonText($ShowFormAlertButtonText) { $this->ShowFormAlertButtonText = $ShowFormAlertButtonText; }
		function getShowFormAlertButtonText() { return $this->ShowFormAlertButtonText; }
		function setRedirectAlertText($RedirectAlertText) { $this->RedirectAlertText = $RedirectAlertText; }
		function getRedirectAlertText() { return $this->RedirectAlertText; }
		function setRedirectAlertTitle($RedirectAlertTitle) { $this->RedirectAlertTitle = $RedirectAlertTitle; }
		function getRedirectAlertTitle() { return $this->RedirectAlertTitle; }
		function setRedirectButtonText($RedirectButtonText) { $this->RedirectButtonText = $RedirectButtonText; }
		function getRedirectButtonText() { return $this->RedirectButtonText; }
		function setFinishAlertText($FinishAlertText) { $this->FinishAlertText = $FinishAlertText; }
		function getFinishAlertText() { return $this->FinishAlertText; }
		function setFinishAlertTitle($FinishAlertTitle) { $this->FinishAlertTitle = $FinishAlertTitle; }
		function getFinishAlertTitle() { return $this->FinishAlertTitle; }
		function setFinishAlertButtonText($FinishAlertButtonText) { $this->FinishAlertButtonText = $FinishAlertButtonText; }
		function getFinishAlertButtonText() { return $this->FinishAlertButtonText; }
		function setRepeatAlertText($RepeatAlertText) { $this->RepeatAlertText = $RepeatAlertText; }
		function getRepeatAlertText() { return $this->RepeatAlertText; }
		function setRepeatAlertTitle($RepeatAlertTitle) { $this->RepeatAlertTitle = $RepeatAlertTitle; }
		function getRepeatAlertTitle() { return $this->RepeatAlertTitle; }
		function setRepeatAlertButtonText($RepeatAlertButtonText) { $this->RepeatAlertButtonText = $RepeatAlertButtonText; }
		function getRepeatAlertButtonText() { return $this->RepeatAlertButtonText; }

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
				
				$mobilePopUpButtonText = $this->getMobilePopUpButtonText();
				$mobilePopUpTitle = $this->getMobilePopUpTitle();
				$mobilePopUpText = $this->getMobilePopUpText();
				
				//Popup before showing Form
				$ShowFormAlertText 			= $this->getShowFormAlertText();
				$ShowFormAlertTitle 		= $this->getShowFormAlertTitle();
				$ShowFormAlertButtonText 	= $this->getShowFormAlertButtonText();
				
				//Popup before re-directing
				$RedirectAlertText 			= $this->getRedirectAlertText();
				$RedirectAlertTitle 		= $this->getRedirectAlertTitle();
				$RedirectButtonText 		= $this->getRedirectButtonText();
				
				//If no redirect - finished
				$FinishAlertText 			= $this->getFinishAlertText();
				$FinishAlertTitle 			= $this->getFinishAlertTitle();
				$FinishAlertButtonText 		= $this->getFinishAlertButtonText();
				
				//Repeat Visitors who entered contest already and no redirect
				$RepeatAlertText 			= $this->getRepeatAlertText();
				$RepeatAlertTitle 			= $this->getRepeatAlertTitle();
				$RepeatAlertButtonText 		= $this->getRepeatAlertButtonText();
				
				
				
				
				
				
				$query = $this->connection->prepare("UPDATE `projects` SET `title` = :title, `startingSegmentID` = :ssid, `active` = :active, `showBadge` = :showBadge, `badgeMode` = :badgeMode, `showCount` = :showCount, `hasForm` = :hasForm, `formURL` = :formURL, `redirect` = :redirect, `redirectURL`= :redirectURL, `showPoster` = :showPoster, `posterFile` = :posterFile, `mobilePopUpButtonText` = :mobilePopUpButtonText,`mobilePopUpTitle`=:mobilePopUpTitle, `mobilePopUpText`=:mobilePopUpText, `ShowFormAlertText`=:ShowFormAlertText,`ShowFormAlertTitle`=:ShowFormAlertTitle,`ShowFormAlertButtonText`=:ShowFormAlertButtonText,`RedirectAlertText`=:RedirectAlertText,`RedirectAlertTitle`=:RedirectAlertTitle,`RedirectButtonText`=:RedirectButtonText, `FinishAlertText`=:FinishAlertText,`FinishAlertTitle`=:FinishAlertTitle,`FinishAlertButtonText`=:FinishAlertButtonText,`RepeatAlertText`=:RepeatAlertText,`RepeatAlertTitle`=:RepeatAlertTitle,`RepeatAlertButtonText`=:RepeatAlertButtonText WHERE `projects`.`id` = :id;");
				
				
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
				
				$query->bindParam(':mobilePopUpButtonText', $mobilePopUpButtonText);
				$query->bindParam(':mobilePopUpTitle', $mobilePopUpTitle);
				$query->bindParam(':mobilePopUpText', $mobilePopUpText);
				
				
				//Popup before showing Form
				$query->bindParam(':ShowFormAlertText', $ShowFormAlertText);
				$query->bindParam(':ShowFormAlertTitle', $ShowFormAlertTitle);
				$query->bindParam(':ShowFormAlertButtonText', $ShowFormAlertButtonText);
				//Popup before re-directing
				$query->bindParam(':RedirectAlertText', $RedirectAlertText);
				$query->bindParam(':RedirectAlertTitle', $RedirectAlertTitle);
				$query->bindParam(':RedirectButtonText', $RedirectButtonText);
				//If no redirect - finished
				$query->bindParam(':FinishAlertText', $FinishAlertText);
				$query->bindParam(':FinishAlertTitle', $FinishAlertTitle);
				$query->bindParam(':FinishAlertButtonText', $FinishAlertButtonText);
				//Repeat Visitors who entered contest already and no redirect
				$query->bindParam(':RepeatAlertText', $RepeatAlertText);
				$query->bindParam(':RepeatAlertTitle', $RepeatAlertTitle);
				$query->bindParam(':RepeatAlertButtonText', $RepeatAlertButtonText);
				
				
				
				
				
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
				$query = $this->connection->prepare("INSERT INTO `projects` (`id`, `title`, `startingSegmentID`, `active`, `showBadge`, `showCount`, `hasForm`, `formURL`, `redirect`, `redirectURL`, `badgeMode`, `showPoster`, `posterFile`, `mobilePopUpButtonText`,`mobilePopUpTitle`,`mobilePopUpText`, 	`ShowFormAlertText`,`ShowFormAlertTitle`,`ShowFormAlertButtonText`,`RedirectAlertText`,`RedirectAlertTitle`,`RedirectButtonText`,`FinishAlertText`,`FinishAlertTitle`,`FinishAlertButtonText`,`RepeatAlertText`,`RepeatAlertTitle`,`RepeatAlertButtonText`) VALUES (NULL, :title , NULL, '0', 0, 1, 0, '', 0, '', 0,0,'', 'OK', 'Welcome to the Mobile Tour', 'Click the play button between scenes to continue',				'Please Complete this form','Enter for a chance to win','Enter Contest','Thank you for playing. You will now be redirected','Thanks for playing','OK',				'Thank you for playing','Thank you for playing','Close','Looks as though you have already entered the contest. Thanks for playing!','Thanks for playing','Close');");
				
				
				
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