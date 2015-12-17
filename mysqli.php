<?php
	require_once "conf/config.php";

	class Connection {
		private $conn;
		private $stmt_select_report_by_id;
		private $stmt_select_report_by_cat;
		private $stmt_insert_report;
		private $stmt_delete_report;
		private $stmt_update_report_state;
		private $report_id;
		private $reason_id;
		private $question;
		private $comment;
		private $state;

		public function start() {
			$conn = new mysqli($conn_report_host, $conn_report_user, $conn_report_pass, $conn_report_db);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			$stmt_select_report_by_id = $conn->prepare(
				"SELECT rp.question, rp.comment rp.state, re.reason_name " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=re.reason_id AND rp.report_id=?"
				);
			$stmt_select_report_by_cat = $conn->prepare(
				"SELECT rp.question, rp.comment rp.state, re.reason_name " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=re.reason_id AND rp.reason_id=?"
				);
			$stmt_insert_report = $conn->prepare("INSERT INTO reports (reason_id, question, comment, state) VALUES (?, ?, ?, 0)");
			$stmt_delete_report = $conn->prepare("DELETE FROM reports WHERE report_id=?");
			$stmt_update_report_state = $conn->prepare("UPDATE reports SET state=? WHERE report_id=?");

			$stmt_select_report_by_id->bind_param("i", $report_id);
			$stmt_select_report_by_cat->bind_param("i", $reason_id);
			$stmt_insert_report->bind_param("iss", $reason_id, $question, $comment);
			$stmt_delete_report->bind_param("i", $report_id);
			$stmt_update_report_state->bind_param("ii", $state, $report_id);
		}

		public function stop() {
			$stmt_select_report_by_id->close();
			$stmt_select_report_by_cat->close();
			$stmt_insert_report->close();
			$stmt_delete_report->close();
			$stmt_update_report_state->close();
			$conn->close();
		}
	}
?>
