<?php
	error_reporting(E_ALL);
	include "../../includes/includes.php";
	pageHeader();
	
	
	$CMPATH = fixedPath."/js/codemirror-4.0";
	?>
	<link rel="stylesheet" href="<?php echo $CMPATH; ?>/lib/codemirror.css">
	<link rel="stylesheet" href="<?php echo $CMPATH; ?>/addon/hint/show-hint.css">
	<link rel="stylesheet" href="<?php echo $CMPATH; ?>/addon/display/fullscreen.css">
	<script src="<?php echo $CMPATH; ?>/lib/codemirror.js"></script>
	<script src="<?php echo $CMPATH; ?>/addon/hint/show-hint.js"></script>
	<script src="<?php echo $CMPATH; ?>/addon/hint/xml-hint.js"></script>
	<script src="<?php echo $CMPATH; ?>/addon/hint/html-hint.js"></script>
	<script src="<?php echo $CMPATH; ?>/mode/xml/xml.js"></script>
	<script src="<?php echo $CMPATH; ?>/mode/javascript/javascript.js"></script>
	<script src="<?php echo $CMPATH; ?>/mode/css/css.js"></script>
	<script src="<?php echo $CMPATH; ?>/mode/htmlmixed/htmlmixed.js"></script>
	<script src="<?php echo $CMPATH; ?>/addon/display/fullscreen.js"></script>
	<style>
		.CodeMirror{
			border: 1px solid #000;
		}
	</style>
	<?php
	$conn = getConnection();
	$project = "";
	$projectID = "";
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if( isset( $_POST['projectID'] ) && $_POST['projectID'] != "" ){
			$projectID = $_POST['projectID'];
			
			checkAccess( $projectID );
			
			$js = ( isset( $_POST['js'] ) ? $_POST['js'] : "" );
			$javascript = new javascript( $conn );
			
			if( isset( $_POST['SSID'] ) && $_POST['SSID'] == "" ){
				
				$javascript->setProjectID( $projectID );
				$javascript->setJS( $js );
				$javascript->save();
				echo "New JS Saved as: ".$javascript->getId();
			}else{
				$javascript = $javascript->loadByProjectID($projectID);
				if( is_object(  $javascript ) ){
					$javascript->setJS( $js );
					$saved = $javascript->save();
					
					echo "Saved";
					
				}else{
					echo "<p class='error'>Error Could not save changes</p>";
				}
			}
			$project = new Projects($conn);
			$project = $project->load($projectID);
		}else{
			$project = "";
		}
		
	}
	
	if( isset($_REQUEST['projectID']) && $_REQUEST['projectID'] ){
		$projectID = $_REQUEST['projectID'];
		$project = new Projects($conn);
		$project = $project->load($projectID);
		
		checkAccess( $projectID );
	}
	
	if( is_object($project) ){
		$javascript = new javascript( getConnection() );
		$javascript = $javascript->loadByProjectID($projectID );
		
		if( is_object($javascript) ){
			$js = $javascript->getJS();
			$ssid = $javascript->getId();
		}
		
		
?>
	<h1>&ldquo;<?php echo $project->getTitle(); ?>&rdquo; project's javascript</h1>
	<form name="Projectjs" method="POST" action="js.php">
		<p>Do not include &lt;script&gt; tags. This is included in the project as a js file within the footer.</p>
		<p>Within the editor press F11 to go fullScreen. When typing if the word goes red press Ctrl-Space for auto-complete</p>
		<textarea style="width: 100%; min-height: 500px; overflow: auto;" name="js" id="js"><?php echo $js ?></textarea>
		<input type="hidden" name="projectID" value="<?php echo $projectID; ?>" />
		<input type="hidden" name="SSID" value="<?php echo $ssid; ?>" /><br />
		<input class="button wa" type="submit" value="Save" />
	</form>
	<div class="clear"></div>
	<script type="text/javascript">
	  var editor = CodeMirror.fromTextArea(document.getElementById("js"), {
			lineNumbers: true,
			mode: "text/javascript", //text/html
			extraKeys: { 
				"F11": function(cm) {
				  cm.setOption("fullScreen", !cm.getOption("fullScreen"));
				},
				"Esc": function(cm) {
				  if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
				},
				"Ctrl-Space": "autocomplete"
			}
		});
	  	
	</script>
<?php
	}
	footerMenu($projectID );
	pageFooter();
?>
