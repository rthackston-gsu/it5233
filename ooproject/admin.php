<?php
	
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Import the application classes
require_once('include/classes.php');

// Declare a new application object
$app = new Application();

// Check for logged in admin user since this page is "isadmin" protected
// NOTE: passing optional parameter TRUE which indicates the user must be an admin
$app->protectPage(TRUE);

// Declare an empty array of error messages
$errors = array();

// Attempt to obtain the list of users
$users = $app->getUsers($errors);

/* Check for url flag indicating that there was a nouser error.
if (isset($_GET["error"]) && $_GET["error"] == "notopic") {
	$errors[] = "User not found.";
}

// Check for url flag indicating that a new topic was created.
if (isset($_GET["newtopic"]) && $_GET["newtopic"] == "success") {
	$message = "Topic successfully created.";
}

*/

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
	<h2>Admin Functions</h2>
	<?php include 'include/messages.php'; ?>
	<ul class="users">
		<?php foreach($users as $user) { ?>
			<li><a href="editprofile.php?isadmin=true&userid=<?php echo $user['userid']; ?>"><?php echo $user['username']; ?></a></li>
		<?php } ?>
	</ul>
	<?php include 'include/footer.php'; ?>
	<script src="js/russellthackston.js"></script>
</body>
</html>
