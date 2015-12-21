<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Rizon - Trivia Reporter Admin</title>
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrapValidator.min.css">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body style="width: 100%; height: 100%;">
<?php
	require_once "utils.php";

	function display_admin_navbar() {
		echo "navbar";
	}

	function display_admin_page() {
		echo "admin";
	}

	function display_login() {
?>
		<div id="login_container">
			<div class="panel panel-primary" id="panel_login">
				<div class="panel-heading">
					<div class="panel-title"><center>Admin Panel</center></div>
				</div>
				<div class="panel-body">
					<form id="formLogin" action="" method="post">
						<div class="form-group">
							<label for="txtUser" class="control-label">Username</label>
							<input type="text" name="txtUser" id="txtUser" value="" class="form-control">
						</div>
						<div class="form-group">
							<label for="txtPass" class="control-label">Password</label>
							<input type="password" name="txtPass" id="txtPass" value="" class="form-control">
						</div>
						<div class="form-group">
							<center><button type="submit" name="loginButton" class="btn btn-primary">Login</button></center>
						</div>
					</form>
				</div>
			</div>
		</div>
<?php
	}

	if ($_POST["txtUser"] && $_POST["txtPass"]) {
		if (Utils::login($_POST["txtUser"], $_POST["txtPass"])) {
			display_admin_navbar();
			echo '<div class="alert alert-success">You have been successfully logged in.</div>';
			display_admin_page();
		}
		else {
			echo '<div class="alert alert-danger">Wrong username or password!</div>';
			display_login();
		}
	}
	if ($_SESSION["username"] && $_SESSION["password"]) {
		display_admin_navbar();
		display_admin_page();
	}
	else {
		display_login();
	}
?>
	<script src="../js/jquery-2.1.4.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
	<script src="../js/ie10-viewport-bug-workaround.js"></script>
	<script src="../js/bootstrapValidator.min.js"></script>
	<script src="../js/misc.js"></script>
</body>
</html>
