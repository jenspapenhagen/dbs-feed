<?php
class fileHandler{
    
    public function FileExists(string $file):bool{
        $filename = $file;
    
        if (file_exists($filename)) {
            return true;
        }else{
            return false;
        }
    }
    
    public function FileExistsWithPath(string $path,string $file):bool{
        $filename =  $path."/".$file;
    
        if (file_exists($filename)) {
            return true;
        }else{
            return false;
        }

    }
    
    public function XMLFileExistsWithPath(string $path,string $file):bool{
        $filename =  $path."/".$file.".xml";
    
        if (file_exists($filename)) {
            return true;
        }else{
            return false;
        }
    }
    
    
    public function WriteTxtFile(string $file, int $rights,string $content){
        $filename = $file.".txt";
        $fileName = fopen($filename,"w");
        fwrite($fileName, $content);
        fclose($fileName);
        chmod($filename, $rights);
     }
     
     public function WriteTxtFileWithPath(string $path, string $file,int $rights, string $content){
         $filename = $path."/".$file.".txt";
         $fileName = fopen($filename,"w");
         fwrite($fileName, $content);
         fclose($fileName);
         chmod($filename, $rights);
     }
     
     public function WriteXMLFileWithPathAndRights(string $path,string $file,int $rights,string $content){
         $filename = $path."/".$file.".xml";
         $handler = fopen($filename,"w");
         fwrite($handler,$content);
         fclose($handler);
         chmod($filename, $rights);
     }
     
     
    public function WriteTxtFileWithPathAndReturnCTimeAndSize(string $path,string $file,int $rights,string $content):array{        
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
    
    public function setFilerights(string $file):bool {
        chmod("$file", 755);
        if (!is_writable($file)){
            return false;
        }
		return true;
    }
    
}