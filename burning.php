<?php

$data_dir='./da/';
$max_file=5000;
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
		die('{"response":"failure","type":"mid error"}');	
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
	die('{"response":"success","type":"'.$ftype.'","remain":"'.$remain.'","data":"'.file_get_contents($data_dir.$file).'"}');
	unlink($data_dir.$file);
}
if($action == 'get' && $mid != $fmid){
	die('{"response":"failure","message":"no data found"}');	
}


if($action == 'make' && $max_file<=$count){
	die('{"response":"failure","message":"too much files"}');	
}
if($action == 'make'){
	$expired = $_POST['expired'];
	if(!is_numeric($expired) || $expired <= 0 || 
	   ($_POST['type']!='text' && $_POST['type']!='image')){
		die('{"response":"failure","message":"bad request"}');	
	}
	
	if($expired > 1440){
			$expired = 1440;
	}
	
	$filename = urlencode($_POST['mid']).'.'.$now.'.'.$expired.'.'.$_POST['type'];
	$fp = fopen($data_dir.$filename,"w");
	fwrite($fp,$_POST['data']);
	fclose($fp);
	$remain = $expired*60;
	die('{"response":"success","message":"ok","remain":"'.$remain.'"}');	
}



?>
