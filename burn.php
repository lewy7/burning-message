<?php

$data_dir='./da/';
if(!is_dir($data_dir)){
	mkdir($data_dir, 0777);
	touch($data_dir."index.html");
}

//mid+date+exipired
$now = time();
$count=0;
$mid = '';
$action=$_GET['action'];
if($action == 'get'){
	$mid = $_GET['mid'];
	if(strlen($mid)<30)
		die('{"response":"failure","type":"SERVER PAGE ERROR : bad request"}');	
}


if ($dir = opendir($data_dir)) {
    while (false !== ($file = readdir($dir))) {
        if ($file != "." && $file != ".." && $file != "index.html") {
        		list($fmid, $fdate, $fexipired, $ftype) = explode('.', $file);
        		if($fdate+$fexipired*60 < $now){
        			unlink($data_dir.$file);
        			//die("expired".$data_dir.$file);
        			$fmid = 'x';
        			$continue;
        		}
        		if($mid == $fmid ){
        			break;	
        		}
            $count++;
        }
    }
    closedir($dir);
}
if($action == 'get' && $mid == $fmid){
	$remain = $fdate+$fexipired*60 - $now;
	echo('{"response":"success","type":"'.$ftype.'","remain":"'.$remain.'","data":"'.file_get_contents($data_dir.$file).'"}');	
	unlink($data_dir.$file);
}
if($action == 'get' && $mid != $fmid){
	die('{"response":"failure","message":"SERVER PAGE ERROR : no data"}');	
}


?>