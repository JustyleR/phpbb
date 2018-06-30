<?php

if(isset($_GET['id'])) {
	
	if(is_numeric($_GET['id'])) {
		
		require('config.php');
		require('functions.php');
		
		$id = $_GET['id'];
		
		define('IN_PHPBB', true);
		define('ROOT_PATH', "../");

		if (!defined('IN_PHPBB') || !defined('ROOT_PATH')) {
			
			exit();
			
		}

		$phpEx = "php";
		$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH . '/';
		include($phpbb_root_path . 'common.' . $phpEx);
		$request->enable_super_globals();

		$user->session_begin();

		if ($user->data['user_id'] == ANONYMOUS) {
			
		   header('Location: ../');
		   
		} else {
			
			$authorID = get_id_byUsername($conn, $table_prefix, $user->data['username']);
			
			echo '
			<form action="" method="POST">
				<textarea style="resize: none;" name="comment" rows="7" cols="32"></textarea><br />
				<input type="submit" name="add" value="Add" />
			</form>
			';
			
			if(isset($_POST['add'])) {
				
				$comment	= mysqli_real_escape_string($conn, $_POST['comment']);
				$date		= date('d-m-Y H:i');
				
				if(empty($comment)) {
					
					echo 'No empty comments!';
					
				} else {
					
					$checkUser = mysqli_query($conn, "SELECT user_id FROM ". $table_prefix ."users WHERE user_id='". $id ."'");
					if(mysqli_num_rows($checkUser) > 0) {
						
						mysqli_query($conn, "INSERT INTO profile_comments (user_id, author_id, comment, date) VALUES ('". $id ."','". $authorID ."','". $comment ."','". $date ."')");
						
						echo 'Comment added successfully!';
						header('refresh:1; url=profile.php?u='.$id.'');
						
					} else {
						
						echo 'User was not found!';
						
					}
					
				}
				
			}
			
			$request->disable_super_globals();
			
		}
		
	}
	
} else {
	
	header('Location: ../');
	
}