<?php

require('config.php');
require('functions.php');

if(isset($_GET['u'])) {
	
	$user = $_GET['u'];
	if(is_numeric($user)) {
		
		$user_type = 'num';
		
	} else {
		
		$user_type = 'string';
		
	}
	
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
			
			while($row = mysqli_fetch_assoc($getComments)) {
				
				$authorName = get_username_byID($conn, $table_prefix, $row['author_id']); 
				
				echo '
				<div style="background: #e3e3e3;">
					<div style="background: silver; padding: 0.5%;">'. $authorName .'</div>
					<div style="padding: 0.5%;">
					<p>'. $row['comment'] .'</p></div>
				</div>
				';
				
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