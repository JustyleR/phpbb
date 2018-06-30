<?php

// Delete a comment

if(isset($_GET['cid'])) {
	
	if(is_numeric($_GET['cid'])) {
		
		$cid = $_GET['cid'];
		
		require('config.php');
		
		define('IN_PHPBB', true);
		define('ROOT_PATH', "../");

		if (!defined('IN_PHPBB') || !defined('ROOT_PATH')) {
			exit();
		}

		$phpEx = "php";
		$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH . '/';
		include($phpbb_root_path . 'common.' . $phpEx);
		
		// Start session management
		$user->session_begin();
		$auth->acl($user->data);
		
		// Check the comment
		$checkComment = mysqli_query($conn, "SELECT * FROM profile_comments WHERE comment_id='". $cid ."'");
		if(mysqli_num_rows($checkComment) > 0) {
			
			$row = mysqli_fetch_assoc($checkComment);
			
			// Check if the user is an admin/moderator or it is the user's profile or its the user's own comment, if so then delete the comment
			if($auth->acl_getf_global('m_') || $auth->acl_getf_global('a_') || $row['author_id'] == $user->data['user_id'] || $row['user_id'] == $user->data['user_id']) {
				
				mysqli_query($conn, "DELETE FROM profile_comments WHERE comment_id='". $cid ."'");
				echo 'The commend was deleted!';
				
				header('refresh:1; url=profile.php?u='. $row['user_id'] .'');
				
			}
			
		}
		
	} else { header('Location: ../'); }
	
} else { header('Location: ../'); }