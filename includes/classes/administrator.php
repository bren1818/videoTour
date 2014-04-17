<?php
	class administrator{
		private $id;
		private $username;
		private $password;
		private $salt;
		private $email;
		private $enabled;
		private $connection;
		
		private $type;
		private $last_login;
		private $last_session;
		private $assigned_projects;
		
		function setConnection( $conn ){
			$this->connection = $conn;
		}
		
		
		function __construct($dbc=null) {
			$this->connection = $dbc;
		}
		
		function verifyLogin($username, $password){
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT `username`, `salt`, `password`, `id` FROM `administrators` WHERE `username` = :USERNAME");
				$query->bindParam(':USERNAME', $username  );
				if( $query->execute() ){
					$result = $query->fetch();
					$salt = $result['salt'];
					$storedpassword = $result['password'];
					$id = $result['id'];
					
					if( crypt( $password, $salt) == $storedpassword ){
						return $id;
					}else{
						return 0;
					}
				}
			}else{
				return 0;
			}
		}
		
		function save(){
			if( $this->connection ){
				$email = $this->getEmail();
				$enabled = $this->getEnabled();
				$lastlogin = $this->getLast_login();
				$lastSession = $this->getLast_session();
				$type = $this->getType();
				$id = $this->id;
				$assigned_projects = $this->getAssigned_projects();
			
				if( $this->id != "" ){
				
					$query = $this->connection->prepare("UPDATE `administrators` SET  `email` = :email, `enabled` = :enabled, `last_login` = :lastlogin, `last_session` = :lastSession, `type` = :type, `assigned_projects` = :projects WHERE `administrators`.`id` = :id;");
					$query->bindParam(':email', $email);
					$query->bindParam(':enabled', $enabled);
					$query->bindParam(':lastlogin', $lastlogin);
					$query->bindParam(':lastSession', $lastSession);
					$query->bindParam(':type', $type);
					$query->bindParam(':projects', $assigned_projects);
					$query->bindParam(':id', $id);
					
					if( $query->execute() ){
						return $id;
					}else{
						return 0;
					}
				}else{
					//insert
					$query = $this->connection->prepare("INSERT INTO `administrators` (`id`, `username`, `password`, `salt`, `email`, `enabled`, `last_login`, `last_session`, `type`, `assigned_projects`) VALUES (NULL, :username, '', '', :email, :enabled, NULL, NULL, :type, :projects);");
					$username = $this->getUsername();
					
					$query->bindParam(':username', $username);
					$query->bindParam(':email', $email);
					$query->bindParam(':enabled', $enabled);
					$query->bindParam(':type', $type);
					$query->bindParam(':projects', $assigned_projects);
					
					if( $query->execute() ){
						$this->setId( $this->connection->lastInsertId() );
						return $this->getId();
					}else{
						return 0;
					}		
				}
			}
		}
		
		function updatePassword( $newPassword ){
			if( $this->connection ){
				if( $this->id != "" ){
					$this->setSalt( $this->randomSalt() );
					$this->setPassword( $newPassword );
					$id = $this->getId();
					$salt = $this->getSalt();
					$pass = $this->getPassword();
					
					$query = $this->connection->prepare("UPDATE `administrators` SET `password` = :password, `salt` = :salt WHERE `administrators`.`id` = :id;");
					$query->bindParam(':password', $pass );
					$query->bindParam(':salt', $salt);
					$query->bindParam(':id', $id);
					
					if( $query->execute() ){
						return 1;
					}else{
						return 0;
					}
				}
			}
		}
		
		function upateActivity(){
			if( $this->connection ){
				if( $this->id != "" ){
					$t = time();
					$id = $this->id;
					$query = $this->connection->prepare("UPDATE `administrators` SET `last_login` = :lastlogin WHERE `administrators`.`id` = :id;");
					$query->bindParam(':lastlogin', $t);
					$query->bindParam(':id', $id);
					$query->execute();
				}
			}
		}
	
		
		function load($id){
			if( $this->connection ){
				if( $id != "" ){
					$query = $this->connection->prepare("SELECT * FROM `administrators` WHERE `id` = :id");
					$query->bindParam(':id', $id);
					if( $query->execute() ){
						$admin = $query->fetchObject("administrator");
					}
					
					if( is_object( $admin ) ){
						$admin->setConnection( $this->connection );
					}
					
					return $admin;
				}
			}
		}
		
		function userNameExists($username){
			if( $this->connection ){
				$query = $this->connection->prepare("SELECT * FROM `administrators` WHERE `username` = :username");
				$query->bindParam(':username', $username);
				if( $query->execute() ){
					return $query->rowCount();
				}
				
			}
		}
		
		function delete(){
			if( $this->connection ){
				if( $this->id != "" ){
					$id = $this->getId();
					$query = $this->connection->prepare("DELETE FROM `administrators` WHERE `id` = :id");
					$query->bindParam(':id', $id);
					if( $query->execute() ){
						return 1;
					}else{
						return 0;
					}
				}
			}
		}
		
		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setUsername($username) { $this->username = $username; }
		function getUsername() { return $this->username; }
		function setPassword($password) { 
			if( $this->salt == "" ){
				$this->salt = $this->randomSalt();
			}
			$this->password = crypt( $password, $this->salt); 
		}
		
		function getPassword() { return $this->password; }
		function setSalt($salt) { $this->salt = $salt; }
		function getSalt() { return $this->salt; }
		function randomSalt(){
			return uniqid(mt_rand(), true);
		}
		
		function setEmail($email) { $this->email = $email; }
		function getEmail() { return $this->email; }
		function setEnabled($enabled) { $this->enabled = $enabled; }
		function getEnabled() { return $this->enabled; }

		function setType($type) { $this->type = $type; }
		function getType() { return $this->type; }
		function setLast_login($last_login) { $this->last_login = $last_login; }
		function getLast_login() { return $this->last_login; }
		function setLast_session($last_session) { $this->last_session = $last_session; }
		function getLast_session() { return $this->last_session; }

		function setAssigned_projects($assigned_projects) { $this->assigned_projects = $assigned_projects; }
		function getAssigned_projects() { return ( $this->assigned_projects != null ) ? $this->assigned_projects : ""; }
		
		function getProjectsAsArray(){ return  (($this->getAssigned_projects() == "" ) ? array() : explode(',', $this->getAssigned_projects() ) ); }
	
	}
?>