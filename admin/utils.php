<?php
	require_once "../conf/config.php";

	class Utils {
		public static function login($user, $pass) {
			if (Config::$admin_user == $user && Config::$admin_pass == $pass) {
				$_SESSION["username"] = $user;
				$_SESSION["password"] = hash("sha256", $pass);
				return true;
			}
			else {
				return false;
			}
		}
		
		public static function logout() {
			session_unset();
		}

		public static function display_login() {
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

		public static function display_admin_navbar($title = "Admin Panel") {
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
				<a class="navbar-brand" style="background-color: black;"><?php echo $title; ?></a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="index.php">Admin Home</a></li>
					<li><a href="../index.php">Trivia Reporter</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" 
						aria-expanded="false">Trivia Editor <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="question.php">Manage Questions</a></li>
							<li><a href="theme.php">Manage Categories</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li ><a href="?action=logout">Logout</a></li>
				</ul>
			</div>
		</div>
	</nav>
<?php
		}
	}
?>
