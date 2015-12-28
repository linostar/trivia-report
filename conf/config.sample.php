<?php
	class Config {
		// Debug switch for the admin pages
		public static $debug_admin = false;
		
		// Debug switch for the user pages
		public static $debug_user = false;

		// Credentials for the admin panel
		public static $admin_user = "admin";
		public static $admin_pass = "foo";

		// Secret key for Captcha equation
		public static $captcha_secret_key = "ChangeMe123!";

		// Number of items to be displayed per page
		public static $items_per_page = 25;

		// MySQL config for trivia-report db access
		public static $conn_report_host = "localhost";
		public static $conn_report_db = "trivia_report_db";
		public static $conn_report_user = "trivia_report_user";
		public static $conn_report_pass = "foo";

		// MySQL config for Trivia bot's db access (for retrieving trivia questions and answers)
		public static $conn_trivia_host = "";
		public static $conn_trivia_db = "";
		public static $conn_trivia_user = "";
		public static $conn_trivia_pass = "";
	}
?>
