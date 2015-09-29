<?php
function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}

function get_filesize($file){

    $ch = curl_init($file);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $data = curl_exec($ch);
    
	$filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
	
	curl_close($ch);
	
	return $filesize;
}

function get_59saniye($link){
	$html = file_get_html($link);
	foreach( $html->find('ul.list li') as $key => $li){
		$vid = preg_replace('/^.+[\\\\\\/]/', '', $li->find('img', 0)->src);
		$vid = substr($vid,0,strlen($vid)-4);

		if(!is_video_inserted($vid)){
			$tek_video[$key]['id'] = $vid;
			$tek_video[$key]['title'] = $li->find('a', 0)->title." (açıklamaya bi' bak)";
			$tek_video[$key]['sayfa'] = $li->find('a', 0)->href;
			
			$tek_video_html = file_get_contents('http:'.$tek_video[$key]['sayfa']);
			///2015/02/06/20150206110228-3230_240 ???? al??. Ba?? urlyi sonuna da .mp4'??liyor.
			$vi = get_string_between($tek_video_html, '//static.59saniye.com/videos', '.mp4');		
			$tek_video[$key]['url'] = 'http://static.59saniye.com/videos'.$vi.'.mp4'; 
			
			$tek_video_html = file_get_html('http:'.$tek_video[$key]['sayfa']);
			
			//$tags dizisini bo????. Bo??mazsak ??ki videonun etiketleri sonraki videonun etiketleri ile birle??r.
			$tags= array();
			$desc = $tek_video_html->find('#info',0)->plaintext;
			$tek_video[$key]['description_yt'] = 'Sağcı mısın solcu mu? http://www.banabenianlat.net/index.php?view=test&testid=1

Ne zaman öleceksin? http://www.banabenianlat.net/index.php?view=test&testid=2#0

İnsan sarrafı mısın? http://www.banabenianlat.net/index.php?view=test&testid=4#0

Facebook sayfamızı takibe almayan internet gündemini kaçırıyor. http://bit.ly/viraldayi
			
			'.$desc;
			$tek_video[$key]['description_fb'] = $desc;
			foreach ($tek_video_html->find('.hash') as $i => $t){
					$tags[$i] = str_replace('#','',$t->plaintext);
			}
			$tek_video[$key]['tags'] = $tags;
		}

	}
	return $tek_video;
}

function get_wimp($link){
	$html_wimp = file_get_html($link);
	foreach($html_wimp->find('.latest-third') as $key => $lt){
		$videothumb = $lt->find('.video-thumb',0);
		$vid = preg_replace('/^.+[\\\\\\/]/', '', $videothumb->find('img', 0)->src);
		$vid = substr($vid,0,strlen($vid)-4);
		
		if(!is_video_inserted($vid)){
			$sayfa = 'www.wimp.com'.$videothumb->find('a',0)->href;
			$title = trim($lt->find('h2', 0)->plaintext)." ytmdb.com";
			
			$tek_video_html = file_get_html('http://'.$sayfa);
			$vi = get_string_between($tek_video_html, '"file": "http://', '.flv');	
			if($vi){
				$tek_video[$key]['id'] = $vid;
				$tek_video[$key]['sayfa'] = $sayfa;
				$tek_video[$key]['title'] = $title;
				$tek_video[$key]['url'] = 'http://'.$vi.'.flv';

				$tek_video[$key]['description_yt'] = $tek_video_html->find('.video-desc',0)->plaintext;
				$tek_video[$key]['tags'] = explode(' ', $tek_video[$key]['title']);
			}
			 
		}
	}
	return	$tek_video;
	
}

function readfile_chunked( $filename, $video, $retbytes = true ) { 
	$maxfilesize = 10*1024*1024; // get videos only which smaller than 10M
    $chunksize = (1/2) * (1024 * 1024); // how many bytes per chunk 
    $buffer = ''; 
    $cnt = 0; 
    $handle = fopen( $filename, 'rb' ); 
    if ( $handle === false ) { 
        return false; 
    } 
	echo filesize($filename);
    ob_end_clean(); //added to fix ZIP file corruption 
    ob_start(); //added to fix ZIP file corruption 
    header( 'Content-Type:' ); //added to fix ZIP file corruption 
	
	//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/yt/tempvideo/'.$video['id'].'.mp4', $reklam_handle,FILE_APPEND);
	
    while ( !feof( $handle ) ) { 
		if(get_filesize($video['url']) < $maxfilesize){
			$buffer = fread( $handle, $chunksize ); 

			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/yt/tempvideo/'.$video['id'].'.mp4', $buffer, FILE_APPEND);
			
			ob_flush(); 
			flush(); 
			if ( $retbytes ) { 
				$cnt += strlen( $buffer ); 
			} 
		}
		else{
			fclose( $handle );
			return false;
		}
    }

	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/yt/tempvideo/'.$video['id'].'.mp4', $reklam_handle, FILE_APPEND);
	
    $status = fclose( $handle ); 
    if ( $retbytes && $status ) { 
        return $cnt; // return num. bytes delivered like readfile() does. 
    } 
    return $status; 
}


?>