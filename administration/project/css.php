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
			
			$CSS = ( isset( $_POST['CSS'] ) ? $_POST['CSS'] : "" );
			$stylesheet = new stylesheet( $conn );
			
			if( isset( $_POST['SSID'] ) && $_POST['SSID'] == "" ){
				$stylesheet->setCSS( $CSS );
				$stylesheet->setProjectID( $projectID );
				$stylesheet->save();
				echo "New SS Saved as: ".$stylesheet->getId();
			}else{
				$stylesheet = $stylesheet->loadByProjectID($projectID);
				if( is_object(  $stylesheet ) ){
					$stylesheet->setCSS( $CSS );
					$saved = $stylesheet->save();
					
					echo "Saved";
					
				}else{
					echo "Error Could not save changes";
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
		$stylesheet = new stylesheet( getConnection() );
		$stylesheet = $stylesheet->loadByProjectID($projectID );
		
		if( is_object($stylesheet) ){
			$css = $stylesheet->getCSS();
			$ssid = $stylesheet->getId();
		}
?>
	<h1>&ldquo;<?php echo $project->getTitle(); ?>&rdquo; project StyleSheet</h1>
	<form name="ProjectCSS" method="POST" action="css.php">
		<p>Within the editor press F11 to go fullScreen. When typing if the word goes red press Ctrl-Space for auto-complete</p>
		<textarea style="width: 100%; min-height: 500px; overflow: auto;" name="CSS" id="CSS"><?php echo $css; ?></textarea>
		<input type="hidden" name="projectID" value="<?php echo $projectID; ?>" />
		<input type="hidden" name="SSID" value="<?php echo $ssid; ?>" /><br />
		<input class="button wa" type="submit" value="Save" />
		<br />
	</form>
	<script type="text/javascript">
	  var editor = CodeMirror.fromTextArea(document.getElementById("CSS"), {
			lineNumbers: true,
			mode: "text/css", //text/html
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
