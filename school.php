<?php

include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

sec_session_start();

$id = $_GET['id'];
$sortByClass = true;

if (isset($_GET['class'])) {
  $sortByClass = $_GET['class'];
}

$school = getSchool($mysqli, $id);
$schoolName = $school["name"];
$schoolLocation = $school["location"];
$user = getMember($mysqli, $_SESSION['user_id']);
$members = getMembersInSchool($mysqli, $id);

if ($sortByClass == "true") {
	$messages = getBoardMessagesWithGraduationYear($mysqli, $id, $user['graduationYear'], 'school');
} else if ($sortByClass == "false") {
	$messages = getBoardMessages($mysqli, $id, 'school');
}
$groups = getGroups($mysqli, $id);
?>

<!DOCTYPE html>
<html>
	<head>
		<title>After School</title>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/main.css" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A==" crossorigin="anonymous"></script>
		<script src="js/schoolAjax.js"></script> 
		<script src="js/board.js"></script>
		<script>
			var schoolUrlId = <?php echo $id; ?>;

			$(document).ready(function() {
				 $('#class_checkbox').click(function(e) {
					 if (this.checked) {
						 window.location.href = 'school.php?id=' + schoolUrlId;
					 } else {
						 window.location.href = 'school.php?id=' + schoolUrlId + '&class=false';
					 }
				 });
			});
			</script>
	</head>
	<body>
		<h1><?php echo $schoolName; ?></h1>
		<h4><?php echo $schoolLocation; ?></h4>
		<h2>Members</h2>
		<ul>
			<?php foreach ($members as $i => $member) {
				$image = getUserAvatar($mysqli, $member["id"]);
				$member = array_merge($member, array("image" => $image));
				$members[$i] = $member; ?>
				
				<li>
					<a href="user.php?id=<?php echo $member["id"]; ?>">
						<img id="user_profile" width="50" height="50" src="<?php echo $member["image"]; ?>" />
						<?php echo $member["firstName"] . " " . $member["lastName"]; ?>
					</a>
				</li>
			<?php } ?>
		</ul> 
		<h2>Board</h2>
		<input id="class_checkbox" type="checkbox" <?php if ($sortByClass == "true") echo 'checked'; ?>>Class's messages only
		<ul>
			<?php 
			
			function isMemberCached($id, $members) {
				foreach ($members as $member) {
					if ($member["id"] == $id) {
						return $member;
					}
				}
				
				return false;
			}
			
			foreach ($messages as $i => $message) {
				$image = $message["author"]["image"];
				$encodedId = convert_uuencode($message["id"]);
				$name = $message["author"]["firstName"] . " " . $message["author"]["lastName"]; 
				$replies = getBoardMessageReplies($mysqli, $message["id"]); 
				?>
				<li id="message_<?php echo $i; ?>" class="message" onclick="toggleReplyVisibility('#board_reply_form_<?php echo $i; ?>', '#reply_note_<?php echo $i; ?>');">
					<h3><?php echo $message["title"]; ?></h3>
					<h4><?php echo $message["body"]; ?></h4>
					<p>
						<a href="user.php?id=<?php echo $message["author"]["id"]; ?>">
							<img id="user_profile" width="25" height="25" src="<?php echo $image; ?>" />
							<?php echo $name; ?>
						</a>
					on <?php echo date("l, F jS", strtotime($message["dt"])); ?></p>
					
					<ul class="replies" style="margin-left: 50px;">
						<?php foreach ($replies as $reply) {
							$name = $reply["author"]["firstName"] . " " . $reply["author"]["lastName"]; 
							$replyImage = $reply["author"]["image"] ?>
							
							<li>
								<h5><?php echo $reply["body"] ?></h5>
								<p>
									<a href="user.php?id=<?php echo $reply["author"]["id"]; ?>">
										<img id="user_profile" width="25" height="25" src="<?php echo $replyImage; ?>" />
										<?php echo $name; ?>
									</a>
								on <?php echo $message["dt"]; ?></p>
							</li>
						<?php } ?>
					</ul>
					
					<em id="reply_note_<?php echo $i; ?>" class="reply_note" style="color: gray">Click to reply</em>
					<form id="board_reply_form_<?php echo $i; ?>" class="board_reply" method="post" style="display: none;">
						<input id="body"
							   name="body"
							   type="text"
							   maxlength="1000"
							   placeholder="Reply..." />
						<input id="message_id"
							   name="message_id"
							   type="hidden"
							   value="<?php echo htmlspecialchars($encodedId, ENT_QUOTES); ?>" />
						<button id="reply_button_<?php echo $i; ?>" class="reply_button" type="button">Reply</button>
					</form>
				</li>
			<?php } ?>
		</ul>
		<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#board_popup">Create Message</button>
		
		<h2>Groups</h2>
		<ul>
			<?php foreach ($groups as $group) { ?>
				<li><a href="group.php?id=<?php echo $group["id"]; ?>"><?php echo $group["name"]; ?></a></li>
			<?php } ?>
		</ul>
		<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#group_popup">Create Group</button>
		
		<?php if (!isUserInSchool($mysqli, $_SESSION['user_id'], $id)) : ?>
			<button id="join_school" onclick="joinSchool(<?php echo $id; ?>);">Join School</button>
		<?php else : ?> 
			<button id="leave_school" onclick="leaveSchool(<?php echo $id; ?>);">Leave School</button>
		<?php endif ?>
		
		<div class="modal fade" id="group_popup" tabindex="-1" role="dialog" aria-labelledby="group_popup_modal_label">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="group_popup_modal_label">Create Message</h4>
					</div>
					<form id="create_group_form" action="includes/groups/createGroup.php" method="post">
						<div class="modal-body">
							<input id="name"
								   name="name"
								   type="text"
								   maxlength="255"
								   placeholder="Group Name" 
								   required/> 
							<input id="school_id"
								   name="school_id"
								   type="hidden"
								   value="<?php echo $id; ?>" />
						</div>
						<div class="modal-footer">
							<button type="submit">Post</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="board_popup" tabindex="-1" role="dialog" aria-labelledby="board_popup_modal_label">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="board_popup_modal_label">Create Message</h4>
					</div>
					<form id="create_board_message_form" action="includes/schools/createBoardMessage.php" method="post">
						<div class="modal-body">
							<input id="title"
								   name="title"
								   type="text"
								   maxlength="255"
								   placeholder="Title" 
								   required/>
							<input id="body"
								   name="body"
								   type="text"
								   maxlength="1000"
								   placeholder="Body"
								   required/>
							<input id="school_id"
								   name="school_id"
								   type="hidden"
								   value="<?php echo $id; ?>" />
						</div>
						<div class="modal-footer">
							<button type="submit">Post</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
	</body>
</html>