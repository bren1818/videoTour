<?php
	class adminSession{
		private $sessionID;
		private $expiresAt;
		private $expiresIn;
		private $expired;
		private $currentUser;
		private $currentUserID;
		private $startTime;
		private $currentTime;
		
		private $loggedIn;
		
		private $duration;
		private $maxLength;


		public function check(){
			$this->load();
			if( $this->expired == 1 ){
				return 1;
			}else{
				return 0;
			}
		}
		
		function __construct(){
			if (session_id() == ''){ session_start(); }
			$this->maxLength = 600; //default
			$this->load();
		}
	
	
		function getDuration(){
			$t = $this->getCurrentTime();
			$start = $this->getStartTime();
			
			return ($t - $start);
		}
		
		function setMaxLength($maxLength){
			$this->maxLength = $maxLength;
		}
		
		function getMaxLength(){
			return $this->maxLength;
		}
		
		
		function getSessionID() { return session_id(); }
		
		
		function setExpiresIn($expiresIn) { $this->expiresIn = $expiresIn; }
		
		
		function getExpiresIn() {
			$t = $this->getCurrentTime();
			$expiresAt = $this->getExpiresAt();
			return ( $expiresAt - $t);
		}
		
		
		function setExpired($expired) { $this->expired = $expired; }
		function getExpired() { 
			if( $this->getExpiresIn() <= 0 ){
				return 1;
			}else{
				return 0;
			}
		}
		function setCurrentUser($currentUser) { $this->currentUser = $currentUser; }
		function getCurrentUser() { return $this->currentUser; }
	
		function setCurrentUserID($currentUserID) { $this->currentUserID = $currentUserID; }
		function getCurrentUserID() { return $this->currentUserID; }	
		
		function setStartTime($startTime) { $this->startTime = $startTime; }
		
		function getStartTime() { 
			$t = $this->getCurrentTime();
			$startTime = (isset($_SESSION['startTime']) && $_SESSION['startTime'] != "" ) ?  $_SESSION['startTime'] : $t;
			return $startTime; 
		}
		
		function setCurrentTime() { $this->currentTime = time(); 
		
		}
		function getCurrentTime() { 
			$t = time();
			return $t; 
		}
		function setExpiresAt($expiresAt) { $this->expiresAt = $expiresAt; }
		
		
		
		function getExpiresAt() { 
			//return $this->expiresAt; 
			$t = $this->getCurrentTime();
			$expiresAt = (isset($_SESSION['expiresAt']) && $_SESSION['expiresAt'] != "" ) ?  $_SESSION['expiresAt'] : $t;
			return $expiresAt;
		}

		public function save(){
			$_SESSION['sessionID'] = 		(($this->sessionID != "") ? $this->sessionID : "");
			$_SESSION['expiresAt'] = 		(($this->expiresAt != "") ? $this->expiresAt : time() );
			$_SESSION['expiresIn'] = 		(($this->expiresIn != "") ? $this->expiresIn : 0);
			$_SESSION['expired'] = 			(($this->expired != "") ? $this->expired : 1);
			$_SESSION['currentUser'] = 		(($this->currentUser != "") ? $this->currentUser : "");
			$_SESSION['currentUserID'] =	(($this->currentUserID != "") ? $this->currentUserID : 0);
			$_SESSION['startTime'] = 		(($this->startTime != "") ? $this->startTime : time() );
			$_SESSION['currentTime'] = 		$this->currentTime;
			$_SESSION['loggedIn'] = 		(($this->loggedIn != "") ? $this->loggedIn : 0  );
			$_SESSION['duration'] = 		(($this->duration != "") ? $this->duration : 0  );
			
		}
		
		
		public function makeExpired(){
			$this->expired = 1;
			$this->expiresIn = 0;
			$this->loggedIn	= 0;
			$this->save();
		}
		
		public function load(){
			$t = time();
			$this->sessionID		=  $this->getSessionID(); //session_id();
			$this->expiresAt		=  $this->getExpiresAt(); //(isset($_SESSION['expiresAt']) && $_SESSION['expiresAt'] != "" ) ?  $_SESSION['expiresAt'] : $t;
			$this->expiresIn		=  $this->getExpiresIn(); //(isset($_SESSION['expiresIn']) && $_SESSION['expiresIn'] != "" ) ?  $_SESSION['expiresIn'] : 0; 
			$this->expired			=  $this->getExpired(); //(isset($_SESSION['expired']) && $_SESSION['expired'] != "" ) ?  $_SESSION['expired'] : 1; 
			$this->currentUser		=  (isset($_SESSION['currentUser']) && $_SESSION['currentUser'] != "" ) ?  $_SESSION['currentUser'] : "";  
			$this->currentUserID	=  (isset($_SESSION['currentUserID']) && $_SESSION['currentUserID'] != "" ) ?  $_SESSION['currentUserID'] : "";
			$this->startTime		=  $this->getStartTime(); //(isset($_SESSION['startTime']) && $_SESSION['startTime'] != "" ) ?  $_SESSION['startTime'] : $t;
			$this->currentTime		=  $t;
			$this->duration 		=  $this->getDuration();
			
			
			if( $this->expired ){
				$this->makeExpired();
			}else{
				$expiresAt = $this->expiresAt;
				$t = time();
				if( ($expiresAt - $t) > 0 ){
					$this->expiresIn = ($expiresAt - $t);
					$this->expired = 0;
					$this->loggedIn	= true;
				}else{
					$this->makeExpired();
				}
			}
			$this->save();
		}
		
		public function renew(){
			$this->expired = 0;
			$this->expiresAt = $this->getCurrentTime() + $this->getMaxLength();
			$this->expiresIn = (  $this->expiresAt - $this->getCurrentTime() ) ;
			$this->loggedIn = 1;
			$this->setCurrentTime();
			$this->save();
		}

		public function destroy(){
			$_SESSION = array();
			session_destroy();
			session_start();
		}
		
		public function killRemoteSession($id){
			// 1. commit session if it's started.
			if (session_id()) {
				session_commit();
			}

			// 2. store current session id
			//session_start();
			$current_session_id = session_id();
			session_commit();

			// 3. hijack then destroy session specified.
			session_id($id);
			session_start();
			session_destroy();
			session_commit();

			// 4. restore current session id. If don't restore it, your current session will refer to the session you just destroyed!
			session_id($current_session_id);
			session_start();
			session_commit();
		}
	
	
	}
?>