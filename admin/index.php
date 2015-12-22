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

	$state_types = array(0 => "NEW", 1 => "FIXED", 2 => "DUPLICATE", 3 => "INVALID", 4 => "WONTFIX");
	$COUNT_PER_PAGE = 25;
	$count_rows = 0;
	$current_page = 1;

	$db = new Connection;
	$db->start();
	$all_reasons = $db->get_all_reasons();
	$reason_exists = $db->check_reason_exists($_GET["rid"]);

	while ($row = $all_reasons->fetch_array(MYSQLI_NUM)) {
		$key = $row[0];
		$reason_names[$key] = $row[1];
	}

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

	function get_label($filter, $fsub) {
		global $state_types;
		global $reason_names;
		global $reason_exists;

		if ($filter == "state") {
			if ($fsub >= 0 and $fsub <= 4) {
				return $state_types[$fsub];
			}
		}
		else if ($filter == "reason") {
			if ($reason_exists) {
				return $reason_names[$fsub];
			}
		}
		return "ALL";
	}

	function display_reports($reports) {
		global $state_types;
		while ($row = $reports->fetch_array(MYSQLI_NUM)) {
			$st = $row[3];
			echo "<tr><td><a href=\"report.php?id=$row[6]\">$row[0]</a></td>" .
			"<td class=\"middle\">$row[1]</td><td class=\"middle\">$row[5]</td><td class=\"middle\">$row[4]</td>" .
			"<td class=\"middle $state_types[$st]\">$state_types[$st]</td></tr>";
		}
		if (!$reports->num_rows) {
			echo "<tr><td colspan=\"5\" class=\"middle empty\"><b>Empty</b></tr>";
		}
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
							<div class="filter_lines"><a href="?filter=state&sid=0">NEW</a></div>
							<div class="filter_lines"><a href="?filter=state&sid=1">FIXED</a></div>
							<div class="filter_lines"><a href="?filter=state&sid=2">DUPLICATE</a></div>
							<div class="filter_lines"><a href="?filter=state&sid=3">INVALID</a></div>
							<div class="filter_lines"><a href="?filter=state&sid=4">WONTFIX</a></div>
							<div class="filter_lines"><a href="?filter=state&sid=-1"><b>ALL</b></a></div>	
						</div>
						<div class="filter_block">
							<div class="filter_title">Mistake type</div>
<?php
	global $reason_names;
	foreach ($reason_names as $key => $value) {
		echo "<div class=\"filter_lines\"><a href=\"?filter=reason&rid=$key\">$value</a></div>";
	}
?>
							<div class="filter_lines"><a href="?filter=reason&rid=-1"><b>ALL</b></a></div>
						</div>
					</div>
				</div>
			</div>
			<div style="width: 80%;">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="panel-title">Reports
<?php
	if ($_GET["sid"]) {
		echo '&nbsp;&nbsp;<span class="label label-success">' . get_label($_GET["filter"], $_GET["sid"]) . '</span>';
	}
	else if ($_GET["rid"]) {
		echo '&nbsp;&nbsp;<span class="label label-success">' . get_label($_GET["filter"], $_GET["rid"]) . '</span>';
	}
?>
						</div>
					</div>
					<div class="panel-body">
						<table class="table" id="table_reports">
							<thead>
								<tr>
									<th width="50%">Question</th>
									<th class="middle">Category</th>
									<th class="middle">Mistake</th>
									<th class="middle">Added on</th>
									<th class="middle">State</th>
								</tr>
							</thead>
							<tbody>
<?php
	global $db;
	global $state_types;
	global $reason_exists;
	global $count_rows;
	global $current_page;
	global $COUNT_PER_PAGE;

	if ($_GET["filter"] == "state") {
		if ($_GET["sid"] >= 0 && $_GET["sid"] <= 4) {
			list($reports, $count_rows) = $db->filter_report_state($_GET["sid"]);
			display_reports($reports);
		}
		else {
			list($reports, $count_rows) = $db->get_reports();
			display_reports($reports);
		}
	}
	else if ($_GET["filter"] == "reason") {
		if ($reason_exists) {
			list($reports, $count_rows) = $db->filter_report_reason($_GET["rid"]);
			display_reports($reports);
		}
		else {
			list($reports, $count_rows) = $db->get_reports();
			display_reports($reports);
		}
	}
	else {
		list($reports, $count_rows) = $db->get_reports();
		display_reports($reports);
	}
	$count_pages = ceil($count_rows / $COUNT_PER_PAGE);
	if ($count_pages == 0)
		$count_pages = 1;
?>
							</tbody>
						</table>
					</div>
				</div>
				<center>
					<nav>
						<ul class="pagination">
						<li>
							<a href="#" aria-label="Previous">
								<span aria-hidden="true">&laquo;</span>
							</a>
						</li>
<?php
	if ($_GET["page"]) {
		if (is_numeric($_GET["page"])) {
			$current_page = intval($_GET["page"]);
		}
	}
	for ($i=1; $i<=$count_pages; $i++) {
		if ($i == $current_page) {
			echo "<li class=\"active\"><a href=\"?page=$i\">$i</a></li>";
		}
		else {
			echo "<li><a href=\"?page=$i\">$i</a></li>";
		}
	}
?>
						<li>
							<a href="#" aria-label="Next">
								<span aria-hidden="true">&raquo;</span>
							</a>
						</li>
						</ul>
					</nav>
				</center>
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
