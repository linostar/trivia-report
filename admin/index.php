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
<body style="width: 100%; height: 100%; padding-top: 70px;">
<?php
	require_once "utils.php";
	require_once "../mysqli.php";

	$db = new Connection;
	$db->start();
	$all_reasons = $db->get_all_reasons();

	function display_admin_navbar() {
?>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" style="background-color: black;" href="index.php">Admin Panel</a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="https://rizon.net/">Rizon Home</a></li>
					<li><a href="../index.php">Trivia Reporter</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li ><a href="?action=logout">Logout</a></li>
				</ul>
			</div>
		</div>
	</nav>
<?php
	}

	function display_admin_page() {
?>
	<div class="container">
		<div style="display: flex; justify-content: space-between;">
			<div>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-title">Filter by</div>
					</div>
					<div class="panel-body">
						<div class="filter_block">
							<div class="filter_title">Report state</div>
							<div class="filter_lines"><a href="?filter=state&fsub=0">NEW</a></div>
							<div class="filter_lines"><a href="?filter=state&fsub=1">FIXED</a></div>
							<div class="filter_lines"><a href="?filter=state&fsub=2">DUPLICATE</a></div>
							<div class="filter_lines"><a href="?filter=state&fsub=3">INVALID</a></div>
							<div class="filter_lines"><a href="?filter=state&fsub=4">WONTFIX</a></div>
							<div class="filter_lines"><a href="?filter=state&fsub=-1"><b>ALL</b></a></div>	
						</div>
						<div class="filter_block">
							<div class="filter_title">Mistake type</div>
<?php
	global $all_reasons;
	while ($row = $all_reasons->fetch_array(MYSQLI_NUM)) {
		echo "<div class=\"filter_lines\"><a href=\"?filter=reason&fsub=$row[0]\">$row[1]</a></div>";
	}
?>
							<div class="filter_lines"><a href="?filter=reason&fsub=-1"><b>ALL</b></a></div>
						</div>
					</div>
				</div>
			</div>
			<div style="width: 80%;">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="panel-title">Reports</div>
					</div>
					<div class="panel-body">
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	}

	function display_login() {
?>
	<div id="login_container">
		<div class="panel panel-primary panel_login">
			<div class="panel-heading">
				<div class="panel-title"><center>Admin Panel</center></div>
			</div>
			<div class="panel-body">
				<form id="formLogin" action="index.php" method="post">
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
			display_admin_page();
		}
		else {
			echo '<center><div class="alert alert-danger panel_login">Wrong username or password!</div></center>';
			display_login();
		}
	}
	else if ($_GET["action"] == "logout") {
		Utils::logout();
		echo '<center><div class="alert alert-info panel_login">You have been successfully logged out.</div></center>';
		display_login();
	}
	else if ($_SESSION["username"] && $_SESSION["password"]) {
		display_admin_navbar();
		display_admin_page();
	}
	else {
		display_login();
	}

	$db->stop();
?>
	<br/><br/>
	<footer class="footer">
		<center>
			<div id="rizon_footer">© <?php echo date("Y"); ?> Rizon</div>
		</center>
	</footer>

	<script src="../js/jquery-2.1.4.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
	<script src="../js/ie10-viewport-bug-workaround.js"></script>
	<script src="../js/bootstrapValidator.min.js"></script>
	<script src="../js/misc.js"></script>
</body>
</html>
