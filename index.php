<?php
include_once "content/contentHandler.php";
$contentHandler = new contentHandler();
$contentHandler->writeNewXML();


if(isset($_GET["update"]) and $_GET["update"] == 1){
    $contentHandler->UPDATE();
	$contentHandler->writeNewXML();

    echo "Update done";
}else if(isset($_REQUEST['callback']) and $_REQUEST['callback'] == "?" and file_exists("content.json") ){
	header('Content-Type: text/javascript');
	echo "callback (" ;
	include_once "content.json";
	echo ");";
}else{
    echo "<a href=\"?update=1\">Update</a> ";
	die();
}

?>