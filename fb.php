<?php
session_start();
require ($_SERVER['DOCUMENT_ROOT'].'/yt/facebook-php-sdk-v4-4.0-dev/autoload.php');

$page_id = '392969390765480';
/* USE NAMESPACES */
	
	use Facebook\FacebookSession;
	use Facebook\FacebookRedirectLoginHelper;
	use Facebook\FacebookRequest;
	use Facebook\FacebookResponse;
	use Facebook\FacebookSDKException;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookAuthorizationException;
	use Facebook\GraphObject;
	use Facebook\GraphUser;
	use Facebook\GraphSessionInfo;
	use Facebook\FacebookHttpable;
	use Facebook\FacebookCurlHttpClient;
	use Facebook\FacebookCurl;
/*PROCESS*/
	
	//1.Stat Session
	 session_start();
	//check if users wants to logout
	 if(isset($_REQUEST['logout'])){
	 	unset($_SESSION['fb_token']);
	 }
	// Initialize application by Application ID and Secret
	FacebookSession::setDefaultApplication('386175114776484','3fefd340857a81e33d324631f6887716');
	
	$helper = new FacebookRedirectLoginHelper('http://www.cruisear.com/yt/fb.php');
	$sess = $helper->getSessionFromRedirect();
	
	//check if facebook session exists
	 	$sess = new FacebookSession('CAAFfOWaJj6QBAIMWm8C3HogIo5g5RJGR5SGg0caWHr619iZAqg17qw71EbGLMBxVRkOecYAZBIfLqCgydVad1xXrllhC2V5mgZAsSB8hDFsKEKUX89jkagXAxwKGoeSAZB1uirHuYZAjUCrQ7fX2XkuD4JRUbQSm0unDL5ErzZCZCRBODPggOXuiSY1PtXkGzroYue32AtMSp3eif0JQYuh');
	
	//logout
	$logout = 'http://www.cruisear.com/yt/fb.php?logout=true';
	//4. if fb sess exists echo name 
	 	if(isset($sess)){
			
			// get page access token
			$page_token = (new FacebookRequest( $sess, 'GET', '/' . $page_id,  array( 'fields' => 'access_token' ) ))
			->execute()->getGraphObject()->asArray();

			echo "<a href='".$logout."'><button>Logout</button></a>";
			
			echo "page token: $page_token[access_token]<br>";
			
			function fb_video_upload($t_video, $sess, $page_token){
				// post to page
				$page_post = (new FacebookRequest( $sess, 'POST', '/'. '392969390765480' .'/videos', array(
					'access_token' => $page_token[access_token],
					'description' => $t_video[title].'. '.$t_video[description_fb],
					'source' => '@'.$_SERVER[DOCUMENT_ROOT].'/yt/tempvideo/'.$t_video[id].'.mp4'
				  ) ))->execute()->getGraphObject()->asArray();

				// return post_id
				return $page_post[id];
			}
	 	}else{
			//else echo login
	 		echo '<a href="'.$helper->getLoginUrl(array('email', 'manage_pages', 'read_stream')).'" >Login with facebook</a>';
	 	}

?>