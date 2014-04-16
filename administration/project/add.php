<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	

if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	
	if( isset( $_POST['title'] ) && $_POST['title'] != "" ){
		$title = $_POST['title'];
		$conn = getConnection();
		$project = new Projects($conn);
		$project->setTitle( $title );
		$id = $project->save();
	
		if( $id > 0 ){
			header("location: ".fixedPath."/administration/project/edit?id=".$id );
		
		}else{
			pageHeader();
			?>
				<p>An error has occurred...</p>
			<?php
			pageFooter();
		}
	}
}else{
	pageHeader();
?>
	<h1><i class="fa fa-rocket"></i> Create a New Project</h1>

	<p>To begin your project, all you have to do is give it a title, and then we can begin!</p>
	
	<form method="post" action="add.php">
		<label for="title"><b>Project Title:</b></label> <input type="text" id="title" name="title" value="" placeholder="A really cool Name"/> <br />
		<input type="submit" value="Proceed" class="button wa" />
	</form>
<?php
	pageFooter();
}
?>