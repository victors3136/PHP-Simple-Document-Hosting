<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles.css"></link>
    <title>L06</title>
    <script src="goToMyDocuments.js"></script>
</head>
<body>
	<?php
	session_start();

	if (!isset($_SESSION['username'])) {
		header("Location: form-login.php");
		exit();
	}

	if($_SERVER["REQUEST_METHOD"] !== "POST" && $_POST['_method'] !== 'PATCH'){
		header("Location: main.php");
		exit();
	}

	if (!isset($_POST['name']) || empty($_POST['name'])) {	
		echo "<div class=\"error-message\">New name should not be empty!</div>	
				<footer>
					<button onclick=\"goToMyDocuments()\">Back</button>
				</footer>";
		exit();
	}

	$document_name = $_POST['name'];
	$document_id = $_POST['document_id'];
	$username = $_SESSION['username'];
	$id = $_SESSION['id'];
	
	$my_sql_connection = new mysqli(getenv('php_l06_db_host'), getenv('php_l06_db_username'), "", getenv('php_l06_db_database'));

	if ($my_sql_connection->connect_error) {
		die("Connection failed: " . $my_sql_connection->connect_error);
	}
	
	$check_statement = $my_sql_connection->prepare("select count(*) from document where ID = ? and AuthorID = ?");
	$check_statement->bind_param("ii", $document_id, $id);
	$check_statement->execute();
	$check_statement->bind_result($count);
	$check_statement->fetch();
	$check_statement->close();

	if ($count === 0) {
		echo "<div class=\"error-message\">You are not authorized to edit this document!</div>";
		echo "<footer><button onclick=\"goToMyDocuments()\">Back</button></footer>";
		exit();
	}
	
	$insert_statement_template = $my_sql_connection->prepare("update document set Name = ? where ID = ?");
	$insert_statement_template->bind_param("si", $document_name, $document_id);

	if ($insert_statement_template->execute()) {
		echo "<div class=\"success-message\">Document edited successfully!</div>";
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
