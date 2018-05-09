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

// Declare a set of variables to hold the details for the user
$userid = "";
$username = "";
$password = "";
$question = "";
$answer = "";

// Declare an empty array of error messages
$errors = array();

// If someone is accessing this page for the first time, try and grab the userid from the GET request
// then pull the user's details from the database
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	// Get the userid and isadmin flag from the URL
	$userid = $_GET['userid'];
	if (isset($_GET['isadmin'])){
		$isadmin = $_GET['isadmin'];
	} else {
		$isadmin = "false";
	}
	
	// Attempt to obtain the user information.
	$user = $app->getUser($userid, $isadmin, $errors);
	
	if ($user != NULL){
		$username = $user['username'];
		$password = $user['password'];
		$question = $user['question'];
		$answer = $user['answer'];
		
	}

// If someone is attempting to edit their profile, process the request	
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Get the form values 
	$userid   = $_POST['userid'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$question = $_POST['question'];
	$answer   = $_POST['answer'];
	$isadmin  = $_POST['isadmin'];

	// Attempt to update the user information.
	$result = $app->updateUser($userid, $username, $password, $question, $answer, $isadmin, $errors);
	
	// Display message upon success.
	
	if ($result == TRUE){
		$message = "User successfully updated.";
	}
		
}
		

	
	
/* Check for url flag indicating that there was a nouser error.
if (isset($_GET["error"]) && $_GET["error"] == "notopic") {
	$errors[] = "User not found.";
}

// Check for url flag indicating that a new topic was created.
if (isset($_GET["newtopic"]) && $_GET["newtopic"] == "success") {
	$message = "Topic successfully created.";
}

*/



/**
If we get to here, there are a couple of possible scenarios that affect how we display the page.

1) This was a GET request. The variable $editAttempt will be False. We can just display the page.
2) This was a POST request with errors. The variable $editAttempt will be True and $errors will contain one or more error messages.
3) This was a POST request without errors. The variable $editAttempt will be True and $errors will contain no error messages. Display a success message.

Notes:
* Be sure to echo the values back into the input fields (i.e. "sticky form").
* Display any error messages found in the $errors array.
* The user may click a link anywhere in the application to get here. 
    if so, the link should contain the id of the user that is being edited (i.e. editprofile.php?userid=1
    We'll discuss, in class, how this userid gets put into the "Profile" links.

**/


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

	<h2>Edit Profile</h2>
	
	<?php include 'include/messages.php'; ?>	
	
	<div>
		<form action="editprofile.php" method="post">
			<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
			<input type="hidden" name="isadmin" value="<?php echo $isadmin; ?>" />
			<input type="text" name="username" id="username" placeholder="Pick a username" value="<?php echo $username; ?>" />
			<br/>
			<input type="password" name="password" id="password" placeholder="Enter a password" value="<?php echo $password; ?>" />
			<br/>
			<input type="text" name="question" id="question" placeholder="Choose a security question" size="40" value="<?php echo $question; ?>" />
			<br/>
			<input type="text" name="answer" id="answer" placeholder="The answer to your security question" size="35" value="<?php echo $answer; ?>" />
			<br/>
			<input type="submit" value="Update profile" />
		</form>
	</div>
	<?php include 'include/footer.php'; ?>
	<script src="js/russellthackston.js"></script>
</body>
</html>
