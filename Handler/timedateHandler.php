<?php
include_once "Handler/fileHandler.php";

class timedateHandler{
    private $fileHandler;

    //construct
    public function __construct(){
        $this->fileHandler = new fileHandler();

    }
	
	public function timedateHandler(){
        self::__construct();
    }
	

    public function checkTime(string $time) : bool {
        if (preg_match('/\b\d{2}:\d{2}\b/', $time)) {
            return true;
        }else{
            return false;
        }
    }


    public function RFC822Time(string $date) : string{
        if(!$this->checkTime($date)){
            $output = "lalalala";
			return $output;
        }
        $input = strtotime($date);
        $output = gmdate(DATE_RFC822,$input);

        return $output;
    }


}