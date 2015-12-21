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
	}
?>
