<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles.css"></link>
    <script src="goToMain.js"></script>
    <title>L06</title>
</head>
<body>
<?php
	session_start();

	if (!isset($_SESSION['username'])) {
		header("Location: form-login.php");
		exit();
	}

	if (!isset($_GET['document_id'])) {
		echo "Document ID is missing.";
		exit();
	}

	$document_id = $_GET['document_id'];

	$my_sql_connection = new mysqli(getenv('php_l06_db_host'), getenv('php_l06_db_username'), "", getenv('php_l06_db_database'));
	if ($my_sql_connection->connect_error) {
		die("Connection failed: " . $my_sql_connection->connect_error);
	}

	$select_statement = $my_sql_connection->prepare("select Document, Extension from document where ID = ?");
	$select_statement->bind_param("i", $document_id);

	$select_statement->execute();
	$select_statement->bind_result($document_content, $extension);

	if ($select_statement->fetch()) {
		switch ($extension) {
			case 'txt':
			case 'json':
			case 'html':
			case 'js':
			case 'java':
			case 'cs':
			case 'c':
			case 'cpp':
			case 'css':
			case 'php':
				echo "<div class=\"content-display\"><pre>" . htmlspecialchars($document_content) . "</pre></div>";
				break;
			case 'pdf':
				header('Content-Type: application/pdf');
				header('Content-Disposition: inline; filename="document.pdf"');
				echo $document_content;
			case 'jpeg':
			case 'jpg':
			case 'png':
				echo "<div class=\"content-display\"><img src='data:image/" . $extension . ";base64," . base64_encode($document_content) . "' alt='Document Image'></div>";
				break;
			default:
				echo "Unsupported file type.";
		}
	} else {
		echo "Document not found.";
	}

	$select_statement->close();
	$my_sql_connection->close();
?>
	<footer>
		<button onclick="goToMain()">Back</button>
	</footer>
</body>
</html>