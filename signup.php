<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="stylesheet.css">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Computing Publication Library</title>
</head>

<body>
	<h1 style='padding-bottom:100px;' class="display-3 center titlemain">Computing Publication Library Page</h1>


	<div style='background-color:white; border-radius:35px;' class="container my-container">
		<div class="row my-row">
			<div style='margin-bottom:50px; margin-top:25px;' id="row0col0" class="col-xs-12 col-lg-12 my-col">
				<div style='border-radius:10px;' class="topnav">
					<a href="index.html">Home</a>
					<a href="publication.html">Publications</a>
					<a href="login.html">Log In</a>
					<a class="active" href="signup.html">Log In</a>
					<!-- <input type="text" placeholder="Search.."> -->
				</div>
			</div>

			<div style='margin-left:50px; margin-bottom:100px;' class="row my-row">
				<form id="signUpForm" oninput='confirmPassword.setCustomValidity(confirmPassword.value != password.value ? "Passwords do not match." : "")' action="" method="post">
					<div class="form-group">
						<div class="row">
							<div class="col">
								<input type="text" name="firstName" class="form-control" placeholder="First name" maxlength="45" required>
							</div>
							<div class="col">
								<input type="text" name="lastName" class="form-control" placeholder="Last name" maxlength="45" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						<input type="email" name="email" class="form-control" placeholder="Email Address" maxlength="45" required>
					</div>
					<div class="form-group">
						<input type="password" name="password" class="form-control" placeholder="Password" minlength="6" required>
					</div>
					<div class="form-group">
						<input type="password" name="confirmPassword" class="form-control" placeholder="Confirm Password">
					</div>
					<div class="modal-footer">
						<a id="signInPrompt" href="#" onclick="signIn()"></a>
						<button type="submit" class="btn btn-primary">Sign Up</button>
					</div>
				</form>
			</div>



</body>

</html>
<?php
require_once("database.php");
if (isset($_POST)){
	if (isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
		if (!isset($_SESSION)) {
			if (validateEmail($_POST["email"]) && validateInput(($_POST["firstName"]) && validateInput($_POST["lastName"]))) {
				if ((strcmp(strval($_POST["password"]), strval($_POST["confirmPassword"])) == 0)) {
					if (checkExistingUser($_POST["email"])) {
						$db_query = $db->prepare('INSERT INTO users (firstName, lastName, email, password, numOfAccess, lastAccessed) VALUES (?,?,?,?,?,?)');
						$db_query->bind_param('ssssii', sanitizeString($_POST["firstName"]), sanitizeString($_POST["lastName"]), $_POST["email"], password_hash($_POST["password"]));
						$db_query->execute();
						$db_query = $db->prepare('SELECT id FROM users WHERE email = ?');
						$db_query->bind_param('s', $_POST["email"]);
						$db_query->execute();
						$db_query->bind_result($id);
						$db_query->fetch();
						$db_conn->close();
						session_start();
						$_SESSION["firstName"] = $_POST["firstName"];
						$_SESSION["lastName"] = $_POST["lastName"];
						$_SESSION["id"] = $id;
						header('Location: ../index.php');
					} else {
						phpAlert("An account with that email already exists");
					}
				} else {
					phpAlert("Passwords do not match");
				}
			} else {
				phpAlert("Invalid input");
			}
		} else {
			phpAlert("Already signed in");
		}
	} else {
		phpAlert("Input invalid");
	}
} else {
	die();
}
function phpAlert($msg)
{
	die('<script type="text/javascript">alert("' . $msg . '")</script>');
}
function validateEmail($email)
{
	if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) < 46) {
		return true;
	} else {
		return false;
	}
}
function validateInput($string)
{
	if (strlen($string) < 46) {
		return true;
	} else {
		return false;
	}
}
function sanitizeString($string)
{
	return filter_var($string, FILTER_SANITIZE_STRING);
}
function checkExistingUser($email)
{
	global $db;
	try {
		$email = $db->real_escape_string(strtolower($email));
		$result = $db->query("SELECT id FROM users WHERE email = '$email'");
		if ($result->num_rows == 0) {
			return true;
		} else {
			return false;
		}
	} catch (Exception $e) {
		echo json_encode(array('message' => 'DB Error'));
		die();
	}
}
?>