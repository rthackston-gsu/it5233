<?php

	// Start a session if one has not been started
	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}

	// Assume the user is not logged in and not an admin
	$loggedin = FALSE;
	$isadmin = FALSE;

	// If the session has a userid stored in it, then someone is logged in
	if (isset($_SESSION['userid'])) {
		$loggedin = TRUE;
		$sessionUserid = $_SESSION['userid'];
		$isadmin = $_SESSION['isadmin'];
	}

?>
	<div class="nav">
		<a href="index.php">Home</a>
		&nbsp;&nbsp;
		<?php if (!$loggedin) { ?>
			<a href="login.php">Login</a>
			&nbsp;&nbsp;
			<a href="register.php">Register</a>
			&nbsp;&nbsp;
		<?php } ?>
		<?php if ($loggedin) { ?>
			<a href="topics.php">Topics</a>
			&nbsp;&nbsp;
			<a href="editprofile.php?userid=<?php echo $sessionUserid; ?>">Profile</a>
			&nbsp;&nbsp;
			<?php if ($isadmin) { ?>
				<a href="admin.php">Admin</a>
				&nbsp;&nbsp;
			<?php } ?>
			<a href="fileviewer.php?file=include/help.txt">Help</a>
			&nbsp;&nbsp;
			<a href="logout.php">Logout</a>
			&nbsp;&nbsp;

		<?php } ?>
	</div>
	<h1>IT 5233</h1>
