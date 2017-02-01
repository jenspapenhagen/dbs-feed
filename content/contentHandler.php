<?php
class contentHandler{
	
    //construct
    function contentHandler(){
		
    }
	
    function guidv4() {
		if (function_exists('com_create_guid') === true){
			return trim(com_create_guid(), '{}');
		}
		$data = openssl_random_pseudo_bytes(16);
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
		$output = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		
		return $output;
	}
	
	function curlWebsite($url, $savefilename, $proxy=0, $randUserAgent=0){
			//File to save the contents to
			$filename = "content/".$savefilename.".txt";
			$fp = fopen ($filename, "w+");
		
			//Here is the file we are downloading, replace spaces with %20
			$ch = curl_init(str_replace(" ","%20",$url));
			if($randUserAgent != 0 and file_exists("browser.txt")){
				$f_contents = file("browser.txt");
				$randUserAgent = $f_contents[rand(0, count($f_contents) - 1)];
				curl_setopt($ch, CURLOPT_USERAGENT,$randUserAgent);
			}else{
				curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
			}
			
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 50);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
			curl_setopt($ch, CURLOPT_REFERER, 'http://www.marinetraffic.com/');//hardcoded for this project

			if($proxy != 0 and file_exists("proxy.txt")){
				//get random proxy form list
				$f_contents = file("proxy.txt");
				$line = $f_contents[rand(0, count($f_contents) - 1)];
				$port = explode(" ", $line);
				curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
				curl_setopt($ch, CURLOPT_PROXY, $line);
				curl_setopt($ch, CURLOPT_PROXYPORT, $port[1]);
			}
			//give curl the file pointer so that it can write to it
			curl_setopt($ch, CURLOPT_FILE, $fp);
		
			$data = curl_exec($ch);//get curl response
		
			curl_close($ch);
		}
	

    function UPDATE(){
            $url = "http://app.dsbcontrol.de/data/2411de16-a699-4014-8ab4-5fe9200b2e11/e4ff3d3d-6122-4b0d-8dc7-99f75fa497a0/e4ff3d3d-6122-4b0d-8dc7-99f75fa497a0.html";
            $shortname = "list";
            $delfile = "content/".$shortname.".txt";
			$output = "";
            if(file_exists($delfile) and filemtime($delfile) < (time() - 300 )){
                unlink($delfile);
                $this->curlWebsite($url, $shortname);
                $output = "Updated";
            }else{
                $output = "Update only every 5min";
            }
        
        return $output;
    }
	
    //parsing functions
    function get_linenumber_form_file($file, $search){
        $line_number = false;
    
        if ($handle = fopen($file, "r")) {
            $count = 0;
            while (($line = fgets($handle, 4096)) !== FALSE and !$line_number) {
                $count++;
                if(strpos($line, $search) !== FALSE){
                    $line_number = $count;
                }else{
                    $line_number = $line_number;
                }
            }
            fclose($handle);
        }
    
        return $line_number;
    }
    
    
    function getStatus($filename){
        $file = "content/".$filename.".txt";
        $statuslinenumber = $this->get_linenumber_form_file($file, "    <tr><td>Letzte ");
        //file in to an array
        $lines = file($file);
        $output = $lines[$statuslinenumber -1];
        $output = strip_tags($output);//remove HTML crap
        $output = trim($output);
		$output = str_replace("Letzte Änderung: ","",$output);
    
        return $output;
    }
    
	function getDates($filename){
		$file = "content/".$filename.".txt";
		$str ="";
		$stringcutter="";
		
        $startdate = $this->get_linenumber_form_file($file, '<tr class="def"><td>');
		$enddate = $this->get_linenumber_form_file($file, '</body>');
        $lines = file($file);


		for($i=($startdate-1); $i<=($enddate-3); ){
			$stringcutter = explode("<td>",$lines[$i]);
			$stringcutter = str_replace("<td>"," ", $stringcutter); 
			$str .= "\t"."<item>"."\n";
			$str .= "\t"."<title>Vertretungs-Text: ".trim(strip_tags($stringcutter[4]))."</title>"."\n"; //mit Aufgaben
			$str .= "\t"."<link>http://blank.com</link>"."\n";
			$str .= "\t"."<description>Die Klasse ".strip_tags($stringcutter[1]);
			
			$stunden = strip_tags($stringcutter[2]);
				if(strlen($stunden) > 1 ){
					$str .= " in den Stunden ".$stunden;
				}else{
					$str .= " in der Stunde ".$stunden;
				}
			
			$str .= " in Raum ".strip_tags($stringcutter[3])."</description>"."\n";
			$str .= "\t"."<guid>".$this->guidv4()."</guid>\n";
			$str .= "\t"."</item>\n";
			
			$i++;
		}
        return $str;
	}
	

	
    function writeNewXML(){
        //del old files and create a new xmlfile
        $contentfile = "content.xml";
        if(file_exists($contentfile)){
            unlink($contentfile);
            unlink("content.json");
        }
        $newXMLfile = fopen($contentfile, "a+");
		
		
		//the header of the XML
        $str = "<?xml version=\"1.0\" encoding=\"UTF-8\"";
        $str .= "?>"."\n";
		$str .= '<rss version="2.0">'."\n";
		$str .= '<channel>'."\n";
        $str .= "<title>Digitales Schwarzes Brett</title>"."\n";
		$str .= "<description>Das Digitales Schwarzes Brett ohne nervige App</description>"."\n";
		$str .= "<link>http://www.google.com</link>"."\n";
		$str .= "<copyright>Copyright 2017 </copyright>"."\n";
		$str .= "<docs>http://blogs.law.harvard.edu/tech/rss</docs>"."\n";
		$str .= "<language>en-us</language>"."\n";
		$str .= "<lastBuildDate>".gmdate(DATE_RFC822,strtotime($this->getStatus("list")))."</lastBuildDate>"."\n";
		$str .= "<managingEditor>jens.papenhagen@web.de  (Jens Papenhagen)</managingEditor>"."\n";
		$str .= "<pubDate>".gmdate(DATE_RFC822,strtotime(date("Y-m-d H:i:s T",time())))."</pubDate>"."\n";
		$str .= "<webMaster>jens.papenhagen@web.de  (Jens Papenhagen)</webMaster>"."\n";
		$str .= "<generator>uglyHTML2RSS(0.0.1)</generator>"."\n";
		
		//body of the XML
        $str .= $this->getDates("list");

		//footer of the XML
        $str .= "</channel>";

        //save the XML file
        fwrite($newXMLfile, $str);
        fclose($newXMLfile);

       //convert to JSON for cross domain fuckup of XML. thanks obama
       //$this->ParseXMLToJSON($contentfile);
    }

    
    function ParseXMLToJSON($file) {
        $fileContents= file_get_contents($file);
			//rss overhead removing
			unset($fileContents[2]); //<rss version="2.0">
			for($i=0; $i<=13; $i++){
				unset($fileContents[3])
			}
        $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $simpleXml = simplexml_load_string($fileContents);
        $json = json_encode($simpleXml);
        
        //save to json file
        $contentfile = "content.json";
        $newJSONfile = fopen($contentfile, "a+");
        fwrite($newJSONfile, $json);
        fclose($newJSONfile);
    }
    
    
}
?>