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

	function display_question_panel($page = 1) {
		global $trivia;
		$themes = $trivia->get_themes_array();
		list($questions, $count_rows) = $trivia->get_questions($page);
?>
	<div class="container">
		<div class="col-sm-6 col-sm-offset-3" id="leftPanel">
			<form id="formQuestionDelete" name="formQuestionDelete" action="question.php" method="post">
				<input type="hidden" name="actionQuestionDelete"  id="actionQuestionDelete" value="delete">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-title">Questions</div>
					</div>
					<div class="panel-body">
						<center>
						<div class="btn-group btn-group-sm">
							<button type="submit" class="btn btn-danger">
								<span class="glyphicon glyphicon-remove"></span> Delete selected
							</button>
							<button type="button" class="btn btn-success" id="btnAddQuestion">
								<span class="glyphicon glyphicon-plus"></span> Add Question
							</button>
						</div>
						</center>	
						<table class="table">
							<thead>
								<tr>
									<th width="5%" class="middle"><input type="checkbox" id="ckSelectAll" name="ckSelectAll"></th>
									<th width="10%" class="middle">Category</th>
									<th>Question</th>
								</tr>
							</thead>
							<tbody>
							<?php while ($question = $questions->fetch_array()) { ?>
								<tr>
									<td class="middle">
										<input type="checkbox" value="<?php echo $question[0]; ?>" name="ckSelectItem[]" 
										class="individualCheckbox">
										</td>
									<td class="middle"><?php echo $question[3]; ?></td>
									<td><a class="triggerQuestionEdit" href="#" tag_id="<?php echo $question[0]; ?>" tag_question="<?php echo $question[1]; ?>"
									tag_answer="<?php echo $question[2]; ?>" tag_theme="<?php echo $question[4]; ?>">
										<?php echo $question[1]; ?>
									</a></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
		<div class="col-sm-6" id="rightPanel" hidden="true">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title" style="display: flex; justify-content: space-between;">
						<span id="rightTitle">New Question</span>
						<span id="btnClose"><a class="pointer"><span class="glyphicon glyphicon-remove"></span></a></span>
					</div>
				</div>
				<div class="panel-body">
					<form id="formQuestionChange" name="formQuestionChange" action="question.php" method="post">
						<input type="hidden" name="actionQuestionChange" id="actionQuestionChange" value="add">
						<input type="hidden" name="txtQuestionID" id="txtQuestionID" value="">
						<div class="form-group">
							<label for="txtQuestion" class="control-label">Question</label>
							<input type="text" name="txtQuestion" id="txtQuestion" class="form-control" value="">
						</div>
						<div class="form-group">
							<label for="txtanswer" class="control-label">Answer</label>
							<input type="text" name="txtAnswer" id="txtAnswer" class="form-control" value="">
						</div>
						<div class="form-group">
							<label for="selTheme" class="control-label">Category</label>
							<select name="selTheme" id="selTheme" class="form-control">
<?php
	foreach ($themes as $key => $value) {
		echo "<option value=\"$key\">$value</option>";
	}
?>
							</select>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-info">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php
	}

	if ($_POST["txtUser"] && $_POST["txtPass"]) {
		if (Utils::login($_POST["txtUser"], $_POST["txtPass"])) {
			Utils::display_admin_navbar();
			display_question_panel();
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
	else if ($_POST["actionQuestionDelete"] == "delete") {
		$count = 0;
		$delete_status = true;
		Utils::display_admin_navbar();
		foreach ($_POST["ckSelectItem"] as $item) {
			$delete_status = $delete_status && $trivia->delete_question($item);
			$count += 1;
		}
		if ($delete_status) {
			if ($count == 1)
				echo '<div class="container"><div class="alert alert-success col-sm-6 col-sm-offset-3">'. 
				'1 Question successfully deleted.</div></div>';
			else
				echo '<div class="container"><div class="alert alert-success col-sm-6 col-sm-offset-3">'. 
				$count . ' Questions successfully deleted.</div></div>';
		}
		else {
			echo '<div class="container"><div class="alert alert-danger col-sm-6 col-sm-offset-3">Error when trying to delete questions.</div></div>';
		}
		display_question_panel();
	}
	else if ($_POST["actionQuestionChange"] == "add" && $_POST["txtQuestion"] && $_POST["txtAnswer"] && $_POST["selTheme"]) {
		Utils::display_admin_navbar();
		if ($trivia->add_question($_POST["txtQuestion"], $_POST["txtAnswer"], $_POST["selTheme"])) {
			echo '<div class="container"><div class="alert alert-success col-sm-6 col-sm-offset-3">Question successfully added.</div></div>';
		}
		else {
			echo '<div class="container"><div class="alert alert-danger col-sm-6 col-sm-offset-3">Error when trying to add the question.</div></div>';
		}
		display_question_panel();
	}
	else if ($_POST["actionQuestionChange"] == "edit" && $_POST["txtQuestion"] && $_POST["txtQuestionID"]) {
		Utils::display_admin_navbar();
		if ($trivia->edit_question($_POST["txtQuestionID"], $_POST["txtQuestion"], $_POST["txtAnswer"], $_POST["selTheme"])) {
			echo '<div class="container"><div class="alert alert-success col-sm-6 col-sm-offset-3">Question successfully updated.</div></div>';
		}
		else {
			echo '<div class="container"><div class="alert alert-danger col-sm-6 col-sm-offset-3">Error when trying to update the question.</div></div>';
		}
		display_question_panel();
	}
	else if ($_SESSION["username"] && $_SESSION["password"]) {
		Utils::display_admin_navbar();
		display_question_panel();
	}
	else {
		Utils::display_login();
	}

	$trivia->stop();
?>
	<br/><br/>
	<footer class="footer footer_admin">
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
