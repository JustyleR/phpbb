<?php

define('file_access', TRUE);
define('IN_PHPBB', true);
define('ROOT_PATH', "../");

if (!defined('IN_PHPBB') || !defined('ROOT_PATH')) { exit(); }

$phpEx = "php";
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH . '/';
include($phpbb_root_path . 'common.' . $phpEx);

require('config.php');
require('functions.php');

if(check_table($conn) == false) {
	die('Table doesnt exists!');
}

require('../includes/functions_display.php');

// Start session management
$user->session_begin();
$auth->acl($user->data);
$request->enable_super_globals();

if ($user->data['user_id'] == ANONYMOUS) { header('Location: ../'); }

if(isset($_GET['u'])) {
	
	$profile = user_profile($conn, $table_prefix, $_GET['u']);
	if($profile == false) { header('Location: ../'); }
	
}

$id	= $profile['user_id'];
//$avatar	= phpbb_get_user_avatar($profile); get avatar function

?>
<!doctype html>
<html lang="en">
		<head>
		
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<link rel="stylesheet" href="style/css/bootstrap.min.css">
		<link rel="stylesheet" href="style/css/custom.css">

		<title>Profile Comments</title>
	</head>
	
	<body>
	
	<div class="container">
		<h3><?php echo $profile['username']; ?>'s Profile</h3>
		<a class="btn btn-primary" href="../memberlist.php?mode=viewprofile&u=<?php echo $profile['user_id']; ?>">Go Back</a>
	</div>
	
	<div class="comments">
	<?php
	// Check the pages
	if(isset($_GET['p'])) {
		
		$page = $_GET['p'];
		
		if($page == 'home') {
			
			// Add comment
			echo '
			<div class="comment-wrap">
				<div class="photo">
					<div class="avatar"><img src="">'. phpbb_get_user_avatar(user_profile($conn, $table_prefix, $user->data['user_id'])) .'</img></div>
				</div>
				<div class="comment-block">
					<form action="" method="POST">
						<textarea maxlength="180" name="comment" id="" cols="30" rows="3" placeholder="Add comment..."></textarea>
						<input class="btn btn-success btn-sm" type="submit" name="add" value="Add Comment" />
					</form>
			';
			
			// Add comment script
			if(isset($_POST['add'])) {
				
				$comment	= mysqli_real_escape_string($conn, $_POST['comment']);
				$date		= date('d-m-Y H:i');
				$authID		= $user->data['user_id'];
				
				if(empty($comment)) { echo 'Please, fill the field!'; } else {
				
					// Check if the user has already commented within a minute (anti spam)
					$checkComment = mysqli_query($conn, "SELECT * FROM profile_comments WHERE author_id='". $authID ."' AND user_id='". $id ."' ORDER BY comment_id DESC");
					if(mysqli_num_rows($checkComment) > 0) {
						
						// Check if 1 minute has passed
						$cDate = mysqli_fetch_assoc($checkComment)['date'];
						$fDate = strtotime('+1 minute', strtotime($cDate));
						
						if(date('d-m-Y H:i', $fDate) < date('d-m-Y H:i')) { $continue = 1;} 
						
						else { $continue = 0; }
						
					} else { $continue = 1; }
					
					if($continue == 1) {
						
						mysqli_query($conn, "INSERT INTO profile_comments (user_id, author_id, comment, date) VALUES ('". $id ."','". $authID ."','". $comment ."','". $date ."')");
						
						header('refresh:0');
					} else {
						
						echo 'Please wait a minute!';
						
					}
				
				}
			}
			
			echo '
				</div>
			</div>
			';
			
			// Get comments
			$getComments = mysqli_query($conn, "SELECT * FROM profile_comments WHERE user_id='". $id ."' ORDER BY comment_id DESC");
			if(mysqli_num_rows($getComments) > 0) {
				
				$link = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '';
				
				if(!isset($_GET['cPage'])) { header('Location: '. $link .'&cPage=1'); }
				
				$cPage = $_GET['cPage'];
				
				if(!is_numeric($cPage) || $cPage == 0) { $cPage = 1; }
				
				$total			= mysqli_num_rows($getComments);
				$total_pages	= ceil($total / $comments_per_page);
				
				if($cPage > $total_pages) { $cPage = 1; }$total_pages	= ceil($total / $comments_per_page);
				
				$comments = $cPage * $comments_per_page - $comments_per_page;
				
				$getComments2 = mysqli_query($conn, "SELECT * FROM profile_comments WHERE user_id='". $id ."' ORDER BY comment_id DESC LIMIT ". $comments .", ". $comments_per_page ."");
				
				// Show the comments
				while($row = mysqli_fetch_assoc($getComments2)) {
					
					$authorName = get_username_byID($conn, $table_prefix, $row['author_id']);
					$group = str_replace('_', ' ', user_group($conn, $table_prefix, $row['author_id']));
					
					echo '
					<div class="comment-wrap">
						<div class="photo">
							<div class="avatar"><img src="">'. phpbb_get_user_avatar(user_profile($conn, $table_prefix, $row['author_id'])) .'</img></div>
						</div>
						<div class="comment-block">
							<p class="comment-text">
							'. $row['comment'] .'
							</p>
							<div class="bottom-comment">
								&nbsp; by <a href="../memberlist.php?mode=viewprofile&u='. $row['author_id'] .'">'. $authorName .'</a>
								('. $group .')';
								// Check if the user is an admin/moderator or it is the user's profile or its the user's own comment, if so then show the delete link
								if($auth->acl_getf_global('m_') || $auth->acl_getf_global('a_') || $row['author_id'] == $user->data['user_id'] || $id == $user->data['user_id']) {
								   
								   echo ' - <a href="index.php?p=delete&cPage='. $cPage .'&cid='. $row['comment_id'] .'">Delete</a>';
								
								}
								echo '
								<div class="comment-date">'. $row['date'] .'</div>
							</div>
						</div>
					</div>
					';
					
				}
				
				// Show the prev/next links
				echo '
				<footer class="footer">
					<div class="container">';
				if($cPage > 1) {
					
					$prev = $cPage - 1;
					echo '<a class="btn btn-primary btn-sm" href="index.php?p=home&u='. $id .'&cPage='. $prev .'">Prev</a> ';
					
				} else {
					
					echo '<a class="btn btn-outline-info btn-sm" href="">Prev</a> ';
					
				}
				
				if($cPage < $total_pages) {
					
					$next = $cPage + 1;
					echo '<a class="btn btn-primary btn-sm" href="index.php?p=home&u='. $id .'&cPage='. $next .'">Next</a>';
					
				} else {
					
					echo '<a class="btn btn-outline-info btn-sm" href="">Next</a>';
					
				}
				echo '
					</div>
				</footer>
				';
				
			} else {
				
				echo 'No comments!';
				
			}
			
		} else if($page == 'delete') {
			
			// Delete comment
			if(isset($_GET['cid'])) {
				
				if(is_numeric($_GET['cid'])) {
					
					$cid = (int)$_GET['cid'];
					
					// Check the comment
					$checkComment = mysqli_query($conn, "SELECT * FROM profile_comments WHERE comment_id='". $cid ."'");
					if(mysqli_num_rows($checkComment) > 0) {
						
						$row = mysqli_fetch_assoc($checkComment);
						
						// Check if the user is an admin/moderator or it is the user's profile or its the user's own comment, if so then delete the comment
						if($auth->acl_getf_global('m_') || $auth->acl_getf_global('a_') || $row['author_id'] == $user->data['user_id'] || $row['user_id'] == $user->data['user_id']) {
							
							mysqli_query($conn, "DELETE FROM profile_comments WHERE comment_id='". $cid ."'");
							
							if(isset($_GET['cPage'])) { $cPage = (int)$_GET['cPage']; } else { $cPage = 1; }
							header('Location: index.php?p=home&u='. $row['user_id'] .'&cPage='. $cPage .'');
							
						}
						
					} else { header('Location: ../'); }
					
				}
				
			}
			
		}
		
	} else { header('Location: ../'); }
	?>
	
	</div>
	
	<script src="style/js/bootstrap.min.js"></script>
	</body>
</html>