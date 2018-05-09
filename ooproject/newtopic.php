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
$title = "";
$topicText = "";
$attachment = NULL;

// Declare a list to hold error messages that need to be displayed
$errors = array();

// If someone is attempting to create a new topic, process their request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Pull the title and topic text from the <form> POST
	$title = $_POST['title'];
	$topicText = $_POST['topicText'];
	$attachment = $_FILES['attachment'];

	// Attempt to create the new topic and capture the result flag
	$result = $app->addTopic($title, $topicText, $attachment, $errors);

	// Check to see if the new topic attempt succeeded
	if ($result == TRUE) {

		// Redirect the user to the login page on success
	    header("Location: topics.php?newtopic=success");
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

	<h2>What's on your mind?</h2>

	<?php include('include/messages.php'); ?>

	<div class="newtopic">
		<form enctype="multipart/form-data" method="post" action="newtopic.php">
			<input type="text" name="title" id="title" size="81" placeholder="Enter a title" value="<?php echo $title; ?>" />
			<br/>
			<textarea name="topicText" id="topicText" rows="8" cols="80" placeholder="What's on your mind?"><?php echo $topicText; ?></textarea>
			<br/>
			<label for="attachment">Add an image, PDF, etc.</label>
			<input id="attachment" name="attachment" type="file">
			<br/>
			<input type="submit" name="start" value="Start a discussion" />
			<input type="submit" name="cancel" value="Cancel" />
		</form>
	</div>
	<?php include 'include/footer.php'; ?>
	<script src="js/russellthackston.js"></script>
</body>
</html>
