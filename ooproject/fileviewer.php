<?php
	
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Import the application classes
require_once('include/classes.php');

// Declare a new application object
$app = new Application();
$name = $_GET["file"];

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
	<h2>User Guide</h2>
	<div>
		<?php echo $app->getFile($name); ?>
	</div>
	
</body>
</html>
