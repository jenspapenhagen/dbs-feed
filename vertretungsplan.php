<?php
include_once "Handler/contentHandler.php";
include_once "Handler/fileHandler.php";
$contentHandler = new contentHandler();
$fileHandler = new fileHandler();
//$contentHandler->writeNewXML();

$rawfile = "content/list.txt";
if((isset($_GET["update"]) and $_GET["update"] == 1) OR filemtime($rawfile) < (time() - 300 )){
    $contentHandler->UPDATE();
	$contentHandler->writeNewXML();
	$contentHandler->ParseXMLToJSON("content.xml");
}
if(isset($_REQUEST['callback']) and $_REQUEST['callback'] == "?" and file_exists("content.json") ){
	header('Content-Type: text/javascript');
	echo "callback (" ;
	include_once "content.json";
	echo ");";
}else{


	$json = file_get_contents("content.json");

    $jsonIterator = new RecursiveIteratorIterator( new RecursiveArrayIterator(json_decode($json, TRUE)),
        RecursiveIteratorIterator::SELF_FIRST);

    echo '<center><table  class="rand08" >';
    echo "<tr><th>Titel</th><th>description</th></tr>"."\n";
    foreach ($jsonIterator as $key => $val) {

            if( isset($val['title']) ){
                echo "<tr>";
                echo "<td>".$val['title']."</td>";
            }
            if( isset($val['description']) ){
                echo "<td>".$val['description']."</td>";
            }


    }
    echo "</table></center>";
	
	
	
	
	echo "<br>";
	echo '<a href="feed.xml"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Generic_Feed-icon.svg/250px-Generic_Feed-icon.svg.png" height="24" width="24" alt="RSS-Feed"></a>';
    echo '<br><a href="index.php?callback=?">JSON</a>&nbsp;&nbsp;';
	if (file_exists($rawfile)) {
		echo '<br>Last Update: '.date ("d F Y H:i:s.",filemtime($rawfile));
	}
}

?>