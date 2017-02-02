<?php
include_once "Handler/fileHandler.php";

class curlHandler{
	private $fileHandler;

    //construct
    public function __construct(){
		$this->fileHandler = new fileHandler();
		
    }
	
	public function curlHandler(){
        self::__construct();
    }

    public function proxyList(): array{
        if(!$this->fileHandler->FileExists("proxy.txt")){
            return false;
        }
        //get random proxy form list
        $f_contents = file("proxy.txt");
        $line = $f_contents[rand(0, count($f_contents) - 1)];
        $port = explode(" ", $line);

        return $line;
    }

    public function proxyPort(array $line): string{
        $port = explode(" ", $line);
        $output = $port[1];

        return $output;
    }

    public function UserAgentList(): string{
        if(!$this->fileHandler->FileExists("browser.txt")){
            return false;
        }
        $f_contents = file("browser.txt");
        $randUserAgent = $f_contents[rand(0, count($f_contents) - 1)];

        return $randUserAgent;
    }

    public function getReferer(string $url): string{
        if(!$this->URLcheck($url)){
            return false;
        }
        $output = parse_url($url, PHP_URL_HOST);

        return $output;

    }


    public function URLcheck(string $url): string{
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            return true;
        } else {
            return false;
        }

    }

	public function curlWebsite(string $url, string $savefilename,int $proxy=0,int $randUserAgent=0) :bool{
			//File to save the contents to
			$filename = "content/".$savefilename.".txt";
			$fp = fopen ($filename, "w+");

            //url check
            if(!$this->URLcheck($url)){
                echo "URL wrong";
            }
			//Here is the file we are downloading, replace spaces with %20
			$ch = curl_init(str_replace(" ","%20",$url));

			if($randUserAgent != 0 and $this->UserAgentList()!= false){
				curl_setopt($ch, CURLOPT_USERAGENT,$this->UserAgentList());
			}else{
				curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
			}
			
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 50);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
			curl_setopt($ch, CURLOPT_REFERER, 'http://'.$this->getReferer($url));

			if($proxy != 0 and $this->proxyList() != false){
				curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
				curl_setopt($ch, CURLOPT_PROXY, $this->proxyList());
				curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort($this->proxyList()));
			}
			//give curl the file pointer so that it can write to it
			curl_setopt($ch, CURLOPT_FILE, $fp);
		
			$data = curl_exec($ch);//get curl response
		
			curl_close($ch);
			
		return true;
		}
	
	
}