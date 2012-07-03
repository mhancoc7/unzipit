<?php
###############################################################
# UnZipit
###############################################################
# Developed by Jereme Hancock for Cloud Sites
# Visit http://cloudsitesrock.com for updates
###############################################################
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-UK">
<head>
<title>UnZipit</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name='robots' content='noindex,nofollow' />
<script type="text/javascript">
function check(){
var r = confirm("Are you sure you want to unzip this file? \n\nThis will overwrite any existing files or folders with the same name as the contents of this archive.");
if(r){
return true;
}
else{
return false;
}
}
</script>

<style>
body {
height:100%;
background: #ddd;
margin-bottom: 1px;
}

.button { 

            border: 1px solid #818185; 
            -moz-border-radius: 15px;
            border-radius: 15px;
            height:30px;
            width:200px;
            padding-left:8px;
            padding-right:8px;
}
            
.wrapper{

        width:450px;
    position:absolute;
    left:50%;
	top:50%;
	margin:-225px 0 0 -245px;
        background-color:#eee;
        -moz-border-radius: 15px;
        border-radius: 15px;
        padding:30px;
        box-shadow: 5px 5px 7px #888;
       -moz-box-shadow: 5px 5px 7px #888;
       -webkit-box-shadow: 5px 5px 7px #888;
}

a {
color:#55688A;
}

h2 {
color:#55688A;
}

@font-face {
	font-family: Fontin;
	src: url('../fonts/fontin.otf');
}

.head {
text-align:center;
font-family: Arial;
font-size:28px;
margin-bottom:10px;
margin-top:-30px;
}
</style>
</head>
<body>
<div class="wrapper">
<center><div class="head"><h2><em>UnZipit</em></h2></div></center>
<?php
$file = $_GET['file'];
$deletezip = $_GET['deletezip'];
$unzip = $_GET['unzip'];

ini_set('max_execution_time', 900);

if (isset($file))
{
// progress bar class/functions
class ProgressBar {
	var $percentDone = 0;
	var $pbid;
	var $pbarid;
	var $tbarid;
	var $textid;
	var $decimals = 1;

	function __construct($percentDone = 0) {
		$this->pbid = 'pb';
		$this->pbarid = 'progress-bar';
		$this->tbarid = 'transparent-bar';
		$this->textid = 'pb_text';
		$this->percentDone = $percentDone;
	}

	function render() {
		print($this->getContent());
		$this->flush();
	}

	function getContent() {
		$this->percentDone = floatval($this->percentDone);
		$percentDone = number_format($this->percentDone, $this->decimals, '.', '') .'%';
		$content .= '<div id="'.$this->pbid.'" class="pb_container">
			<div id="'.$this->textid.'" class="'.$this->textid.'">'.$percentDone.'</div><br><div style="position:relative; top:-10px;">Please wait...</div>
			<div class="pb_bar">
				<div id="'.$this->pbarid.'" class="pb_before"
				style="width: '.$percentDone.';"></div>
				<div id="'.$this->tbarid.'" class="pb_after"></div>
			</div>
			<br style="height: 1px; font-size: 1px;"/>
		</div>
		<style>
			.pb_container {
				position: relative;
			}
			.pb_bar {
				width: 100%;
				height: 1.3em;
				border: 1px solid silver;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				-moz-border-radius-bottomleft: 5px;
				-moz-border-radius-bottomright: 5px;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-webkit-border-bottom-left-radius: 5px;
				-webkit-border-bottom-right-radius: 5px;
			}
			.pb_before {
				float: left;
				height: 1.3em;
				background-color: #43b6df;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-bottomleft: 5px;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-bottom-left-radius: 5px;
			}
			.pb_after {
				float: left;
				background-color: #FEFEFE;
				-moz-border-radius-topright: 5px;
				-moz-border-radius-bottomright: 5px;
				-webkit-border-top-right-radius: 5px;
				-webkit-border-bottom-right-radius: 5px;
			}
			.pb_text {
				padding-top: 0.1em;
				position: absolute;
				left: 48%;
                                display:none;
			}
		</style>'."\r\n";
		return $content;
	}

	function setProgressBarProgress($percentDone, $text = '') {
		$this->percentDone = $percentDone;
		$text = $text ? $text : number_format($this->percentDone, $this->decimals, '.', '').'%';
		print('
		<script type="text/javascript">
		if (document.getElementById("'.$this->pbarid.'")) {
			document.getElementById("'.$this->pbarid.'").style.width = "'.$percentDone.'%";');
		if ($percentDone == 100) {
			print('document.getElementById("'.$this->pbid.'").style.display = "none";');
		} else {
			print('document.getElementById("'.$this->tbarid.'").style.width = "'.(100-$percentDone).'%";');
		}
		if ($text) {
			print('document.getElementById("'.$this->textid.'").innerHTML = "'.htmlspecialchars($text).'";');
		}
		print('}</script>'."\n");
		$this->flush();
	}

	function flush() {
		print str_pad('', intval(ini_get('output_buffering')))."\n";
		flush();
	}
}
echo '<center>';

// start progress bar
    $p = new ProgressBar();
    echo '<div style="width: 300px;">';
    $p->render();

// set the command to run
    $cmd = "unzip -o $file";
    $pipe = popen($cmd, 'r');

    if (empty($pipe)) {
    throw new Exception("Unable to open pipe for command '$cmd'");
    }

    stream_set_blocking($pipe, false);

    while (!feof($pipe)) {
    fread($pipe, 1024);

for ($i = 0; $i < ($size = 100); $i++) {
// keeps browser from timing out after 30 seconds
   $p->setProgressBarProgress($i*100/$size);
   usleep(100000*0.1);
}
echo '<script type="text/javascript">';
echo 'alert("Unzip has completed!")';
echo '</script>';  
echo "<script>location.href='unzip.php'</script>";
}
if (isset($deletezip)) {
echo "Deleting Zip...<br />\n";
unlink("$file");
}
}
if (isset($unzip)) {
echo "Deleting Script...<br />\n";
unlink(__FILE__);
echo "Script Deleted!<br /><a href=\"/\">HOME</a>\n";
exit;
}
$handler = opendir(".");
echo "Please choose a file to unzip: <br />\n";
echo '<form action="" method="get">'."\n";
$found = 0;
while ($file = readdir($handler))
{
if(strrchr($file,".zip") != ".zip" ) { continue; }
{
echo '<input type="radio" name="file" value="' . $file . '"/> ' . $file . "<br />\n";
$found = 1;
}
}
echo '<hr/><input type="checkbox" name="deletezip" value="Remove" />Delete .zip after extraction?'."<br 

/>\n";
echo '<input type="checkbox" name="unzip" value="Remove" />Delete UnZipit Script?'."<br />\n";
closedir($handler);
if ($found == FALSE)
echo "No .zips found<br />";
else
echo '<br />NOTE: This unzips and <strong><font color="red">REPLACES</font></strong> files.<br /><br /><br /><center><input type="submit" 
value="UnZipit!" class="button" onclick="return check();" /></center>';
echo "\n</form><br /><br /><center>
 <font size='1em'>Developed by <a href='http://www.cloudsitesrock.com' target='_blank'>CloudSitesRock.com</a> for Rackspace Cloud Sites</font></center>";
?>

</div>
</body>
</html>