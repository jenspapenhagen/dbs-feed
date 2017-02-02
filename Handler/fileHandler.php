<?php
class fileHandler{
    
    function FileExists($file){
        $filename = $file.".txt";
    
        if (file_exists($filename)) {
            return true;
        }else{
            return false;
        }
    }
    
    function FileExistsWithPath($path, $file){
        $filename =  $path."/".$file.".txt";
    
        if (file_exists($filename)) {
            return true;
        }else{
            return false;
        }

    }
    
    function XMLFileExistsWithPath($path, $file){
        $filename =  $path."/".$file.".xml";
    
        if (file_exists($filename)) {
            return true;
        }else{
            return false;
        }
    }
    
    
    function WriteTxtFile($file, $rights, $content){
        $filename = $file.".txt";
        $fileName = fopen($filename,"w");
        fwrite($fileName, $content);
        fclose($fileName);
        chmod($filename, $rights);
     }
     
     function WriteTxtFileWithPath($path, $file, $rights, $content){
         $filename = $path."/".$file.".txt";
         $fileName = fopen($filename,"w");
         fwrite($fileName, $content);
         fclose($fileName);
         chmod($filename, $rights);
     }
     
     function WriteXMLFileWithPathAndRights($path, $file, $rights, $content){
         $filename = $path."/".$file.".xml";
         $handler = fopen($filename,"w");
         fwrite($handler,$content);
         fclose($handler);
         chmod($filename, $rights);
     }
     
     
    function WriteTxtFileWithPathAndReturnCTimeAndSize($path, $file, $rights, $content){        
    	$filename = $path."/".$file.".txt";
        $handler = fopen($filename,"w");
        fwrite($handler,$content);
        fclose($handler);
        chmod($filename, $rights);
        
        $fileCreateTime = filectime($filename);
        $fileSizeInByte = filesize($filename);
        $propertyArray= array();
        $propertyArray["size"] =  $fileSizeInByte;
        $propertyArray["ctime"] =  $fileCreateTime;
        
    return $propertyArray;
    }
    
//chmod explaination
/*
Value    Permission Level
400    Owner Read
200    Owner Write
100    Owner Execute
 40    Group Read
 20    Group Write
 10    Group Execute
  4    Global Read
  2    Global Write
  1    Global Execute
*/
    
    function setFilerights($file) {
        chmod("$file", 755);
        if (!is_writable($file)){
            return false;
        }
    }
    
}