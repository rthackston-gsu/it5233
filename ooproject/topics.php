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

// Declare an empty array of error messages
$errors = array();

// Attempt to obtain the list of topics
$topics = $app->getTopics($errors);

// Check for url flag indicating that there was a notopic error.
if (isset($_GET["error"]) && $_GET["error"] == "notopic") {
	$errors[] = "Topic not found.";
}

// Check for url flag indicating that a new topic was created.
if (isset($_GET["newtopic"]) && $_GET["newtopic"] == "success") {
	$message = "Topic successfully created.";
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

<!--1. Display Errors if any exists 
	2. If no errors display topics -->
<body>
	<?php include 'include/header.php'; ?>
	<h2>Discussion Topics</h2>
	
	<?php include('include/messages.php'); ?>
	
	<div class="search">
		<form action="topics.php" method="post">
			<label for="search">Filter:</label>
			<input type="text" id="search" name="search"/>
			<input type="submit" value="Apply" />
		</form>
	</div>
	<h3>Join the discussion by clicking on a topic below</h3>
	<ul class="topics">
		<?php if (sizeof($topics) == 0) { ?>
		<li>No topics found</li>
		<?php } ?>
		<?php foreach ($topics as $topic) { ?>
		<li>
			<a href="comments.php?topicid=<?php echo $topic['topicid']; ?>"><?php echo $topic['topictitle']; ?></a>
			<span class="author"><?php echo $topic['username']; ?> on <?php echo $topic['topicposted']; ?></span>
		</li>
		<?php } ?>
		<li class="new"><a href="newtopic.php">Start a new discussion</a></li>
	</ul>
	<?php include 'include/footer.php'; ?>
	<script src="js/russellthackston.js"></script>
</body>
</html>
