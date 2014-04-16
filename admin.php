<?php
	error_reporting(E_ALL);
	include "includes/includes.php";
	pageHeader();
?>
<h1>Admin</h1>
<style>
	a{
		transition: color .4s;
		min-width: 50px;
		display: inline-block;
		text-decoration: none;
		font-size: 12px;
	}
	a,
	a:visited{
		color: #000;
	}
	
	a:hover{
		color: #f00;
	}

	a.button,
	a.button:hover{
		color: #fff;
	}
	
	a.projectName{
		font-weight: bold;
		text-transform: capitalize;
	}
	
	ol{
		padding-left: 20px;
	}
	
	a.showTour:hover i{
		color: #ff0;
	}
	
	
</style>
<?php
	$conn = getConnection();
	
	/*List Projects*/
	$project = new Projects($conn);
	$projects = $project->listProjects();
	
	if( is_array( $projects ) ){
		echo '<h3>'.sizeof($projects)." projects to choose</h3>";
		if( sizeof($projects) > 0 ){
			echo "<ol>";
			foreach( $projects as $project){
				echo "<li><p><a class='projectName' href='".fixedPath."/administration/project/edit?id=".$project->getId()."'>".$project->getTitle()."</a> (id: ".$project->getId().") -  <a href='".fixedPath."/administration/project/settings?projectID=".$project->getId()."'><i class='fa fa-pencil'></i> Edit Project Settings</a> - <a href='".fixedPath."/administration/MapProject.php?projectID=".$project->getId()."'><i class='fa fa-sitemap'></i> View Flow Chart</a> - <a href='".fixedPath."/administration/ProjectAnalytics.php?projectID=".$project->getId()."'><i class='fa fa-bar-chart-o'></i> View Analytics</a> - <a href='".fixedPath."/administration/ProjectContestEntries?projectID=".$project->getId()."'><i class='fa fa-tasks'></i> View Contest Entries</a> <a class='showTour' href='".fixedPath."/?tourID=".$project->getId()."' target='_blank'><i class='fa fa-star'></i> View Tour</a></p></li>";
			
			}
			echo "</ol>";
		}
	}

?>

<a href="<?php echo fixedPath; ?>/administration/project/add" class="button wa"><i class="fa fa-plus"></i> Add Project</a>


<?php
	pageFooter();
?>