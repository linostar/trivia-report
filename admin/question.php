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
	$current_page = 1;

	function display_question_panel($page = 1, $search = false) {
		global $trivia;
		global $current_page;

		$themes = $trivia->get_themes_array();
		if ($search) {
			list($questions, $count_rows) = $trivia->search_question($search, $page);
		}
		else {
			list($questions, $count_rows) = $trivia->get_questions($page);
		}
?>
	<div class="container">
		<div class="col-sm-6 col-sm-offset-3" id="leftPanel">
			<form name="formSearch" id="formSearch" action="question.php" method="get"></form>
			<form id="formQuestionDelete" name="formQuestionDelete" action="question.php" method="post">
				<input type="hidden" name="actionQuestionDelete"  id="actionQuestionDelete" value="delete">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="panel-title">
							Questions
						</div>
					</div>
					<div class="panel-body">
						<div class="input-group">
							<?php if ($_GET["search"]) { ?>
							<input type="text" name="search" id="search" value="<?php echo $_GET['search']; ?>" class="form-control pale"
							placeholder="Search questions" aria-describedby="basic-addon" form="formSearch">
							<?php } else { ?>
							<input type="text" name="search" id="search" value="" class="form-control"
							placeholder="Search questions" aria-describedby="basic-addon" form="formSearch">
							<?php } ?>
							<?php
								if ($_GET["search"]) {
									echo '<span class="input-group-addon pointer" id="clearSearch"><span class="glyphicon glyphicon-remove">' .
									'</span></span>';
								}
							?>
							<span class="input-group-addon pointer" id="basic-addon">
							<span class="glyphicon glyphicon-search"></span></span>
						</div>
						<br/>
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
						<table class="table" id="table_questions">
							<thead>
								<tr>
									<th width="5%" class="middle"><input type="checkbox" id="ckSelectAll" name="ckSelectAll"></th>
									<th width="10%">Category</th>
									<th>Question</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								if (!$questions->num_rows) {
									echo '<tr><td colspan="3" class="middle empty">No results</td></tr>';
								}
								else {
									while ($question = $questions->fetch_array()) { 
							?>
								<tr>
									<td class="middle">
										<input type="checkbox" value="<?php echo $question[0]; ?>" name="ckSelectItem[]" 
										class="individualCheckbox">
										</td>
									<td><?php echo $question[3]; ?></td>
									<td><a class="triggerQuestionEdit" href="#" tag_id="<?php echo $question[0]; ?>" tag_question="<?php echo $question[1]; ?>"
									tag_answer="<?php echo $question[2]; ?>" tag_theme="<?php echo $question[4]; ?>">
										<?php echo $question[1]; ?>
									</a></td>
								</tr>
							<?php
									}
								}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</form>

				<center>
<?php
	$current_page = $_GET["page"];
	if (!$_GET["page"]) {
		$current_page = 1;
	}
	$count_pages = ceil($count_rows / Config::$items_per_page);
	if ($count_pages == 0) {
		$count_pages = 1;
	}
	$current_page = max(1, min($count_pages, $current_page));
	$class_previous = "";
	$class_next = "";
	if ($_GET["search"]) {
		$qstring = "search=" . htmlentities($_GET['search']) . "&page=";
	}
	else {
		$qstring = "page=";
	}
	$href_previous = "href=\"question.php?" . $qstring . ($current_page - 1) ."\"";
	$href_next = "href=\"question.php?" . $qstring . ($current_page + 1) ."\"";
	if ($current_page == 1) {
		$class_previous = 'class="disabled"';
		$href_previous = "";
	}
	if ($current_page == $count_pages) {
		$class_next = 'class="disabled"';
		$href_next = "";
	}
?>
					<nav>
						<ul class="pagination">
						<li <?php echo $class_previous; ?>>
							<a <?php echo $href_previous; ?> aria-label="Previous">
								<span aria-hidden="true">&laquo;</span>
							</a>
						</li>
<?php
	for ($i=1; $i<=$count_pages; $i++) {
		if ($i == $current_page) {
			echo "<li class=\"active\"><a href=\"question.php?$qstring$i\">$i</a></li>";
		}
		else {
			echo "<li><a href=\"question.php?$qstring$i\">$i</a></li>";
		}
	}
?>
						<li <?php echo $class_next; ?>>
							<a <?php echo $href_next; ?> aria-label="Next">
								<span aria-hidden="true">&raquo;</span>
							</a>
						</li>
						</ul>
					</nav>
				</center>

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
			Utils::display_admin_navbar("Question Manager");
			display_question_panel($_GET["page"]);
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
		Utils::display_admin_navbar("Question Manager");
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
		display_question_panel($_GET["page"]);
	}
	else if ($_POST["actionQuestionChange"] == "add" && $_POST["txtQuestion"] && $_POST["txtAnswer"] && $_POST["selTheme"]) {
		Utils::display_admin_navbar("Question Manager");
		if ($trivia->add_question($_POST["txtQuestion"], $_POST["txtAnswer"], $_POST["selTheme"])) {
			echo '<div class="container"><div class="alert alert-success col-sm-6 col-sm-offset-3">Question successfully added.</div></div>';
		}
		else {
			echo '<div class="container"><div class="alert alert-danger col-sm-6 col-sm-offset-3">Error when trying to add the question.</div></div>';
		}
		display_question_panel($_GET["page"]);
	}
	else if ($_POST["actionQuestionChange"] == "edit" && $_POST["txtQuestion"] && $_POST["txtQuestionID"]) {
		Utils::display_admin_navbar("Question Manager");
		if ($trivia->update_question($_POST["txtQuestionID"], $_POST["txtQuestion"], $_POST["txtAnswer"], $_POST["selTheme"])) {
			echo '<div class="container"><div class="alert alert-success col-sm-6 col-sm-offset-3">Question successfully updated.</div></div>';
		}
		else {
			echo '<div class="container"><div class="alert alert-danger col-sm-6 col-sm-offset-3">Error when trying to update the question.</div></div>';
		}
		display_question_panel($_GET["page"]);
	}
	else if ($_GET["search"]) {
		Utils::display_admin_navbar("Question Manager");
		display_question_panel($_GET["page"], $_GET["search"]);
	}
	else if ($_SESSION["username"] && $_SESSION["password"]) {
		Utils::display_admin_navbar("Question Manager");
		display_question_panel($_GET["page"]);
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
