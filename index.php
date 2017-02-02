<?php
include_once "Handler/contentHandler.php";
$contentHandler = new contentHandler();
//$contentHandler->writeNewXML();

$rawfile = "content/list.txt";
if((isset($_GET["update"]) and $_GET["update"] == 1) OR filemtime($rawfile) < (time() - 300 )){
    $contentHandler->UPDATE();
	$contentHandler->writeNewXML();
	$contentHandler->ParseXMLToJSON("content.xml");
}else if(isset($_REQUEST['callback']) and $_REQUEST['callback'] == "?" and file_exists("content.json") ){
	header('Content-Type: text/javascript');
	echo "callback (" ;
	include_once "content.json";
	echo ");";
}else{
	echo '<a href="feed.xml"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Generic_Feed-icon.svg/250px-Generic_Feed-icon.svg.png" alt="RSS-Feed"></a>';
    echo '<br><a href="index.php?callback=?">JSON</a>&nbsp;&nbsp;';

	if (file_exists($rawfile)) {
		echo '<br>Last Update: '.date ("d F Y H:i:s.",filemtime($rawfile));
	}
    echo "<a href=\"?update=1\">Update</a> ";
}

?>