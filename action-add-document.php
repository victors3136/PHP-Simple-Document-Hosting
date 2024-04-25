<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles.css"></link>
    <title>L06</title>
    <script src="goToMyDocuments.js"></script>
	<style>
	</style>
</head>
<body>
	<?php
	session_start();

	if (!isset($_SESSION['username'])) {
		header("Location: form-login.php");
		exit();
	}

	if($_SERVER["REQUEST_METHOD"] !== "POST"){
		header("Location: main.php");
		exit();
	}

	if (!isset($_POST['name']) || !isset($_POST['extension']) || !isset($_FILES['file'])) {	
		echo "<div class=\"error-message\">All fields are required</div>	
				<footer>
					<button onclick=\"goToMyDocuments()\">Back</button>
				</footer>";
		exit();
	}

	$name = $_POST['name'];
	$extension = $_POST['extension'];
	$file = $_FILES['file'];
	$username = $_SESSION['username'];
	$author_id = $_SESSION['id'];

	$accepted_extensions = array('pdf', 'json','txt', 'png', 'jpeg', 'jpg', 'java', 'cs', 'c', 'cpp', 'js', 'html', 'css', 'php');
	if (!in_array($extension, $accepted_extensions)) {
		echo "<div class=\"error-message\"> Invalid file extension. Accepted extensions: pdf, json, txt, png, jpeg, jpg</div>	
				<footer>
					<button onclick=\"goToMyDocuments()\">Back</button>
				</footer>";
		exit();
	}

	$uploaded_extensions = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
	if ($uploaded_extensions !== $extension) {
		echo "<div class=\"error-message\"> The uploaded file extension does not match the declared extension</div>	
				<footer>
					<button onclick=\"goToMyDocuments()\">Back</button>
				</footer>";
		exit();
	}

	$my_sql_connection = new mysqli(getenv('php_l06_db_host'), getenv('php_l06_db_username'), "", getenv('php_l06_db_database'));

	if ($my_sql_connection->connect_error) {
		die("Connection failed: " . $my_sql_connection->connect_error);
	}

	$insert_statement_template = $my_sql_connection->prepare("insert into document (Name, Extension, AuthorID, Document) values (?, ?, ?, ?)");
	$insert_statement_template->bind_param("ssis", $name, $extension, $author_id, $file_content);

	$file_content = file_get_contents($file['tmp_name']);

	if ($insert_statement_template->execute()) {
		echo "<div class=\"success-message\">Document added successfully!</div>";
	} else {
		echo "<div class=\"error-message\">Error adding document to the database</div>";
	}

	$insert_statement_template->close();
	$my_sql_connection->close();

	?>
	<footer>
		<button onclick="goToMyDocuments()">Back</button>
	</footer>
</body>
</html>
