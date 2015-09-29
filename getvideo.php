<?php
include('config.php');
include('db_fns.php');
include('class/simple_html_dom.php');
include('getvideo_fns.php');
include('playlist.php');
include('upload.php');
//include('fb.php');

$channels = all_channels();
$tek_video = [];
foreach ($channels as $channel){
	if($channel[channel_name]=="Viral Dayi"){
		$tek_video = get_59saniye($channel[link]);
	}
	else{
		$tek_video = get_wimp($channel[link]);
	}
	
	foreach($tek_video as $t_video){
		$video_url = $t_video['url'];
		$video_url_name = $t_video['id'];
		$video_title = $t_video['title'];
		
		$sa = readfile_chunked( $video_url, $t_video, $retbytes = true );
		//$video = file_get_contents($video_url);
		//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/yt/tempvideo/'.$t_video['id'].'.mp4', $video);
		//$fb_video_id = fb_video_upload($t_video, $sess, $page_token);
		if($sa)
			video_upload($t_video, $channel[credentials], $fb_video_id=null);
	}
	//Tek video dizisini bir sonraki channel icin bosalttik.
	$tek_video = [];
	
	$files = glob($_SERVER['DOCUMENT_ROOT'].'/yt/tempvideo/*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}	
	
	print_r($tek_video);
}



?>