<?php

function get_username_byID($conn, $db_prefix, $id) {
	
	if(is_numeric($id)) {
		
		$get = mysqli_query($conn, "SELECT username FROM ". $db_prefix ."users WHERE user_id='". $id ."'");
		if(mysqli_num_rows($get) > 0) {
			
			return mysqli_fetch_assoc($get)['username'];
			
		}
		
	}
	
}

function get_id_byUsername($conn, $db_prefix, $username) {
	
	$get = mysqli_query($conn, "SELECT user_id FROM ". $db_prefix ."users WHERE username='". $username ."'");
	if(mysqli_num_rows($get) > 0) {
		
		return mysqli_fetch_assoc($get)['user_id'];
		
	}
	
}