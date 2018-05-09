<?php

// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Import the application classes
require_once('include/classes.php');

// Declare a new application object
$app = new Application();

// Check for logged in user since this page is protected
$app->protectPage();

// Declare a set of variables to hold the username, password, question, and answer for the new user
$text = "";
$topicid = "";
$attachment = NULL;

// Declare a list to hold error messages that need to be displayed
$errors = array();

// If this is a GET request, pull the topic ID from the URL
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

	// The topic ID is in the URL
	$topicid = $_GET['topicid'];

	// Get the details of the topic from the database
	$topic = $app->getTopic($topicid, $errors);

	// If there were errors, send the user back to the list of topics
	if (sizeof($errors) > 0) {
		
		// Redirect the user to the topics page.
		header("Location: topics.php?error=notopic");
		exit();
		
	}

}

// If someone is attempting to create a new topic, process their request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Pull the comment text from the <form> POST
	$text = $_POST['comment'];

	// Pull the topic ID from the URL
	$topicid = $_POST['topicid'];
	$attachment = $_FILES['attachment'];

	// Get the details of the topic from the database
	$topic = $app->getTopic($topicid, $errors);

	// Attempt to create the new topic and capture the result flag
	$result = $app->addComment($text, $topicid, $attachment, $errors);

	// Check to see if the new comment attempt succeeded
	if ($result == TRUE) {

		// Redirect the user to the login page on success
	    header("Location: comments.php?newcomment=success&topicid=" . $topicid);
		exit();

	}

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
	<div class="topiccontainer">
		<p class="topictitle"><?php echo $topic['topictitle']; ?></p>
		<p class="topictagline"><?php echo $topic['username']; ?> on <?php echo $topic['topicposted']; ?></p>
		<p class="topicdescription">
			<?php echo $topic['topicmessage']; ?>
		</p>
		<p class="topicattachment">No attachment</p>
	</div>
	
	<?php include("include/messages.php"); ?>
	
	<div class="newcomment">
		<form enctype="multipart/form-data" method="post" action="newcomment.php">
			<textarea name="comment" id="comment" rows="8" cols="80" placeholder="Enter your comment here"></textarea>
			<br/>
			<label for="attachment">Add an image, PDF, etc.</label>
			<input id="attachment" name="attachment" type="file">
			<br/>
			<input type="hidden" name="topicid" value="<?php echo $topicid; ?>" />
			<input type="submit" name="start" value="Post comment" />
			<input type="submit" name="cancel" value="Cancel" />
		</form>
	</div>
	<?php include 'include/footer.php'; ?>
	<script src="js/russellthackston.js"></script>
</body>
</html>
