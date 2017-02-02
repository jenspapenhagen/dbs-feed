<?php
include_once "Handler/fileHandler.php";

class timedateHandler{
    private $fileHandler;

    //construct
    function timedateHandler(){
        $this->fileHandler = new fileHandler();

    }

    function checkTime($time){
        if (preg_match('/\b\d{2}:\d{2}\b/', $time)) {
            return true;
        }else{
            return false;
        }
    }


    function RFC822Time($date){
        if(!$this->checkTime($date)){
            $output = "lalalala";
			return $output;
        }
        $input = strtotime($date);
        $output = gmdate(DATE_RFC822,$input);

        return $output;
    }


}