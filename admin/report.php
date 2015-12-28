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

	$state_types = array(0 => "NEW", 1 => "FIXED", 2 => "DUPLICATE", 3 => "INVALID", 4 => "WONTFIX");

	$db = new Connection;
	$db->start();
	$trivia = new Trivia_DB;
	$trivia->start();

	$all_reasons = $db->get_all_reasons();
	$all_themes_names = $trivia->get_themes_array();

	function display_question_panel($question, $rep_id) {
		global $trivia;
		global $all_themes_names;
		
		if (!$question) {
			return false;
		}
		$question_dict = $trivia->get_question($question);
		$question_dict = $question_dict->fetch_assoc();
		if ($question_dict) {
?>
	<div class="container">
		<div class="col-sm-8 col-sm-offset-2">
			<form action="report.php?id=<?php echo $rep_id; ?>" id="form_save_all" method="post">
				<input type="hidden" id="idQuestion" name="idQuestion" value="<?php echo $question_dict['id']; ?>">
				<input type="hidden" id="idState" name="idState" value="">
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title">Related Question</div>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label for="txtQuestion" class="control-label">Question</label>
							<input type="text" name="txtQuestion" id="txtQuestion" value="<?php echo $question_dict['question']; ?>" 
							class="form-control">
						</div>
						<div class="form-group">
							<label for="txtAnswer" class="control-label">Answer</label>
							<input type="text" name="txtAnswer" id="txtAnswer" value="<?php echo $question_dict['answer']; ?>" 
							class="form-control">
						</div>
						<div class="form-group">
							<label for="selTheme" class="control-label">Category</label>
							<select id="selTheme" name="selTheme" class="form-control">
<?php
			foreach ($all_themes_names as $key => $value) {
				if ($key == $question_dict["theme_id"]) {
					echo "<option value=\"$key\" selected>$value</option>";
				}
				else {
					echo "<option value=\"$key\">$value</option>";
				}
			}
?>
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<button type="sumbit" class="btn btn-info">Save Report &amp; Question</button>
					<a href="index.php" class="btn btn-default">Back</a>
				</div>
			</form>
		</div>
	</div>
<?php
		}
	}

	function display_report_panel($rep_id) {
		global $db;
		global $all_reasons;
		global $state_types;
		global $all_themes_names;

		$report = $db->get_single_report($rep_id);
		$report = $report->fetch_assoc();
		if ($report) {
?>
	<div class="container">
		<div class="col-sm-8 col-sm-offset-2">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<div class="panel-title">Report</div>
				</div>
			<table class="table">
				<tr>
					<td width="20%"><b>Question:</b></td>
					<td><?php echo $report["question"]; ?></td>
				</tr>
				<tr>
					<td><b>Category:</b></td>
					<td><?php $theme_id = $report["theme_id"]; echo $all_themes_names[$theme_id]; ?></td>
				</tr>
				<tr>
					<td><b>Mistake type:</b></td>
					<td><?php echo $report["reason_name"]; ?></td>
				</tr>
				<tr>
					<td><b>Date Reported:</b></td>
					<td><?php echo $report["cdate"]; ?></td>
				</tr>
				<tr>
					<td><b>Comment:</b></td>
					<td><?php echo $report["comment"]; ?></td>
				</tr>
			</table>
			</div>
			<form id="form_change_report_state" action="report.php?id=<?php echo $_GET["id"]; ?>" method="post">
				<div class="form-group">
					<label for="selState" class="control-label">Change state to</label>
					<select id="selState" name="selState" class="form-control" 
					style="display: inline-block; width: 20%; margin-left: 0.5em;">
<?php
	foreach ($state_types as $key => $value) {
		if ($key == $report["state"]) {
			echo "<option value=\"$key\" selected>$value</option>";
		}
		else {
			echo "<option value=\"$key\">$value</option>";
		}
	}
?>
					</select>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary">Save Report</button>
					<a href="index.php" class="btn btn-default">Back</a>
				</div>
			</form>
		</div>
	</div>
	<br/>
<?php
			return $report["question"];
		}
		else {
			echo '<div class="container"><div class="col-sm-8 col-sm-offset-2"><div class="alert alert-warning">Report not found.</div>';
			echo '<a href="javascript:history.go(-1)" class="btn btn-default">Go back</a></div></div>';
			return false;
		}
	}

	if ($_POST["txtUser"] && $_POST["txtPass"]) {
		if (Utils::login($_POST["txtUser"], $_POST["txtPass"])) {
			Utils::display_admin_navbar();
			$question = display_report_panel($_GET["id"]);
			display_question_panel($question, $_GET["id"]);
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
	else if ($_POST["idQuestion"] && $_POST["txtQuestion"] && $_POST["txtAnswer"] && $_POST["selTheme"] && $_POST["idState"] && $_GET["id"]) {
		Utils::display_admin_navbar();
		echo '<div class="container">';
		if ($db->update_state($_GET["id"], $_POST["selState"])) {
			if ($trivia->update_question($_POST["idQuestion"], $_POST["txtQuestion"], $_POST["txtAnswer"], $_POST["selTheme"])) {
				echo '<div class="alert alert-success col-sm-8 col-sm-offset-2">Report and question were successfully saved.</div>';
			}
			else {
				echo '<div class="alert alert-danger col-sm-8 col-sm-offset-2">Error while trying to save question.</div>';
			}
		}
		else {
			echo '<div class="alert alert-danger col-sm-8 col-sm-offset-2">Error while trying to update report state. Please go easy on the developer.</div>';
		}
		echo '</div>';
		$question = display_report_panel($_GET["id"]);
		display_question_panel($question, $_GET["id"]);
	}
	else if ($_POST["selState"] && $_GET["id"]) {
		Utils::display_admin_navbar();
		echo '<div class="container">';
		if ($db->update_state($_GET["id"], $_POST["selState"])) {
			echo '<div class="alert alert-success col-sm-8 col-sm-offset-2">Report state successfully updated.</div>';
		}
		else {
			echo '<div class="alert alert-danger col-sm-8 col-sm-offset-2">Error while trying to update report state. Please go easy on the developer.</div>';
		}
		echo '</div>';
		$question = display_report_panel($_GET["id"]);
		display_question_panel($question, $_GET["id"]);
	}
	else if ($_SESSION["username"] && $_SESSION["password"]) {
		Utils::display_admin_navbar();
		$question = display_report_panel($_GET["id"]);
		display_question_panel($question, $_GET["id"]);
	}
	else {
		Utils::display_login();
	}

	$db->stop();
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
