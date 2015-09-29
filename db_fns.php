<?php
    function db_connect() {
        $connection = new mysqli(MYSQL_HOSTNAME, USERNAME, PASSWORD, DATABASE);
        
        if (!$connection)
        {
          echo 'baglanamadi';
          mysql_error(); 
        }
 
        if (!$connection->select_db(DATABASE))
        {
          echo 'database bulunamadi'; 
          mysql_error();
        }
        
        $connection->query("SET NAMES UTF8");

        return $connection;    
    }
	
	function db_result_to_array($result) 
    {
        $res_array = array();
            
        for ($count = 0; $row = mysqli_fetch_assoc($result); $count++)
        {
            $res_array[$count] = $row;    
        }
        return $res_array;
    }
	
	function insert_video($video_id){
		$conn = db_connect();
		
		$query = "INSERT into video SET id = '$video_id'";

		$result = $conn->query($query);
		
		if(!$result){
			echo 'Dünyayý ele geçirmeye falan mý çalýþýyorsun?';
			echo $conn->error;
		}else{
			return $conn->insert_id;
		}
		$conn->close();		
	}
	
	function is_video_inserted($video_id){
		$conn = db_connect();
		
		$query = "SELECT id FROM video WHERE id = '$video_id'";
		
		$result = $conn->query($query);
		
		$result = $result->fetch_array();
		
		if($result)
		{
			return $result;
		}
		else
		{
			return false;
		}

		if(mysqli_ping($conn))
		{
			$conn->close(); 
		}
	}
	
	function insert_channel($credentials){
		$conn = db_connect();
		
		$query = "INSERT into channel SET credentials = '$credentials'";	

		$result = $conn->query($query);
		
		if(!$result){
			echo 'Dünyayý ele geçirmeye falan mý çalýþýyorsun?';
			echo $conn->error;
		}else{
			return $conn->insert_id;
		}
		$conn->close();		
	}
	
	function all_channels(){
		$conn = db_connect();
		
		$query = "SELECT * FROM channel WHERE is_active=1";
		
		$result = $conn->query($query);
		
		$result = db_result_to_array($result);
		
		return $result;
	}
?>