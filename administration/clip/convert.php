<?php
class Convert{

	private $sourceFolder;
	private $destinationFolder;
	private $sourceFile;
	private $destinationFile;
	private $width;
	private $height;
	private $clipID;
	private $type;
	private $projectID;
	private $connection;
	
	private $threads = 4;
	private $fps = 24;
	private $ext;
	
	private $outputpath;
	
	function __construct($dbc=null) {
		$this->connection = $dbc;
	}
	
	function doConvert(){
		
		$id = $this->updateRecord();
		
		$postfix = "_o";
		if( $this->getWidth() != "" && $this->getHeight() != ""){
			$postfix = "_".$this->getWidth()."x".$this->getHeight();
		}
		
		set_time_limit( 10 * 60 ); //10 minute time out
		
		if( $postfix == "_o" ){
			$cmd = "ffmpeg.exe -i ".$this->getSourceFolder().$this->getSourceFile()." -threads ".$this->getThreads()." -r ".$this->getFps()." ".$this->getDestinationFolder().$this->getDestinationFile().$postfix.".m4v 2>&1";
		}else{
			$cmd = "ffmpeg.exe -i ".$this->getSourceFolder().$this->getSourceFile()." -threads ".$this->getThreads()." -r ".$this->getFps()." -vf scale=".$this->getWidth().":".$this->getHeight()." ".$this->getDestinationFolder().$this->getDestinationFile().$postfix.".m4v 2>&1";
		}
			
			
		
		$this->setOutputpath( "/uploads/".$this->getDestinationFile().$postfix.".m4v");
		exec( $cmd, $output);
		
		$this->updateRecord($id);
		
	}
	
	function updateRecord($id=null){
		//
		$clips = new Clips( $this->getConnection() );
		$clips->setConverted( 0 );
		
		if( $id != null ){
			$clips->setId( $id );
			$clips->setConverted( 1 );
		}
		
		$clips->setClipID( $this->getClipID() );
		$clips->setProjectID( $this->getProjectID() );
		$clips->setPath( $this->getOutputpath() );
		$clips->setType( $this->getType() );
		
		return $clips->save();
		
	}
	
	function setThreads($threads) { $this->threads = $threads; }
	function getThreads() { return $this->threads; }
	function setFps($fps) { $this->fps = $fps; }
	function getFps() { return $this->fps; }
	function setSourceFolder($sourceFolder) { $this->sourceFolder = $sourceFolder; }
	function getSourceFolder() { return $this->sourceFolder; }
	function setDestinationFolder($destinationFolder) { $this->destinationFolder = $destinationFolder; }
	function getDestinationFolder() { return $this->destinationFolder; }
	function setSourceFile($sourceFile) { $this->sourceFile = $sourceFile; }
	function getSourceFile() { return $this->sourceFile; }
	function setDestinationFile($destinationFile) { $this->destinationFile = $destinationFile; }
	function getDestinationFile() { return $this->destinationFile; }
	function setWidth($width) { $this->width = $width; }
	function getWidth() { return $this->width; }
	function setHeight($height) { $this->height = $height; }
	function getHeight() { return $this->height; }
	function setClipID($clipID) { $this->clipID = $clipID; }
	function getClipID() { return $this->clipID; }
	function setType($type) { $this->type = $type; }
	function getType() { return $this->type; }
	function setProjectID($projectID) { $this->projectID = $projectID; }
	function getProjectID() { return $this->projectID; }
	function setConnection($connection) { $this->connection = $connection; }
	function getConnection() { return $this->connection; }
	function setExt($ext) { $this->ext = $ext; }
	function getExt() { return $this->ext; }
	function setOutputpath($outputpath) { $this->outputpath = $outputpath; }
	function getOutputpath() { return $this->outputpath; }
	
}
?>