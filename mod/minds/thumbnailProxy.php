<?php

set_time_limit (1); //don't spend longer than 3 seconds
ini_set('max_execution_time', 1); 

$src = urldecode(get_input('src'));
//$src = "https:$src";      
//forward($src);return;
if(strpos($src, 'http') === FALSE){
	$src = "https:$src";
}

//get the original file

$ch = curl_init($src);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch,CURLOPT_TIMEOUT_MS,1000);
$image = curl_exec($ch);
$errorno = curl_errno($ch);
curl_close($ch);

if($errorno){
	var_dump($errorno);
	die();
}

if(!$image){
return false;
}else{
$filename = '/tmp/'.time().rand();
file_put_contents($filename, $image);
//chmod($filename, fileperms($filename) | 128 + 16 + 2);
$image = @imagecreatefromstring($image);
}

if(!$image){
	@unlink($filename);
	forward($src);
	return false;
}

header('Expires: ' . date('r',  strtotime("today+6 months")), true);
header("Pragma: public");
header("Cache-Control: public");

// Get new dimensions
$width = imagesx($image);
$height = imagesy($image);
$new_width = get_input('width', 400);
if($width == 0 || $height == 0){
	@unlink($filename);
	forward($src);
	return;
}

$ratio = $width / $height;
$new_height = $new_width / $ratio;

if($width <= 1 || $height <= 1){
	$new_width = 1;
	$new_height = 1;
}

if(get_input('height')){
	$new_height = get_input('height');
}

if(get_input('width') == 'auto'){
	$new_width = $width;
	$new_height = $height;
}

// Resample
$image_p = imagecreatetruecolor($new_width, $new_height);

$mime = @getimagesize($filename);
$mime = $mime['mime'];
switch($mime){
	case 'image/gif':
	//$image = imagecreatefromgif($src);
	//WE WANT TO HAVE COOL GIFS!
//	header('Content-type: image/gif');
//	readfile($src);
		@unlink($filename);
		forward($src);
	return;
	break;
	case 'image/png':
		$image = @imagecreatefrompng($filename);
	break;
	case 'image/bmp':
	case 'image/jpeg':
	default:
		$image = @imagecreatefromjpeg($filename);
}
if(!$image){
	//we couldn't get the images, just output directly
	//header('Content-type: image/jpeg');
	 @unlink($filename);
	forward($src); 
	return;
}
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

header('Content-type: image/jpeg');
header('Access-Control-Allow-Origin: *');
// Output
imagejpeg($image_p, null, 75);
	@unlink($filename);

exit;
