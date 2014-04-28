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
	
	<p><a class="button wa" href="<?php echo fixedPath; ?>/administration/user/update?new=1"><i class="fa fa-users"></i> Add User</a> - <a class="button wa"  href="<?php echo fixedPath."/administration/user/update?id=".$userID;?>"><i class="fa fa-user"></i> Update Profile</a> </p>
	
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
						<th>Tools</th>
					</tr> 
				</thead>
				<tbody>
				<?php
				$updatePath = fixedPath."/administration/user/update?id=";
				foreach( $res as $row ){
					echo '<tr><td>'.$row['username'].'</td><td>'.$row['id'].'</td><td>'.$row['email'].'</td><td>'.date("Y-m-d H:i:s",$row['last_login']).'</td><td><a class="button wa" onClick="killSession(\''.$row['last_session'].'\')"><i class="fa fa-gavel"></i> Boot em</a></td><td>'.(($row['type'] == 1) ? "Super Admin" : "Admin").'</td><td><a class="button wa"  href="'.$updatePath.$row['id'].'"><i class="fa fa-pencil"></i> Edit</a> <a class="button wa"  href="'.fixedPath."/administration/user/delete?id=".$row['id'].'"><i class="fa fa-times-circle"></i> Delete</a></td></tr>';
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
		$query = $connection->prepare("SELECT `username`, `id`, `email`, `enabled`, `last_login`, `last_session`, `type` FROM `administrators` WHERE `last_login` < :time OR `last_login` IS NULL ;");
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
						<th>type</th>
						<th>tools</th>
					</tr> 
				</thead>
				<tbody>
				<?php
				$updatePath = fixedPath."/administration/user/update?id=";
				foreach( $res as $row ){
					echo '<tr><td>'.$row['username'].'</td><td>'.$row['id'].'</td><td>'.$row['email'].'</td><td>'.date("Y-m-d H:i:s",$row['last_login']).'</td><td>'.(($row['type'] == 1) ? "Super Admin" : "Admin").'</td><td><a class="button wa"  href="'.$updatePath.$row['id'].'"><i class="fa fa-pencil"></i> Edit</a> <a class="button wa"  href="'.fixedPath."/administration/user/delete?id=".$row['id'].'"><i class="fa fa-times-circle"></i> Delete</a></td></tr>';
				}
				?>
				</tbody>
			</table>
			<?php
		}
	?>
	<hr />
	<a class="button wa" href="<?php echo fixedPath; ?>/administration/backup/importDump.php"><i class="fa fa-level-up"></i> Import Project</a>
	<hr />
	<p><a class="button wa" href="<?php echo fixedPath; ?>/admin"><i class="fa fa-th-list"></i> Back to Admin</a></p>
	
<?php
	}
	pageFooter();
?>