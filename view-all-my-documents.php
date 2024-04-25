<?php
	session_start();

	if (!isset($_SESSION['username'])) {
		header("Location: form-login.php");
		exit();
	}
	$my_sql_connection = new mysqli(getenv('php_l06_db_host'), getenv('php_l06_db_username'), "", getenv('php_l06_db_database'));
	if ($my_sql_connection->connect_error) {
		die("Connection failed: " . $my_sql_connection->connect_error);
	}
	$author_id = $_SESSION['id'];
	$author_username = $_SESSION['username'];
	$sql_select_documents_template = "SELECT * FROM document WHERE authorId = ?";
	$sql_get_all_docs_by_auth_id_maching_current = $my_sql_connection->prepare($sql_select_documents_template);
	if ($sql_get_all_docs_by_auth_id_maching_current === false) {	
		$sql_get_all_docs_by_auth_id_maching_current->close();
		$my_sql_connection->close();
		die("Error in preparing statement: " . $my_sql_connection->error);
	}
	$sql_get_all_docs_by_auth_id_maching_current->bind_param("i", $author_id);
	
	$sql_get_all_docs_by_auth_id_maching_current->execute();
	$result = $sql_get_all_docs_by_auth_id_maching_current->get_result();
	$sql_get_all_docs_by_auth_id_maching_current->close();
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
    <h1>Here's all your stuff, <?php echo $author_username; ?>!</h1>
	<button class="logout-button" onclick="logout()">Log out</button>
</header>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Extension</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['Name'] . "</td>";
                echo "<td>" . $row['Extension'] . "</td>";
				echo "<td class=\"relaxed-buttons\">";
				echo "<button onclick=\"window.location.href='view-single-document.php?document_id=" . $row['ID'] . "';\">View</button>";
				echo "<button onclick=\"window.location.href='form-update-document.php?document_id=" . $row['ID'] . "';\">Edit</button>";
				echo "<button onclick=\"window.location.href='action-delete-document.php?document_id=" . $row['ID'] . "';\">Delete</button>";
				echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
	<footer>
		<button onclick="goToMain()">Back</button>
	</footer>
</body>
</html>
