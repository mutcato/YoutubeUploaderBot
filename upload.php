<?php

	session_start();
	 
	set_include_path($_SERVER['DOCUMENT_ROOT'] . '/yt/google-api-php-client-master/src/');
	require_once 'Google/Client.php';
	require_once 'Google/Service/YouTube.php';

function video_upload($tek_video, $credentials, $fb_video_id = null){	 

	$application_name = 'YTuploader'; 
	$client_secret = '****';
	$client_id = '*0t2365oe65nt2opig2************.apps.googleusercontent.com';
	$scope = array('https://www.googleapis.com/auth/youtube.upload', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/youtubepartner');
			 
	$videoPath = $_SERVER['DOCUMENT_ROOT']."/yt/tempvideo/".$tek_video['id'].'.mp4';
	$videoTitle = $tek_video['title'];
	//$videoDescription = 'Bu vidyonun Facebook linki: https://www.facebook.com/video.php?v='.$fb_video_id;	
	$videoDescription .= $tek_video['description_yt'];
	$videoCategory = "22";
	$videoTags = $tek_video['tags'];
	 
	try{	
		// Client init
		$client = new Google_Client();
		$client->setApplicationName($application_name);
		$client->setClientId($client_id);
		$client->setScopes($scope);
		$client->setClientSecret($client_secret);
		$acc = $client->setAccessToken($credentials);
	 
		if ($client->getAccessToken()) {
	 
			/**
			 * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
			 */
			if($client->isAccessTokenExpired()) {
				$newToken = json_decode($acc);
				echo "<br/>Credentials: ".$credentials."<br/>";
				echo "<br/>$client->setAccessToken($credentials): ".$acc."<br/>";
				$client->refreshToken(json_decode($credentials)->refresh_token);
				$_SESSION['token'] = $client->getAccessToken();
				echo "<br/>New Token: ".$_SESSION['token']."<br/>";
			}
	
			$youtube = new Google_Service_YouTube($client);
	 
	 
	 
			// Create a snipet with title, description, tags and category id
			$snippet = new Google_Service_YouTube_VideoSnippet();
			$snippet->setTitle($videoTitle);
			$snippet->setDescription($videoDescription);
			$snippet->setCategoryId($videoCategory);
			$snippet->setTags($videoTags);
	 
			// Create a video status with privacy status. Options are "public", "private" and "unlisted".
			$status = new Google_Service_YouTube_VideoStatus();
			$status->setPrivacyStatus('public');
	 
			// Create a YouTube video with snippet and status
			$video = new Google_Service_YouTube_Video();
			$video->setSnippet($snippet);
			$video->setStatus($status);
	 
			// Size of each chunk of data in bytes. Setting it higher leads faster upload (less chunks,
			// for reliable connections). Setting it lower leads better recovery (fine-grained chunks)
			$chunkSizeBytes = 1 * 128 * 1024;
	 
			// Setting the defer flag to true tells the client to return a request which can be called
			// with ->execute(); instead of making the API call immediately.
			$client->setDefer(true);
	 
			// Create a request for the API's videos.insert method to create and upload the video.
			$insertRequest = $youtube->videos->insert("status,snippet", $video);
	 
			// Create a MediaFileUpload object for resumable uploads.
			$media = new Google_Http_MediaFileUpload(
				$client,
				$insertRequest,
				'video/*',
				null,
				true,
				$chunkSizeBytes
			);
			$media->setFileSize(filesize($videoPath));
	 
	 
			// Read the media file and upload it chunk by chunk.
			$status = false;
			$handle = fopen($videoPath, "rb");
			while (!$status && !feof($handle)) {
				$chunk = fread($handle, $chunkSizeBytes);
				$status = $media->nextChunk($chunk);
			}
	 
			fclose($handle);
	 
			
			 //Vidoe has successfully been upload, now lets perform some cleanup functions for this video
			 
			if ($status->status['uploadStatus'] == 'uploaded') {
				//Veritabanına gir
				echo $status['id'].'<br />';
				insert_video($tek_video['id']);
				playlist_add($status['id']);
				
			}
	 
			// If you want to make other calls after the file upload, set setDefer back to false
			$client->setDefer(true);
	 
		} else{
			// @TODO Log error
			echo 'Problems creating the client!+%&!';
		}
	 
	} catch(Google_Service_Exception $e) {
		print "Caught Google service Exception 1 ".$e->getCode(). " message is ".$e->getMessage();
		print "Stack trace is ".$e->getTraceAsString();
	}catch (Exception $e) {
		print "Caught Google service Exception 2 ".$e->getCode(). " message is ".$e->getMessage();
		print "Stack trace is ".$e->getTraceAsString();
	}

}
?>