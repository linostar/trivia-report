<?php
	require_once "conf/config.php";

	if (Config::$debug_user) {
		ini_set('display_errors', 'On');
		error_reporting(E_ALL);
	}
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Rizon - Trivia Reporter</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrapValidator.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div class="userpage_header">
		<div class="container">
			<div class="header_container col-sm-8 col-sm-offset-2">
				<div>
					<img src="https://abuse.rizon.net/images/logo.png">
				</div>
				<div>
					<ul class="nav_menu">
						<li><a href="https://rizon.net/">Home</a></li>
						<li><a href="https://rizon.net/chat/">Chat</a></li>
						<li><a href="http://forum.rizon.net/">Forums</a></li>
						<li><a href="https://abuse.rizon.net/">Ban appeal</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
	<?php
		require_once "mysqli.php";
		require_once "captcha.php";

		$db = new Connection;
		$db->start();

		$trivia = new Trivia_DB;
		$trivia->start();

		list($equation, $hashedCaptcha) = Captcha::calculate();
		$all_reasons = $db->get_all_reasons();
		$all_themes_names = $trivia->get_themes_array();

		if ($_POST["selReason"] && $_POST["txtQuestion"] && $_POST["txtCaptcha"] && $_POST["selTheme"]) {
			if (!$db->check_reason_exists($_POST["selReason"])) {
				echo '<div id="user_alert" class="alert alert-danger col-sm-8 col-sm-offset-2">Invalid type of mistake.</div>';
			}
			else if (Captcha::check($_POST["txtCaptcha"], $_POST["hashedCaptcha"])) {
				if (array_key_exists($_POST["selTheme"], $all_themes_names)) {
					$theme_id = $_POST["selTheme"];
				}
				else {
					$theme_id = 1;
				}
				if ($db->add_report($_POST["selReason"], $_POST["txtQuestion"], $_POST["txtComment"], $theme_id)) {
					echo '<div id="user_alert" class="alert alert-success col-sm-8 col-sm-offset-2">Report successfully submitted. ' .
					'Thank you for notifying us about this mistake.</div>';
				}
				else {
					echo '<div id="user_alert" class="alert alert-danger col-sm-8 col-sm-offset-2">Error occured when submitting report. ' .
					'Please try again later.</div>';
				}
			}
			else {
				echo '<div id="user_alert" class="alert alert-danger col-sm-8 col-sm-offset-2">The answer to the equation is ' .
				'<b>incorrect</b>. Try again.</div>';
			}
		}
		else if ($_POST["txtQuestion"]) {
			echo '<div id="user_alert" class="alert alert-danger col-sm-8 col-sm-offset-2">Please solve the equation correctly.</div>';
		}
		else if ($_POST["txtCaptcha"]) {
			echo '<div id="user_alert" class="alert alert-danger col-sm-8 col-sm-offset-2">Adding the question there the mistake ' .
			'occured is required.</div>';
		}
		else if ($_POST["selReason"]) {
			echo '<div id="user_alert" class="alert alert-danger col-sm-8 col-sm-offset-2">Please fill all required fields.</div>';
		}
	?>
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<h3 id="form_header">Report a mistake in Trivia bot's questions</h3>
			<form action="" method="post" id="formReport">
				<input type="hidden" name="hashedCaptcha" id="hashedCaptcha" value="<?php echo $hashedCaptcha; ?>">
				<div class="form-group">
					<label for="selReason" class="control-label">Type of mistake</label>
					<select name="selReason" id="selReason" class="form-control">
					<?php
						while ($row = $all_reasons->fetch_array(MYSQLI_NUM)) {
							echo "<option value=\"$row[0]\">$row[1]</option>";
						}
					?>
					</select>
				</div>
				<div class="form-group">
					<label for="selTheme" class="control-label">Question category</label>
					<select name="selTheme" id="selTheme" class="form-control">
					<?php
						foreach ($all_themes_names as $key => $value) {
							echo "<option value=\"$key\">$value</option>";
						}
					?>
					</select>
				</div>
				<div class="form-group">
					<label for="txtQuestion" class="control-label">Question where the mistake occured</label>
					<input type="text" id="txtQuestion" name="txtQuestion" value="" class="form-control" 
					placeholder="Copy the question from the IRC chat window">
				</div>
				<div class="form-group">
					<label for="txtComment" class="control-label">Your comment</label>
					<textarea id="txtComment" name="txtComment" class="form-control" 
					placeholder="Your correction or any other comment you want to add"></textarea>
				</div>
				<div class="form-group">
					<label for="txtCaptcha" class="control-label">Solve the following equation</label>
					<input type="text" name="txtCaptcha" id="txtCaptcha" value="" class="form-control" 
					placeholder="<?php echo $equation; ?>">
				</div>
				<div class="form-group">
					<button type="submit" name="reportButton" class="btn btn-primary">Report</button>
				</div>
			</form>
		</div>
	</div>
	</div>
	<br/><br/>
	<footer class="footer">
		<center>
			<div id="rizon_footer">© <?php echo date("Y"); ?> Rizon</div>
		</center>
	</footer>
	<?php
		$trivia->stop();
		$db->stop();
	?>
	<script src="js/jquery-2.1.4.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/ie10-viewport-bug-workaround.js"></script>
	<script src="js/bootstrapValidator.min.js"></script>
	<script src="js/misc.js"></script>
</body>
</html>