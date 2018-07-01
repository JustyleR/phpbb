<?php

define('file_access', TRUE);

require('config.php');
require('functions.php');

if(check_table($conn) == false) {
	die('Table doesnt exists!');
}

?>
<link rel="stylesheet" href="comments/style/css/forum.css">
<?php
$id	= (int)$_GET['u'];

$getComments = mysqli_query($conn, "SELECT * FROM profile_comments WHERE user_id='". $id ."' ORDER BY comment_id DESC");
if(mysqli_num_rows($getComments) > 0) {

	$getComments = mysqli_query($conn, "SELECT * FROM profile_comments WHERE user_id='". $id ."' ORDER BY comment_id DESC LIMIT ". $last_comments_max ."");
	
	// Show the comments
	while($row = mysqli_fetch_assoc($getComments)) {
		
		$authorName = get_username_byID($conn, $table_prefix, $row['author_id']);
		$group = str_replace('_', ' ', user_group($conn, $table_prefix, $row['author_id']));
		
		echo '
		<div class="comment">
			<div class="comment-info">'. $row['date'] .' by <font color="'. user_color($conn, $table_prefix, $row['author_id']) .'">'. $authorName .'</font></div>
		<div class="comment-text">'. $row['comment'] .'</div>
		</div>
		';
		
	}
	
} else {
	
	echo 'No comments.<br />
	<a href="comments/index.php?p=home&u='. $id .'">Add Comment?</a>';
	
}