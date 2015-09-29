# YoutubeUploaderBot
PHP Youtube video uploader bot

1/Make a config.php file<br />
<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 'on');

/* Mysql Settings */
define('MYSQL_HOSTNAME', 'localhost');
define('USERNAME', 'cruisear');
define('PASSWORD', 'PASSWORD');
define('DATABASE', 'cruisear_ytupload');

//Google Api
define('API_KEY', 'AIzaSyD0u9FtGD************');

?>
<br />
2/Build db tables <br />
3/Connect getvideo.php file to cronjob
