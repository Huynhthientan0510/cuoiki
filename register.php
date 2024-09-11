<?php
include 'includes/db.php';  // Database connection
include 'includes/functions.php';  // Helper functions

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST['username'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$password = hashPassword($_POST['password']);  // Hash the password

	// Prepare a statement to prevent SQL injection
	$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
	$stmt->bind_param("ss", $email, $phone);
	$stmt->execute();
	$stmt->store_result();

	// Check if the email or phone already exists
	if ($stmt->num_rows > 0) {
		// Redirect back to register.html with error message and previous form data
		header("Location: register.html?error=Email%20or%20Phone%20number%20already%20registered.&username=$username&email=$email&phone=$phone");
		exit();
	} else {
		// Insert into database
		$stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ssss", $username, $email, $phone, $password);

		if ($stmt->execute()) {
			// Redirect to confirmation page
			header("Location: confirmation.php");
			exit();
		} else {
			// Redirect back to register.html with error message
			$error_message = urlencode("Error: " . $stmt->error);
			header("Location: register.html?error=$error_message&username=$username&email=$email&phone=$phone");
			exit();
		}
	}

	$stmt->close();
}
?>
