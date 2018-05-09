<?php

// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Declare a set of variables to hold the username, password, question, and answer for the new user
$username = "";
$password = "";
$question = "";
$answer = "";

// Declare a list to hold error messages that need to be displayed
$errors = array();

// If someone is attempting to register, process their request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Import the application classes
	require_once('include/classes.php');

	// Pull the username, password, question, and answer from the <form> POST
	$username = $_POST['username'];
	$password = $_POST['password'];
	$question = $_POST['question'];
	$answer = $_POST['answer'];

	// Attempt to register the new user and capture the result flag
	$app = new Application();
	$result = $app->register($username, $password, $question, $answer, $errors);

	// Check to see if the register attempt succeeded
	if ($result == TRUE) {

		// Redirect the user to the login page on success
	    header("Location: login.php?register=success");
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
<!--1. Display Errors if any exists 
	2. Display Registration form (sticky):  Username, Password, Question, and Answer -->
<body>
	<?php include 'include/header.php'; ?>
	
	<h2>Register</h2>
	
	<?php include('include/messages.php'); ?>
		
	<div>
		<form action="register.php" method="post">
			<input type="text" name="username" id="username" placeholder="Pick a username" value="<?php echo $username; ?>" />
			<br/>
			<input type="password" name="password" id="password" placeholder="Provide a password" value="<?php echo $password; ?>" />
			<br/>
			<input type="text" name="question" id="question" placeholder="Choose a security question" size="40" value="<?php echo $question; ?>" />
			<br/>
			<input type="text" name="answer" id="answer" placeholder="The answer to your security question" size="35" value="<?php echo $answer; ?>" />
			<br/>
			<input type="submit" value="Register" />
		</form>
	</div>
	<a href="login.php">Already a member?</a>
	<?php include 'include/footer.php'; ?>
	<script src="js/russellthackston.js"></script>
</body>
</html>
