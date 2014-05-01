Located within the includes directory there is a file called Settings.php.

Ensure these settings, as well as the Database  settings ( db.php ) match your current configuration

Regarding FFMPEG - Ensure you have a version which suits your needs. I have included a Windows binary, and a Linux binary Downloaded from:

Windows  : http://ffmpeg.zeranoe.com/builds/
GNU/Linux: http://sourceforge.net/projects/ffmpeg-builds/?source=dlp

Place these binary files in: /administration/clip/ and make appropriate edits to convert.php.

This should be incorporated into the default styles
.video .desktop #clickActions{
  position: fixed;
  top: 0px;
  background-color: transparent;
}

.video .desktop #clickActions li.actionButton{
 background-color: transparent;
  background: none;
 display: block;
 border: none;
  line-height: 1em;
  color: transparent !important;
  box-shadow: none;
  height: 400px;
  top: -200px;
  float: left;
  position: relative;

}

.video .desktop #clickActions li.actionButton strong{
  display: none;
}

.video .desktop #clickActions ul.decisions{
 width: 95%;
 margin: 0 auto;
  top: 50%;
  position: relative;
}

.video .desktop #clickActions ul.decisions-3 li.actionButton{
  width: 31%;
  margin: 0 1%;
}

.video .desktop #clickActions ul.decisions-2 li.actionButton{
  width: 45%;
  margin: 0 1%;

  
}

.desktop #badge{ top: 0px; left: 0px; width: 20%; }

.desktop .badge,
.desktop #badge .badge{
  width: 100%;
  max-width: 100%;
  height: auto;
  max-height: 100%;
}

#currentQuestion{
 display: none; 
}

.mobile #badge{
 clear: both;
  width: 100%;
  position: relative;
}

.mobile #currentQuestion{
 clear: both;
 display: block;
  color: #fff;
 width: 90%;
  margin: 0 auto;
  font-size: 30px;
}

div.actions ul li{
 font-family: arial;
  text-transform: uppercase;
}



.mobile .jp-gui a.jp-video-play{
	height: 64px;
}

.mobile a.jp-video-play-icon{
 margin-top: 240px !important; 
}