<?php
	class administrator{
		private $id;
		private $username;
		private $password;
		private $salt;
		private $email;
		private $enabled;
		private $connection;
		
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
			
				if( $this->id != "" ){
					//update
				}else{
					//insert
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


	
	}
?>