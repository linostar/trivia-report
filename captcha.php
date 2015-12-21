<?php
	require_once "conf/config.php";

	class Captcha {
		public static function calculate() {
			$key = Config::$captcha_secret_key;
			$num1 = rand(2, 12);
			$num2 = rand(2, 12);
			$sum = $num1 + $num2;
			$equation = $num1 . " + " . $num2 . " = ?";
			return array($equation, hash("sha256", $sum . $key));
		}

		public static function check($sum, $hash) {
			$key = Config::$captcha_secret_key;
			return ($hash == hash("sha256", $sum . $key));
		}
	}
?>
