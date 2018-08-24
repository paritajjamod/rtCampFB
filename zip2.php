<?php
include 'class.facebook-gallery.php';

	
	session_start();
	$a=$_SESSION['f2'];
	$b=$_SESSION['f3'];
	$files = $a; /*Image array*/
 
# create new zip opbject
$zip = new ZipArchive();
 
# create a temp file & open it
$tmp_file = $b;
$zip->open($tmp_file, ZipArchive::CREATE);
 
# loop through each file
foreach($files as $file){
 
    # download file
    $download_file = file_get_contents($file['name'], $file['id']);
 
    #add it to the zip
    $zip->addFromString(basename($file),$download_file);
 
}
 
# close zip
$zip->close();
 
# send the file to the browser as a download
header("Content-Length: " . filesize($file));
header('Content-disposition: attachment; filename=download.zip');
header('Content-type: application/zip');
readfile($tmp_file);

    
    

    
     
					
	
	
								
			

			
	
?>