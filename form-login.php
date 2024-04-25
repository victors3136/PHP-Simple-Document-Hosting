<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles.css"></link>
    <title>L06</title>
</head>
<body>
	<div class="main">
		<h1>Welcome!</h1>
		<h2>Please log in to continue:</h2>
		<form method="post">
			<label for="username">Username:</label>
			<input type="text" id="username" name="username" required/><br/><br/>
			<button type="submit" name="login">Login</button>
		</form>
	</div>
    <?php
	session_start(); 
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
		
		$my_sql_connection = new mysqli(getenv('php_l06_db_host'), getenv('php_l06_db_username'), "", getenv('php_l06_db_database'));

        if ($my_sql_connection->connect_error) {
            die("Connection failed: " . $my_sql_connection->connect_error);
        }

        $sql_select_template = "SELECT * FROM author WHERE Name = ?";

        $sql_check_if_username_exists = $my_sql_connection->prepare($sql_select_template);
		
        if ($sql_check_if_username_exists === false) {
            die("Error in preparing statement: " . $mysqli->error);
        }

        $sql_check_if_username_exists->bind_param("s", $username);

        $sql_check_if_username_exists->execute();

        $result = $sql_check_if_username_exists->get_result();

        $sql_check_if_username_exists->close();
        if ($result->num_rows <= 0) {
			$sql_insert_template = "INSERT INTO `author` (`Name`) VALUES (?)";
            $sql_insert_new_author_in_table = $my_sql_connection->prepare($sql_insert_template);
            
            if ($sql_insert_new_author_in_table === false) {
                die("Error in preparing statement: " . $mysqli->error);
            }
            $sql_insert_new_author_in_table->bind_param("s", $username);
            $sql_insert_new_author_in_table->execute();
			
			$user_id = $sql_insert_new_author_in_table->insert_id;

            $sql_insert_new_author_in_table->close();
        }else{
			$row = $result->fetch_assoc();
			$user_id = $row['ID'];
		}			
		$_SESSION['username'] = $username;
		$_SESSION['id'] = $user_id;
        $my_sql_connection->close();
		header("Location: main.php");
    }
    ?>
</body>
</html>