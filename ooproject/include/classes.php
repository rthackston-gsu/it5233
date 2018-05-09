<?php

class Application {

	// Creates a database connection
	protected function getConnection() {

		// Import the database credentials
		require('credentials.php');	

		// Create the connection
		$conn = new mysqli($servername, $serverusername, $serverpassword, $serverdb);
	
		// Check connection for errors
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		// Return the newly created connection
		return $conn;
	}
	
	// Registers a new user
	public function register($username, $password, $question, $answer, &$errors) {

		// Validate the user input
		if (empty($username)) {
			$errors[] = "Missing username";
		}
		if (empty($password)) {
			$errors[] = "Missing password";
		}
		if (empty($question)) {
			$errors[] = "Missing security question";
		}
		if (empty($answer)) {
			$errors[] = "Missing answer to security question";
		}
	
		// Only try to insert the data into the database if there are no validation errors
		if (sizeof($errors) == 0) {
	
			// Connect to the database
			$conn = $this->getConnection();
		
			// Construct a SQL statement to perform the insert operation
			$sql = "INSERT INTO users (username, password, question, answer) " . 
				"VALUES ('$username', '$password', '$question', '$answer')";
	
			// Run the SQL insert and capture the result code
			$result = $conn->query($sql);
	
			// If the query did not run successfully, add an error message to the list
			if ($result === FALSE) {
				$errors[] = "An unexpected error occurred: " . $conn->error;
			}
	
			// Close the connection
			$conn->close();
	
		}
	
		// Return TRUE if there are no errors, otherwise return FALSE
		if (sizeof($errors) == 0){
			return TRUE; 
		} else {
			return FALSE;
		}
	}

	// Logs in an existing user and will return the $errors array listing any errors encountered
	public function login($username, $password, &$errors) {

		// Validate the user input
		if (empty($username)) {
			$errors[] = "Missing username";
		}
		if (empty($password)) {
			$errors[] = "Missing password";
		}

		// Only try to insert the data into the database if there are no validation errors
		if (sizeof($errors) == 0) {
	
			// Connect to the database
			$conn = $this->getConnection();
		
			// Construct a SQL statement to perform the insert operation
			$sql = "SELECT userid, isadmin FROM users " . 
				"WHERE username = '$username' AND password = '$password'";
	
			// Run the SQL select and capture the result code
			$result = $conn->query($sql);

			// If the query did not run successfully, add an error message to the list
			if ($result === FALSE) {

				$errors[] = "An unexpected error occurred: " . $conn->error;

			// If the query did not return any rows, add an error message for bad username/password
			} else if ($result->num_rows == 0) {

				$errors[] = "Bad username/password combination";

			// If the query ran successfully and we got back a row, then the login succeeded
			} else {

				// Get the row from the result
				$row = $result->fetch_assoc();

				// Start a session if one has not already been started
				if (session_status() == PHP_SESSION_NONE) {
				    session_start();
				}

				// Get the user's ID and store it in the session
				$_SESSION["userid"] = $row['userid'];

				//check to see if user is admin and if so inserting a session token identifying him as such
				if ($row['isadmin'] == "1") {
					$_SESSION["isadmin"] = True;
				} else {
					$_SESSION["isadmin"] = False;
				}

			}

			// Close the connection
			$conn->close();
	
		}
	
		// Return TRUE if there are no errors, otherwise return FALSE
		if (sizeof($errors) == 0){
			return TRUE; 
		} else {
			return FALSE;
		}
	}


	// Checks for logged in user and redirects to login if not found with "page=protected" indicator in URL.
	public function protectPage($isAdmin = FALSE) {

		// Start a session if one has not already been started
		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}

		// Get the user ID from the session
		$userid = $_SESSION["userid"];

		// If there is no user ID in the session, then the user is not logged in
		if(empty($userid)){

			// Redirect the user to the login page
			header("Location: login.php?page=protected");
			exit();
		} else if ($isAdmin == TRUE && $_SESSION["isadmin"] != True)  {
			// Redirect the user to the home page
			header("Location: index.php?page=protectedAdmin");
			exit();
		}

	}

	// Get a list of topics from the database and will return the $errors array listing any errors encountered
	public function getTopics(&$errors) {

		// Assume an empty list of topics
		$topics = array();

		// Connect to the database
		$conn = $this->getConnection();
	
		// Construct a SQL statement to perform the select operation
		$sql = "SELECT topicid, topictitle, username, topicposted FROM topics LEFT JOIN users ON topics.topicuserid = users.userid";

		// Run the SQL select and capture the result code
		$result = $conn->query($sql);

		// If the query did not run successfully, add an error message to the list
		if ($result === FALSE) {

			$errors[] = "An unexpected error occurred: " . $conn->error;

		// If the query ran successfully, then get the list of topics
		} else {

			// Get all the rows
			$topics = $result->fetch_all(MYSQLI_ASSOC);

		}

		// Close the connection
		$conn->close();

		// Return the list of topics
		return $topics;

	}

	// Get a single topic from the database and will return the $errors array listing any errors encountered
	public function getTopic($topicid, &$errors) {

		// Assume no topic exists for this topic id
		$topic = null;

		// Check for a valid topic ID
		if (!isset($topicid) || $topicid == "") {

			// Add an appropriaye error message to the list
			$errors[] = "Missing topic ID";

		} else {

			// Connect to the database
			$conn = $this->getConnection();
		
			// Construct a SQL statement to perform the select operation
			$sql = "SELECT topicid, topictitle, username, topicposted, topicmessage, attachmentid, filename " . 
				"FROM topics LEFT JOIN users ON topics.topicuserid = users.userid " . 
				"LEFT JOIN attachments ON topics.topicattachmentid = attachments.attachmentid " .
				"WHERE topicid = '$topicid'";
	
			// Run the SQL select and capture the result code
			$result = $conn->query($sql);
	
			// If the query did not run successfully, add an error message to the list
			if ($result === FALSE) {
	
				$errors[] = "An unexpected error occurred: " . $conn->error;
	
			// If no row returned then the topic does not exist in the database.
			} else if ($result->num_rows == 0) {
				
				$errors[] = "Topic not found";
				
			// If the query ran successfully and row was returned, then get the details of the topic				
			} else {
	
				// Get the topic
				$topic = $result->fetch_assoc();

				// Fix deleted usernames
				$this->fixUsernames($topic);

			}
	
			// Close the connection
			$conn->close();

		}

		// Return the list of topics
		return $topic;

	}

	// Get a list of comments from the database
	public function getComments($topicid, &$errors) {

		// Assume an empty list of comments
		$comments = array();

		// Check for a valid topic ID
		if (!isset($topicid) || $topicid == "") {

			// Add an appropriaye error message to the list
			$errors[] = "Missing topic ID";

		} else {
			
			// Connect to the database
			$conn = $this->getConnection();
		
			// Construct a SQL statement to perform the select operation
			$sql = "SELECT commentid, commenttext, commentposted, username, attachmentid, filename " . 
				"FROM comments LEFT JOIN users ON comments.commentuserid = users.userid " . 
				"LEFT JOIN attachments ON comments.commentattachmentid = attachments.attachmentid " .
				"WHERE commenttopicid = '$topicid'";
	
			// Run the SQL select and capture the result code
			$result = $conn->query($sql);
	
			// If the query did not run successfully, add an error message to the list
			if ($result === FALSE) {
	
				$errors[] = "An unexpected error occurred: " . $conn->error;
	
			// If the query ran successfully, then get the list of comments
			} else {
	
				// Get all the rows
				$comments = $result->fetch_all(MYSQLI_ASSOC);

				// Fix deleted usernames
				$this->fixUsernames($comments);

			}
	
			// Close the connection
			$conn->close();
	
		}

		// Return the list of comments
		return $comments;

	}

	// This function goes through the list and replaces NULL usernames with "[Deleted]"
	// The function parameter $ary represents a generic variable that should reference an array object.
	protected function fixUsernames(&$ary) {

		// If the object passed in is not an array, do nothing
		if (!is_array($ary)) {
			return;
		}

		// Go through each item in the array
		foreach($ary as &$item) {

			// If the item is an array, then check for a key called "username"
			// Note: There's an extra check to make sure "username" is a key in the array (to avoid errors)
		    if (is_array($item) && array_key_exists('username', $item) && $item['username'] == NULL) {

				// If there is no username, assume the user deleted their account
			    $item['username'] = "[Deleted]";
		    }
			
		}

		// Check if the main array has a key called "username"
	    if (array_key_exists('username', $ary) && $ary['username'] == NULL) {
		    $ary['username'] = "[Deleted]";
	    }

	}

	// Adds a new topic to the database
	public function addTopic($title, $message, $attachment, &$errors) {

		// Start a session if one has not already been started
		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}

		// Get the user id from the session
		$userid = $_SESSION['userid'];

		// Validate the user input
		if (empty($userid)) {
			$errors[] = "Missing user ID. Not logged in?";
		}
		if (empty($title)) {
			$errors[] = "Missing title";
		}
		if (empty($message)) {
			$errors[] = "Missing message";
		}
	
		// Only try to insert the data into the database if there are no validation errors
		if (sizeof($errors) == 0) {
	
			// Connect to the database
			$conn = $this->getConnection();

			// Add a record to the Attachments table
			$filename = $attachment['name'];
			$attachmentID = NULL;

			// Construct a SQL statement to perform the insert operation
			$sql = "INSERT INTO attachments (filename) VALUES ('$filename')";
	
			// Run the SQL insert and capture the result code
			$result = $conn->query($sql);
	
			// If the query did not run successfully, add an error message to the list
			if ($result === FALSE) {
				$errors[] = "An unexpected error occurred: " . $conn->error;
			} else {
				// Get the attachment ID from the attachments table
				$attachmentID = $conn->insert_id;
				
				// Move the file from temp folder to html attachments folder
				move_uploaded_file($attachment['tmp_name'], '/var/www/html/ooproject/attachments/' . $attachmentID . '-' . $attachment['name']);
			}

			// Add a record to the Topics table
			// Construct a SQL statement to perform the insert operation
			$sql = "INSERT INTO topics (topictitle, topicmessage, topicposted, topicuserid, topicattachmentid) VALUES ('$title', '$message', now(), '$userid', '$attachmentID')";
	
			// Run the SQL insert and capture the result code
			$result = $conn->query($sql);
	
			// If the query did not run successfully, add an error message to the list
			if ($result === FALSE) {
				$errors[] = "An unexpected error occurred: " . $conn->error;
			}
	
			// Close the connection
			$conn->close();

		}
	
		// Return TRUE if there are no errors, otherwise return FALSE
		if (sizeof($errors) == 0){
			return TRUE; 
		} else {
			return FALSE;
		}
	}

	// Adds a new comment to the database
	public function addComment($text, $topicid, $attachment, &$errors) {
		
		// Start a session if one has not already been started
		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}

		// Get the user id from the session
		$userid = $_SESSION['userid'];

		// Validate the user input
		if (empty($userid)) {
			$errors[] = "Missing user ID. Not logged in?";
		}
		if (empty($topicid)) {
			$errors[] = "Missing topic ID";
		}
		if (empty($text)) {
			$errors[] = "Missing comment text";
		}
	
		// Only try to insert the data into the database if there are no validation errors
		if (sizeof($errors) == 0) {
	
			// Connect to the database
			$conn = $this->getConnection();

			// Add a record to the Attachments table
			$filename = $attachment['name'];
			$attachmentID = NULL;

			// Construct a SQL statement to perform the insert operation
			$sql = "INSERT INTO attachments (filename) VALUES ('$filename')";
	
			// Run the SQL insert and capture the result code
			$result = $conn->query($sql);
	
			// If the query did not run successfully, add an error message to the list
			if ($result === FALSE) {
				$errors[] = "An unexpected error occurred: " . $conn->error;
			} else {
				// Get the attachment ID from the attachments table
				$attachmentID = $conn->insert_id;
				
				// Move the file from temp folder to html attachments folder
				move_uploaded_file($attachment['tmp_name'], '/var/www/html/ooproject/attachments/' . $attachmentID . '-' . $attachment['name']);
			}

			// Add a record to the Comments table
			// Construct a SQL statement to perform the insert operation
			$sql = "INSERT INTO comments (commenttext, commentposted, commentuserid, commenttopicid, commentattachmentid) VALUES ('$text', now(), '$userid', '$topicid', '$attachmentID')";
	
			// Run the SQL insert and capture the result code
			$result = $conn->query($sql);
	
			// If the query did not run successfully, add an error message to the list
			if ($result === FALSE) {
				$errors[] = "An unexpected error occurred: " . $conn->error;
			}
	
			// Close the connection
			$conn->close();
	
		}
	
		// Return TRUE if there are no errors, otherwise return FALSE
		if (sizeof($errors) == 0){
			return TRUE; 
		} else {
			return FALSE;
		}
	}

	// Get a list of users from the database and will return the $errors array listing any errors encountered
	public function getUsers(&$errors) {

		// Assume an empty list of topics
		$users = array();

		// Connect to the database
		$conn = $this->getConnection();
	
		// Construct a SQL statement to perform the select operation
		$sql = "SELECT userid, username, password, question, answer, isadmin FROM users ORDER BY username";

		// Run the SQL select and capture the result code
		$result = $conn->query($sql);

		// If the query did not run successfully, add an error message to the list
		if ($result === FALSE) {

			$errors[] = "An unexpected error occurred: " . $conn->error;

		// If the query ran successfully, then get the list of users
		} else {

			// Get all the rows
			$users = $result->fetch_all(MYSQLI_ASSOC);

		}

		// Close the connection
		$conn->close();

		// Return the list of users
		return $users;

	}

	// Gets a single user from database and will return the $errors array listing any errors encountered
	public function getUser($userid, $isadmin, &$errors) {
		
		// Assume no user exists for this user id
		$user = NULL;

		// Stop people from viewing someone else's profile
		if ($isadmin != "true" && $_SESSION["userid"] != $userid) {
			$errors[] = "Cannot view other user";

		} else {
			// Validate the user input
			if (empty($userid)) {
				$errors[] = "Missing userid";
			}
			
	
			// Only try to insert the data into the database if there are no validation errors
			if (sizeof($errors) == 0) {
		
				// Connect to the database
				$conn = $this->getConnection();
			
				// Construct a SQL statement to perform the select operation
				$sql = "SELECT userid, username, password, question, answer, isadmin FROM users WHERE userid = $userid";
		
				// Run the SQL select and capture the result code
				$result = $conn->query($sql);
	
				// If the query did not run successfully, add an error message to the list
				if ($result === FALSE) {
	
					$errors[] = "An unexpected error occurred: " . $conn->error;
	
				// If the query did not return any rows, add an error message for invalid user id
				} else if ($result->num_rows == 0) {
	
					$errors[] = "Bad userid";
	
				// If the query ran successfully and we got back a row, then the request succeeded
				} else {
	
					// Get the row from the result
					$user = $result->fetch_assoc();
	
				}
	
				// Close the connection
				$conn->close();
		
			}
		}
		// Return user if there are no errors, otherwise return NULL
		return $user;
	}


	// Updates a single user in the database and will return the $errors array listing any errors encountered
	public function updateUser($userid, $username, $password, $question, $answer, $isadmin, &$errors) {
		
		// Stop people from editing someone else's profile
		if ($isadmin != "true" && $_SESSION["userid"] != $userid) {
			$errors[] = "Cannot edit other user";

		} else {
			// Validate the user input
			if (empty($userid)) {
				$errors[] = "Missing userid";
			}
			if (empty($username)) {
				$errors[] = "Missing username";
			}
			if (empty($password)) {
				$errors[] = "Missing password";
			}
			if (empty($question)) {
				$errors[] = "Missing question";
			}
			if (empty($answer)) {
				$errors[] = "Missing answer";
			}
	
			// Only try to update the data into the database if there are no validation errors
			if (sizeof($errors) == 0) {
		
				// Connect to the database
				$conn = $this->getConnection();
			
				// Construct a SQL statement to perform the select operation
				$sql = 	"UPDATE users SET username='$username', password='$password', question='$question', answer='$answer' ".
						"WHERE userid = '$userid'";
		
				echo $sql;
				// Run the SQL select and capture the result code
				$result = $conn->query($sql);
	
				// If the query did not run successfully, add an error message to the list
				if ($result === FALSE) {
					$errors[] = "An unexpected error occurred: " . $conn->error;
				}
				
				// Close the connection
				$conn->close();
			}
		}
		
		// Return TRUE if there are no errors, otherwise return FALSE
		if (sizeof($errors) == 0){
			return TRUE; 
		} else {
			return FALSE;
		}
	}


	function getFile($name){
		return file_get_contents($name);
	}



}


?>