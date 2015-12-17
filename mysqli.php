<?php
	require_once "conf/config.php";

	class Connection {
		private $conn;
		private $stmt_select_report_by_id;
		private $stmt_select_report_by_reason;
		private $stmt_select_report_by_state;
		private $stmt_select_report_by_reason_state;
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
				"SELECT rp.question, rp.comment, rp.state, DATE(rp.date), re.reason_name " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=re.reason_id AND rp.report_id=?"
				);
			$stmt_select_report_by_reason = $conn->prepare(
				"SELECT rp.question, rp.comment, rp.state, DATE(rp.date), re.reason_name " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=? AND rp.reason_id=re.reason_id " .
				"ORDER BY rp.report_id DESC"
				);
			$stmt_select_report_by_state = $conn->prepare(
				"SELECT rp.question, rp.comment, rp.state, DATE(rp.date), re.reason_name " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=re.reason_id AND rp.state=? " .
				"ORDER BY rp.report_id DESC"
				);
			$stmt_select_report_by_reason_state = $conn->prepare(
				"SELECT rp.question, rp.comment, rp.state, DATE(rp.date), re.reason_name " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=? AND rp.state=? " .
				"AND rp.reason_id=re.reason_id ORDER BY rp.report_id DESC"
				);
			$stmt_insert_report = $conn->prepare("INSERT INTO reports (reason_id, question, comment, state) VALUES (?, ?, ?, 0)");
			$stmt_delete_report = $conn->prepare("DELETE FROM reports WHERE report_id=?");
			$stmt_update_report_state = $conn->prepare("UPDATE reports SET state=? WHERE report_id=?");

			$stmt_select_report_by_id->bind_param("i", $report_id);
			$stmt_select_report_by_reason->bind_param("i", $reason_id);
			$stmt_select_report_by_state->bind_param("i", $state);
			$stmt_select_report_by_reason_state->bind_param("ii", $reason_id, $state);
			$stmt_insert_report->bind_param("iss", $reason_id, $question, $comment);
			$stmt_delete_report->bind_param("i", $report_id);
			$stmt_update_report_state->bind_param("ii", $state, $report_id);
		}

		public function stop() {
			$stmt_select_report_by_id->close();
			$stmt_select_report_by_reason->close();
			$stmt_select_report_by_state->close();
			$stmt_select_report_by_reason_state->close();
			$stmt_insert_report->close();
			$stmt_delete_report->close();
			$stmt_update_report_state->close();
			$conn->close();
		}
	}
?>
