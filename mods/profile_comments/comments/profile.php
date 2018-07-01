<?php

// Profile page

require('config.php');
require('functions.php');

if(isset($_GET['u'])) {
	
	$user = (int)$_GET['u'];
	// Check if the user is called by id or username
	if(is_numeric($user)) { $user_type = 'num'; } else { $user_type = 'string'; }
	
	if($user_type == 'num') { $userCheck = mysqli_query($conn, "SELECT user_id,username FROM ". $table_prefix ."users WHERE user_id='". $user ."'"); }
	else if($user_type == 'string') { $userCheck = mysqli_query($conn, "SELECT user_id,username FROM ". $table_prefix ."users WHERE username='". $user ."'"); }
	
	if(mysqli_num_rows($userCheck) > 0) {
		
		$row	= mysqli_fetch_assoc($userCheck);
		$id		= $row['user_id'];
		
		echo 'Profile: <strong>'. $row['username'] .'</strong> - <a href="../memberlist.php?mode=viewprofile&u='. $id .'">Go Back</a>
		<hr />';
		
		$getComments = mysqli_query($conn, "SELECT * FROM profile_comments WHERE user_id='". $id ."' ORDER BY comment_id DESC");
		if(mysqli_num_rows($getComments) > 0) {
			
			echo '<a href="add_comment.php?id='.$id.'">Add comment</a><br /><br />';
			
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
			$request->enable_super_globals();
			
			$link = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '';
			
			if(!isset($_GET['cPage'])) { header('Location: '. $link .'&cPage=1'); }
			
			$page = $_GET['cPage'];
			
			if(!is_numeric($page) || $page == 0) { $page = 1; }
			
			$total			= mysqli_num_rows($getComments);
			$total_pages	= ceil($total / $comments_per_page);
			
			if($page > $total_pages) { $page = 1; }
			
			$comments = $page * $comments_per_page - $comments_per_page;
			
			$getComments2 = mysqli_query($conn, "SELECT * FROM profile_comments WHERE user_id='". $id ."' ORDER BY comment_id DESC LIMIT ". $comments .", ". $comments_per_page ."");
			
			while($row = mysqli_fetch_assoc($getComments2)) {
				
				$authorName = get_username_byID($conn, $table_prefix, $row['author_id']); 
				
				echo '
				<div style="background: #e3e3e3;">
					<div style="background: silver; padding: 0.5%;">
					<a href="profile.php?u='. $row['author_id'] .'">'. $authorName .'</a> ('. $row['date'] .')';
					
					// Check if the user is an admin/moderator or it is the user's profile or its the user's own comment, if so then show the delete link
					if($auth->acl_getf_global('m_') || $auth->acl_getf_global('a_') || $row['author_id'] == $user->data['user_id'] || $id == $user->data['user_id']) {
					   
					   echo ' - <a href="delete.php?cid='. $row['comment_id'] .'">Delete</a>';
					
					} 
					
					echo '
					</div>
					<div style="padding: 0.5%;">
					<p>'. $row['comment'] .'</p></div>
				</div>
				';
				
			}
			
			if($page > 1) {
				
				$prev = $page - 1;
				echo '<a href="profile.php?u='. $id .'&cPage='. $prev .'">Prev</a> - ';
				
			} else {
				
				echo 'Prev - ';
				
			}
			
			if($page < $total_pages) {
				
				$next = $page + 1;
				echo '<a href="profile.php?u='. $id .'&cPage='. $next .'">Next</a>';
				
			} else {
				
				echo 'Next';
				
			}
			
		} else {
			
			echo 'No comments..<br />
			<a href="add_comment.php?id='.$id.'">Add a comment?</a>';
			
		}
		
		
	} else {
		
		echo 'User not found!';
		
	}
	
} else {
	header('Location: ../');
}