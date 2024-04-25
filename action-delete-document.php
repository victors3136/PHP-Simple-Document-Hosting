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

	if(!isset($_SESSION['id'])){
		header("Location: form-login.php");
		exit();	
	}
	$author_id = $_SESSION['id'];
	
	if (!isset($_GET['document_id'])) {
		echo "<div class=\"error-message\"> Document is missing </div>	
					<footer>
						<button onclick=\"goToMyDocuments()\">Back</button>
					</footer>";
		exit();
	}

	$document_id = $_GET['document_id'];

	$my_sql_connection = new mysqli("localhost", "root", "", "l06");
	if ($my_sql_connection->connect_error) {
		die("Connection failed: " . $my_sql_connection->connect_error);
	}
	$check_statement = $my_sql_connection->prepare("select count(*) from document where ID = ? and AuthorID = ?");
	$check_statement->bind_param("ii", $document_id, $author_id);
	$check_statement->execute();
	$check_statement->bind_result($count);
	$check_statement->fetch();
	$check_statement->close();

	if ($count === 0) {
		echo "<div class=\"error-message\">You are not authorized to delete this document!</div>";
		echo "<footer><button onclick=\"goToMyDocuments()\">Back</button></footer>";
		exit();
	}
	
	$delete_statement = $my_sql_connection->prepare("delete from document where ID = ?");
	$delete_statement->bind_param("i", $document_id);

	if ($delete_statement->execute()) {
		echo "<div class=\"success-message\">
		<p>Document deleted successfully.</p>
		</div>";
	} else {
		echo "<div class=\"error-message\">Error deleting document: " . $my_sql_connection->error . " </div>";
	}

	$delete_statement->close();
	$my_sql_connection->close();
?>
	<footer>
		<button onclick="goToMyDocuments()">Back</button>
	</footer>
</body>
</html>
