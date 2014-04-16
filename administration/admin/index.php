<?php
	require_once("../../includes/includes.php");
	pageHeader();
	
	$adminSession = new adminSession();
	$userID = $adminSession->getCurrentUserID();
	$admin = new administrator( getConnection() );
	$admin = $admin->load( $userID );
	
	
	if( $admin->getType() != 1 ){
		?>
		<h1>Access Denied</h1>
		<?php
	}else{
?>
	<script type="text/javascript">
		function killSession(id){
			console.log("Kill session: " + id );
			
			$(function(){
				var r =  getAjaxHandlerResponse(id, "session", "destroyRemoteSession");
				console.log( r );
			});
			
		}
	</script>

	<h1>Super Admin</h1>
	
	<p><a href="<?php echo fixedPath; ?>/administratio/user/update?new">Add User</a></p>
	
	<p>Users Online</p>
	<?php
		$connection = getConnection();
		$t = time();
		$t = $t - 300;
		//last login is  > than current time -300
		$query = $connection->prepare("SELECT `username`, `id`, `email`, `enabled`, `last_login`, `last_session`, `type` FROM `administrators` WHERE `last_login` > :time;");
		$query->bindParam(':time', $t  );
		if( $query->execute() ){
			$res = $query->fetchAll();
			?>
			<table class="tablesorter">
				<thead> 
					<tr> 
						<th>Username</th>
						<th>id</th>
						<th>email</th> 
						<th>last online</th>
						<th>kill session</th>
						<th>type</th>
					</tr> 
				</thead>
				<tbody>
				<?php
				foreach( $res as $row ){
					echo '<tr><td>'.$row['username'].'</td><td>'.$row['id'].'</td><td>'.$row['email'].'</td><td>'.date("Y-m-d H:i:s",$row['last_login']).'</td><td><a onClick="killSession(\''.$row['last_session'].'\')">Boot em</a></td><td>'.$row['type'].'</td></tr>';
				}
				?>
				</tbody>
			</table>
			<?php
		}
	?>
	
	
	
	<p>Users Offline</p>
		<?php
		$connection = getConnection();
		$t = time();
		$t = $t - 300;
		//last login is  > than current time -300
		$query = $connection->prepare("SELECT `username`, `id`, `email`, `enabled`, `last_login`, `last_session`, `type` FROM `administrators` WHERE `last_login` < :time;");
		$query->bindParam(':time', $t  );
		if( $query->execute() ){
			$res = $query->fetchAll();
			?>
			<table class="tablesorter">
				<thead> 
					<tr> 
						<th>Username</th>
						<th>id</th>
						<th>email</th> 
						<th>last online</th>
						<th>kill session</th>
						<th>type</th>
					</tr> 
				</thead>
				<tbody>
				<?php
				foreach( $res as $row ){
					echo '<tr><td>'.$row['username'].'</td><td>'.$row['id'].'</td><td>'.$row['email'].'</td><td>'.date("Y-m-d H:i:s",$row['last_login']).'</td><td><a onClick="killSession(\''.$row['last_session'].'\')">Boot em</a></td><td>'.$row['type'].'</td></tr>';
				}
				?>
				</tbody>
			</table>
			<?php
		}
	?>
	
	
	
	<?php footerMenu($projID); ?>
<?php
	}
	pageFooter();
?>