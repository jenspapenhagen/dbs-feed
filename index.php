<?php
include_once "Handler/contentHandler.php";
$contentHandler = new contentHandler();
$contentHandler->writeNewXML();


if(isset($_GET["update"]) and $_GET["update"] == 1){
    $contentHandler->UPDATE();
	$contentHandler->writeNewXML();
	$contentHandler->ParseXMLToJSON("content.xml");

    echo "Update done";
}else if(isset($_REQUEST['callback']) and $_REQUEST['callback'] == "?" and file_exists("content.json") ){
	header('Content-Type: text/javascript');
	echo "callback (" ;
	include_once "content.json";
	echo ");";
}else{
	echo '<a href="feed.xml"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Generic_Feed-icon.svg/250px-Generic_Feed-icon.svg.png" alt="RSS-Feed"></a>';
    echo '<br><a href="index.php?callback=?">JSON</a>';
	$filename = "conten/list.txt";
	if (file_exists($filename)) {
		echo '<br>Last Update: '.date ("F d Y H:i:s.",filemtime("conten/list.txt"));
	}
    //echo "<a href=\"?update=1\">Update</a> ";
	die();
}

?>