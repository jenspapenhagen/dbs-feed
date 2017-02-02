<?php
include_once "Handler/fileHandler.php";
include_once "Handler/curlHandler.php";
include_once "Handler/timedateHandler.php";

class contentHandler{
    private $fileHandler;
    private $curlHandler;
    private $timedateHandler;

    //construct
    public function __construct(){
		$this->fileHandler = new fileHandler();
		$this->curlHandler = new curlHandler();
        $this->timedateHandler = new timedateHandler();
    }
	public function contentHandler(){
        self::__construct();
    }
	
    public function guidv4() :string {
		if (function_exists('com_create_guid') === true){
			return trim(com_create_guid(), '{}');
		}
		$data = openssl_random_pseudo_bytes(16);
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
		$output = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		
		return $output;
	}
	
	public function UPDATE() :string{
            $url = "http://app.dsbcontrol.de/data/2411de16-a699-4014-8ab4-5fe9200b2e11/e4ff3d3d-6122-4b0d-8dc7-99f75fa497a0/e4ff3d3d-6122-4b0d-8dc7-99f75fa497a0.html";
            $shortname = "list";
            $rawfile = "content/".$shortname.".txt";
			$output = "";
            if(file_exists($rawfile) and filemtime($rawfile) < (time() - 300 )){
                unlink($rawfile);
                $this->curlHandler->curlWebsite($url, $shortname);
                $output = "Updated";
            }else{
                $output = "Update only every 5min";
            }
        
        return $output;
    }
	
    //parsing functions
    public function get_linenumber_form_file(string $file, string $search):int{
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
    
    
    public function getStatus(string $filename): string{
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
    
	public function getDates(string $filename): string{
		$file = "content/".$filename.".txt";
		$str ="";
		$stringcutter="";
		
        $startdate = $this->get_linenumber_form_file($file, '<tr class="def"><td>');
		$enddate = $this->get_linenumber_form_file($file, '</body>');
        $lines = file($file);

		
		for($i=($startdate-1); $i<=($enddate-3); ){
			if( strpos($lines[$i],'class="def"') OR strpos($lines[$i], 'class="alt"') ){
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
			}
			$i++;
		}
        return $str;
	}
	

	
    public function writeNewXML(){
        //del old files and create a new xmlfile
        $contentfile = "content.xml";
        $rssfile = "feed.xml";
        $jsonfile = "content.json";
        if(file_exists($contentfile)){
            unlink($contentfile);
            unlink($rssfile);
            unlink($jsonfile);
        }

        $newXMLfile = fopen($contentfile, "a+");
        $newRSSfile = fopen($rssfile, "a+");
		
		//the header of the XML
        $XMLheader = "<?xml version=\"1.0\" encoding=\"UTF-8\"";
        $XMLheader .= "?>"."\n";
		
        //rss header
        $RSSheader = '<rss version="2.0">'."\n";
        $RSSheader .= "<title>Digitales Schwarzes Brett</title>"."\n";
		$RSSheader .= "<description>Das Digitales Schwarzes Brett ohne nervige App</description>"."\n";
		$RSSheader .= "<link>http://www.google.com</link>"."\n";
		$RSSheader .= "<copyright>Copyright 2017 </copyright>"."\n";
		$RSSheader .= "<docs>http://blogs.law.harvard.edu/tech/rss</docs>"."\n";
		$RSSheader .= "<language>en-us</language>"."\n";
		$RSSheader .= "<lastBuildDate>".$this->timedateHandler->RFC822Time($this->getStatus("list"))."</lastBuildDate>"."\n";
		$RSSheader .= "<managingEditor>jens.papenhagen@web.de  (Jens Papenhagen)</managingEditor>"."\n";
		$RSSheader .= "<pubDate>".$this->timedateHandler->RFC822Time(date("Y-m-d H:i:s T",time()))."</pubDate>"."\n";
        $RSSheader .= "<webMaster>jens.papenhagen@web.de  (Jens Papenhagen)</webMaster>"."\n";
        $RSSheader .= "<generator>uglyHTML2RSS(0.0.1)</generator>"."\n";
		
		//XML header
		$XMLheader2 = '<channel>'."\n";
		
		//body of the XML
        $XMLbody = $this->getDates("list");

		//footer of the XML
        $XMLfooter = "</channel>";
		$RSSfooter = "</rss>";

        //save the XML file
        $xmlbuild = $XMLheader;
		$xmlbuild .= $XMLheader2;
        $xmlbuild .= $XMLbody;
        $xmlbuild .= $XMLfooter;
        fwrite($newXMLfile, $xmlbuild);
        fclose($newXMLfile);

        //save the RSS file
        $rssbuild = $XMLheader;
        $rssbuild .= $RSSheader;
		$rssbuild .= $XMLheader2;
        $rssbuild .= $XMLbody;
        $rssbuild .= $XMLfooter;
		$rssbuild .= $RSSfooter;
        fwrite($newRSSfile, $rssbuild);
        fclose($newRSSfile);

       //convert to JSON for cross domain fuckup of XML. thanks obama
       //$this->ParseXMLToJSON($contentfile);
    }

    
    public function ParseXMLToJSON(string $file) {
        $fileContents= file_get_contents($file);
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