<?php
	require_once "conf/config.php";

	class Report_DB {
		public $stmt;
		public $conn;

		public function start() {
			$conn = new mysqli($conn_report_host, $conn_report_user, $conn_report_pass, $conn_report_db);
		}

		public function stop() {
			$stmt->close();
			$conn->close();
		}
	}
?>
