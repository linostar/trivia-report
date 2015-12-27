<?php
	require_once "../conf/config.php";

	if (Config::$debug_admin) {
		ini_set('display_errors', 'On');
		error_reporting(E_ALL);
	}

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

	$trivia = new Trivia_DB;
	$trivia->start();

	function display_theme_panel() {
		global $trivia;
		$themes = $trivia->get_all_themes();
?>
	<div class="container">
		<div class="col-sm-6 col-sm-offset-3">
			<form id="form_delete" action="theme.php" method="post">
				<table class="table">
					<thead>
						<tr>
							<th width="5%" class="middle"><input type="checkbox" id="ckSelectAll" name="ckSelectAll"></th>
							<th width="10%" class="middle">ID</th>
							<th>Theme</th>
						</tr>
					</thead>
					<tbody>
					<?php while ($theme = $themes->fetch_array()) { ?>
						<tr>
							<td class="middle">
								<input type="checkbox" id="ckSelect_<?php echo $theme[0]; ?>" name="ckSelect_<?php echo $theme[0]; ?>" 
								class="individualCheckbox">
								</td>
							<td class="middle"><?php echo $theme[0]; ?></td>
							<td><a href="theme.php?action=edit&id=<?php echo $theme[0]; ?>"><?php echo $theme[1]; ?></a></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
<?php
	}

	if ($_POST["txtUser"] && $_POST["txtPass"]) {
		if (Utils::login($_POST["txtUser"], $_POST["txtPass"])) {
			Utils::display_admin_navbar();
			display_theme_panel();
		}
		else {
			echo '<center><div class="alert alert-danger panel_login">Wrong username or password!</div></center>';
			Utils::display_login();
		}
	}
	else if ($_GET["action"] == "logout") {
		Utils::logout();
		echo '<center><div class="alert alert-info panel_login">You have been successfully logged out.</div></center>';
		Utils::display_login();
	}
	else if ($_SESSION["username"] && $_SESSION["password"]) {
		Utils::display_admin_navbar();
		display_theme_panel();
	}
	else {
		Utils::display_login();
	}

	$trivia->stop();
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