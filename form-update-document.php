<?php
	session_start();
	if (!isset($_SESSION['username'])) {
		header("Location: form-login.php");
		exit();
	}
	$author_username = $_SESSION['username'];
	if(!isset($_GET['document_id'])){
		header("Location: main.php");
		exit();
	}
	$document_id = $_GET['document_id'];
	
	$my_sql_connection = new mysqli(getenv('php_l06_db_host'), getenv('php_l06_db_username'), "", getenv('php_l06_db_database'));
	
	if($my_sql_connection->connect_error){
		die('Connection failed with status '.$my_sql_connection->connect_error);
	}
	$select_document_template = 'select * from document where id = ?';
	$query = $my_sql_connection->prepare($select_document_template);
	$query->bind_param("i", $document_id);
	$query->execute();
	$result = $query->get_result();
	if ($result->num_rows < 1) {
		header("Location: main.php");
		exit();
	}
	$row = $result->fetch_assoc();
	$query->close();
	$my_sql_connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles.css"></link>
    <script src="logout.js"></script>
    <script src="goToMain.js"></script>
    <title>L06</title>
</head>
<body>
<header>
    <h1>Here you can edit your document, <?php echo $author_username; ?>!</h1>
	<button class="logout-button" onclick="logout()">Log out</button>
</header>
	<form class="main" action="action-update-document.php" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="method" value="PATCH">
        <input type="hidden" name="document_id" value="<?php echo $document_id ?>">
		<label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $row['Name']; ?>" required><br/>

        <input type="submit" value="Confirm changes"><br/>
    </form>
	<footer>
		<button onclick="goToMain()">Back</button>
	</footer>
</body>
</html>
