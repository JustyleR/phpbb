<?php

if(!defined('file_access')) { exit(); }

// Get the username of the user by his ID
function get_username_byID($conn, $db_prefix, $id) {
	
	if(is_numeric($id)) {
		
		$get = mysqli_query($conn, "SELECT username FROM ". $db_prefix ."users WHERE user_id='". $id ."'");
		if(mysqli_num_rows($get) > 0) {
			
			return mysqli_fetch_assoc($get)['username'];
			
		}
		
	}
	
}

// Get the ID of the user by his username
function get_id_byUsername($conn, $db_prefix, $username) {
	
	$get = mysqli_query($conn, "SELECT user_id FROM ". $db_prefix ."users WHERE username='". $username ."'");
	if(mysqli_num_rows($get) > 0) {
		
		return mysqli_fetch_assoc($get)['user_id'];
		
	}
	
}

// Get user information
function user_profile($conn, $table_prefix, $user) {
	
	// Check if the user is called by id or username
	if(is_numeric($user)) { $user_type = 'num'; } else { $user_type = 'string'; }
	
	if($user_type == 'num') { $userCheck = mysqli_query($conn, "SELECT * FROM ". $table_prefix ."users WHERE user_id='". $user ."'"); }
	else if($user_type == 'string') { $userCheck = mysqli_query($conn, "SELECT user_id,username FROM ". $table_prefix ."users WHERE username='". $user ."'"); }
	
	if(mysqli_num_rows($userCheck) > 0) {
		
		$row = mysqli_fetch_assoc($userCheck);
		
		return $row;
		
	} else { return false; }
	
}

// Get user group
function user_group($conn, $table_prefix, $user) {
	
	// Check if the user is called by id or username
	if(is_numeric($user)) { $user_type = 'num'; } else { $user_type = 'string'; }
	
	if($user_type == 'num') { $userCheck = mysqli_query($conn, "SELECT * FROM ". $table_prefix ."users WHERE user_id='". $user ."'"); }
	else if($user_type == 'string') { $userCheck = mysqli_query($conn, "SELECT user_id,username FROM ". $table_prefix ."users WHERE username='". $user ."'"); }
	
	if(mysqli_num_rows($userCheck) > 0) {
		
		$row = mysqli_fetch_assoc($userCheck);
		
		// Get the group
		$group = mysqli_query($conn, "SELECT group_name,group_colour FROM ". $table_prefix ."groups WHERE group_id='". $row['group_id'] ."'");
		if(mysqli_num_rows($group) > 0) {
			
			$row = mysqli_fetch_assoc($group);
			
			return '<font color="'. $row['group_colour'] .'">' . $row['group_name'] . '</font>';
			
		}
		
	} else { return false; }
	
}