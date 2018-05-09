<?php

// Import the application classes
require_once('include/classes.php');

// Declare a new application object
$app = new Application();

// Check for logged in user since this page is protected
$app->protectPage();

// Get the topic id from the URL
$topicid = $_GET['topicid'];

// Declare an empty array of error messages
$errors = array();

// Attempt to obtain the topic
$topic = $app->getTopic($topicid, $errors);

// If there were no errors getting the topic, try to get the comments
if (sizeof($errors) == 0) {

	// Attempt to obtain the comments for this topic
	$comments = $app->getComments($topicid, $errors);

} else {
	// Redirect the user to the topics page on error
	header("Location: topics.php?error=notopic");
	exit();
}

// Check for url flag indicating that a new comment was created.
if (isset($_GET["newcomment"]) && $_GET["newcomment"] == "success") {
	$message = "Comment successfully created.";
}

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>russellthackston.me</title>
	<meta name="description" content="Russell Thackston's personal website for IT 5233">
	<meta name="author" content="Russell Thackston">
	<link rel="stylesheet" href="css/russellthackston.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<?php include 'include/header.php'; ?>
	<div class="breadcrumbs">
		<a href="topics.php">Back to topics list</a>
	</div>
	
	<?php include('include/messages.php'); ?>
	
	<div class="topiccontainer">
		<p class="topictitle"><?php echo $topic['topictitle']; ?></p>
		<p class="topictagline"><?php echo $topic['username']; ?> on <?php echo $topic['topicposted']; ?></p>
		<p class="topicdescription">
			<?php echo $topic['topicmessage']; ?>
		</p>
		<?php if ($topic['filename'] != NULL) { ?>
			<p class="topicattachment"><a href="attachments/<?php echo $topic['attachmentid'] . '-' . $topic['filename']; ?>"><?php echo $topic['filename']; ?></a></p>
		<?php } else { ?>
			<p class="topicattachment">No attachment</p>
		<?php } ?>
	</div>
	<ul class="comments">
		<li class="new"><a href="newcomment.php?topicid=<?php  echo $topicid; ?>">Add to the discussion</a></li>
		<?php foreach ($comments as $comment) { ?>
		<li>
			<?php echo $comment['commenttext']; ?>
			<br/>
			<span class="author"><?php echo $comment['username']; ?> on <?php echo $comment['commentposted']; ?></span>
			<?php if ($comment['filename'] != NULL) { ?>
				<p class="commentattachment"><a href="attachments/<?php echo $comment['attachmentid'] . '-' . $comment['filename']; ?>"><?php echo $comment['filename']; ?></a></p>
			<?php } else { ?>
				<p class="commentattachment">No attachment</p>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>
	<?php include 'include/footer.php'; ?>
	<script src="js/russellthackston.js"></script>
</body>
</html>
