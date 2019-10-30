<?php

$data_dir='./da/';
$max_file=500;
if(!is_dir($data_dir)){
	mkdir($data_dir, 0777);
	touch($data_dir."index.html");
}

//mid+date+exipired
$now = time();
$count=0;
$vc = urlencode($_POST['vc']);
$mid = urlencode($_POST['mid']);
$expired = floor(urlencode($_POST['expired']));
$type = urlencode($_POST['type']);
if(!is_numeric($expired) || $expired < 1 || strlen($mid)<40 || strlen($mid)>64 ||
	   ($type!='text' && $type!='image')){
		die('{"response":"failure","message":"SERVER PAGE ERROR : bad request"}');	
}

if ($dir = opendir($data_dir)) {
    while (false !== ($file = readdir($dir))) {
        if ($file != "." && $file != ".." && $file != "index.html") {
        		list($fmid, $fdate, $fexipired, $ftype) = explode('.', $file);
        		if($fdate+$fexipired*60 < $now){
        			unlink($data_dir.$file);
        			//die("expired".$data_dir.$file);
        			$continue;
        		}
            $count++;
        }
    }
    closedir($dir);
}

if($max_file<=$count){
	die('{"response":"failure","message":"SERVER PAGE ERROR : too much files"}');	
}

if($expired > 1440){
  $expired = 1440;
}


	
	$filename = $vc.'.'.$mid.'.'.$now.'.'.$expired.'.'.$type ;
	$fp = fopen($data_dir.$filename,"w");
	fwrite($fp,$_POST['data']);
	fclose($fp);
	$remain = $expired*60;
	die('{"response":"success","message":"ok","remain":"'.$remain.'"}');	


?>
