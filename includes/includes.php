<?php
	session_start();
	define("ADMIN_DIR", "/administration");
	
	$path = dirname ( __FILE__ );
	
	require_once($path."/db.php");
	
	require_once($path.'/classes/projects.php');
	require_once($path.'/classes/segments.php');
	require_once($path.'/classes/decisions.php');
	require_once($path.'/classes/decisionTree.php');
	require_once($path.'/classes/clip.php');
	require_once($path.'/classes/clips.php');
	require_once($path.'/classes/badge.php');
	
	require_once($path.'/classes/analytics_visitors.php');
	require_once($path.'/classes/analytics_visitor_event.php');
	require_once($path.'/classes/entry.php');
	require_once($path.'/classes/administrator.php');
	require_once($path.'/classes/adminSession.php');
	
	require_once($path."/pageTemplate.php");
	
	function logMessage($message=null, $filename=null){
		if ( is_null($filename) ){
			$filename = "logger.log";
		}
		$p = str_replace('\\','/',dirname(dirname(__FILE__)));
		$logMsg=date('Y/m/d H:i:s').": $message\r\n";
		
		file_put_contents( $p."/logs/".$filename, $logMsg, FILE_APPEND);
		
	}
	
	function pa( $arr ){
		echo '<pre>'.print_r($arr,true).'</pre>';
	}
	
?>