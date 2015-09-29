<?php
include('config.php');
include('db_fns.php');

$channels = all_channels();
foreach($channels as $channel){
	echo "<a href='http://www.youtube.com/channel/$channel[channel_id]'>$channel[channel_name]</a> <br />";
}
?>