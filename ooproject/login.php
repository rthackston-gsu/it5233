<?php
	
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Declare a set of variables to hold the username and password for the user
$username = "";
$password = "";

// Declare an empty array of error messages
$errors = array();


// If someone is attempting to login, process their request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Import the application classes
	require_once('include/classes.php');
	
	// Pull the username and password from the <form> POST
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Attempt to login the user and capture the result flag
	$app = new Application();
	$result = $app->login($username, $password, $errors);

	// Check to see if the login attempt succeeded
	if ($result == TRUE) {

		// Redirect the user to the topics page on success
		header("Location: topics.php");
		exit();

	}

}

if (isset($_GET['register']) && $_GET['register']== 'success') {
		$message = "Registration successful. Please login.";
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
	2. Display Login form (sticky):  Username and Password -->

<body>
	<?php include 'include/header.php'; ?>

	<h2>Login</h2>

	<?php include('include/messages.php'); ?>
	
	<div>
		<form method="post" action="login.php">
			
			<input type="text" name="username" id="username" placeholder="Username" value="<?php echo $username; ?>" />
			<br/>

			<input type="password" name="password" id="password" placeholder="Password" value="<?php echo $password; ?>" />
			<br/>

			<input type="submit" value="Login" name="login" />
		</form>
	</div>
	<a href="register.php">Need to create an account?</a>
	<br/>
	<a href="reset.php">Forgot your password?</a>
	<?php include 'include/footer.php'; ?>
	<script src="js/russellthackston.js"></script>
</body>
</html>
