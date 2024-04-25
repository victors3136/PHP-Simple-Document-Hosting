<?php
	session_start();
	if (!isset($_SESSION['username'])) {
		header("Location: form-login.php");
		exit();
	}
	$author_username = $_SESSION['username'];
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
    <h1>Here you can add a new document, <?php echo $author_username; ?>!</h1>
	<button class="logout-button" onclick="logout()">Log out</button>
</header>
    <form class="main" action="action-add-document.php" method="POST" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="extension">Extension:</label>
        <input type="text" id="extension" name="extension" required>

        <label for="file">Content:</label>
        <input type="file" id="file" name="file" required>

        <input type="submit" value="Add Document"><br/>
    </form>
	<footer>
		<button onclick="goToMain()">Back</button>
	</footer>
</body>
</html>
