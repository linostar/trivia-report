<?php
	require_once "conf/config.php";

	class Connection {
		private $conn;
		private $stmt_select_report_by_id;
		private $stmt_select_report_by_reason;
		private $stmt_select_report_by_state;
		private $stmt_select_report_by_reason_state;
		private $stmt_select_reason_name;
		private $stmt_insert_report;
		private $stmt_delete_report;
		private $stmt_update_report_state;
		private $report_id;
		private $reason_id;
		private $reason_name;
		private $question;
		private $comment;
		private $state;

		public function start() {
			$conn =& $this->conn;
			$stmt_select_report_by_id =& $this->stmt_select_report_by_id;
			$stmt_select_report_by_reason =& $this->stmt_select_report_by_reason;
			$stmt_select_report_by_state =& $this->stmt_select_report_by_state;
			$stmt_select_report_by_reason_state =& $this->stmt_select_report_by_reason_state;
			$stmt_select_reason_name =& $this->stmt_select_reason_name;
			$stmt_insert_report =& $this->stmt_insert_report;
			$stmt_delete_report =& $this->stmt_delete_report;
			$stmt_update_report_state =& $this->stmt_update_report_state;

			$conn = new mysqli(Config::$conn_report_host, Config::$conn_report_user, Config::$conn_report_pass, Config::$conn_report_db);
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
			$stmt_select_reason_name = $conn->prepare("SELECT reason_name FROM reasons WHERE reason_id=?");
			$stmt_insert_report = $conn->prepare("INSERT INTO reports (reason_id, question, comment, state) VALUES (?, ?, ?, 0)");
			$stmt_delete_report = $conn->prepare("DELETE FROM reports WHERE report_id=?");
			$stmt_update_report_state = $conn->prepare("UPDATE reports SET state=? WHERE report_id=?");

			$stmt_select_report_by_id->bind_param("i", $this->report_id);
			$stmt_select_report_by_reason->bind_param("i", $this->reason_id);
			$stmt_select_report_by_state->bind_param("i", $this->state);
			$stmt_select_report_by_reason_state->bind_param("ii", $this->reason_id, $this->state);
			$stmt_select_reason_name->bind_param("i", $this->reason_id);
			$stmt_insert_report->bind_param("iss", $this->reason_id, $this->question, $this->comment);
			$stmt_delete_report->bind_param("i", $this->report_id);
			$stmt_update_report_state->bind_param("ii", $this->state, $this->report_id);
		}

		public function stop() {
			$conn =& $this->conn;
			$stmt_select_report_by_id =& $this->stmt_select_report_by_id;
			$stmt_select_report_by_reason =& $this->stmt_select_report_by_reason;
			$stmt_select_report_by_state =& $this->stmt_select_report_by_state;
			$stmt_select_report_by_reason_state =& $this->stmt_select_report_by_reason_state;
			$stmt_select_reason_name =& $this->stmt_select_reason_name;
			$stmt_insert_report =& $this->stmt_insert_report;
			$stmt_delete_report =& $this->stmt_delete_report;
			$stmt_update_report_state =& $this->stmt_update_report_state;

			$stmt_select_report_by_id->close();
			$stmt_select_report_by_reason->close();
			$stmt_select_report_by_state->close();
			$stmt_select_report_by_reason_state->close();
			$stmt_select_reason_name->close();
			$stmt_insert_report->close();
			$stmt_delete_report->close();
			$stmt_update_report_state->close();
			$conn->close();
		}

		public function add_report($n_reason, $n_question, $n_comment) {
			$stmt_insert_report =& $this->stmt_insert_report;
			$this->reason_id = $n_reason;
			$this->question = $n_question;
			$this->comment = $n_comment;
			$stmt_insert_report->execute();
		}

		public function delete_report($n_report) {
			$stmt_delete_report =& $this->stmt_delete_report;
			$this->report_id = $n_report;
			$stmt_delete_report->execute();
		}

		public function update_state($n_report, $n_state) {
			$stmt_update_report_state =& $this->stmt_update_report_state;
			$this->report_id = $n_report;
			$this->state = $n_state;
			$stmt_update_report_state->execute();
		}

		public function get_all_reasons() {
			$conn =& $this->conn;
			return $conn->query("SELECT reason_id, reason_name FROM reasons ORDER BY reason_id ASC");
		}
	}
?>
