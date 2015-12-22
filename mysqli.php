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
		private $theme;
		private $page_num;
		private $count_per_page = 25;

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
				"SELECT rp.question, rp.theme, rp.comment, rp.state, DATE(rp.date), re.reason_name, rp.report_id " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=re.reason_id AND rp.report_id=?"
				);
			$stmt_select_report_by_reason = $conn->prepare(
				"SELECT rp.question, rp.theme, rp.comment, rp.state, DATE(rp.date), re.reason_name, rp.report_id " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=? AND rp.reason_id=re.reason_id " .
				"ORDER BY rp.report_id DESC LIMIT ?, $this->count_per_page"
				);
			$stmt_select_report_by_state = $conn->prepare(
				"SELECT rp.question, rp.theme, rp.comment, rp.state, DATE(rp.date), re.reason_name, rp.report_id " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=re.reason_id AND rp.state=? " .
				"ORDER BY rp.report_id DESC LIMIT ?, $this->count_per_page"
				);
			$stmt_select_report_by_reason_state = $conn->prepare(
				"SELECT rp.question, rp.theme, rp.comment, rp.state, DATE(rp.date), re.reason_name, rp.report_id " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=? AND rp.state=? " .
				"AND rp.reason_id=re.reason_id ORDER BY rp.report_id DESC LIMIT ?, $this->count_per_page"
				);
			$stmt_select_reason_name = $conn->prepare("SELECT reason_name FROM reasons WHERE reason_id=?");
			$stmt_insert_report = $conn->prepare("INSERT INTO reports (reason_id, question, comment, state, theme) VALUES (?, ?, ?, 0, ?)");
			$stmt_delete_report = $conn->prepare("DELETE FROM reports WHERE report_id=?");
			$stmt_update_report_state = $conn->prepare("UPDATE reports SET state=? WHERE report_id=?");

			$stmt_select_report_by_id->bind_param("i", $this->report_id);
			$stmt_select_report_by_reason->bind_param("ii", $this->reason_id, $this->page_num);
			$stmt_select_report_by_state->bind_param("ii", $this->state, $this->page_num);
			$stmt_select_report_by_reason_state->bind_param("iii", $this->reason_id, $this->state, $this->page_num);
			$stmt_select_reason_name->bind_param("i", $this->reason_id);
			$stmt_insert_report->bind_param("isss", $this->reason_id, $this->question, $this->comment, $this->theme);
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

		public function add_report($n_reason, $n_question, $n_comment, $n_theme) {
			$stmt_insert_report =& $this->stmt_insert_report;
			$this->reason_id = $n_reason;
			$this->question = $n_question;
			$this->comment = $n_comment;
			$this->theme = $n_theme;
			if (!$stmt_insert_report->execute())
				echo "Execute failed: (" . $stmt_insert_report->errno . ") " . $stmt_insert_report->error;
		}

		public function delete_report($n_report) {
			$stmt_delete_report =& $this->stmt_delete_report;
			$this->report_id = $n_report;
			if(!$stmt_delete_report->execute())
				echo "Execute failed: (" . $stmt_delete_report->errno . ") " . $stmt_delete_report->error;
		}

		public function update_state($n_report, $n_state) {
			$stmt_update_report_state =& $this->stmt_update_report_state;
			$this->report_id = $n_report;
			$this->state = $n_state;
			if (!$stmt_update_report_state->execute())
				echo "Execute failed: (" . $stmt_update_report_state->errno . ") " . $stmt_update_report_state->error;
		}

		public function get_all_reasons() {
			$conn =& $this->conn;
			return $conn->query("SELECT reason_id, reason_name FROM reasons ORDER BY reason_id ASC");
		}

		public function check_reason_exists($n_reason = -1) {
			$stmt_select_reason_name =& $this->stmt_select_reason_name;
			$this->reason_id = $n_reason;
			if (!$stmt_select_reason_name->execute())
				echo "Execute failed: (" . $stmt_select_reason_name->errno . ") " . $stmt_select_reason_name->error;
			$stmt_select_reason_name->store_result();
			$num_rows = $stmt_select_reason_name->num_rows;
			$stmt_select_reason_name->free_result();
			if ($num_rows > 0)
				return true;
			else
				return false;
		}

		public function get_reports($n_page = 1) {
			$conn =& $this->conn;
			$this->page_num = ($n_page - 1) * $this->count_per_page;
			$result = $conn->query("SELECT rp.question, rp.theme, rp.comment, rp.state, DATE(rp.date), re.reason_name, " .
				"rp.report_id FROM reports AS rp JOIN reasons AS re ON rp.reason_id = re.reason_id " .
				"ORDER BY rp.report_id DESC LIMIT $this->page_num, $this->count_per_page");
			$count = $conn->query("SELECT COUNT(*) FROM reports");
			return array($result, $count->fetch_array()[0]);
		}

		public function filter_report_state($n_state, $n_page = 1) {
			$conn =& $this->conn;
			$count = $conn->query("SELECT COUNT(*) FROM reports WHERE state=$n_state");
			$stmt_select_report_by_state =& $this->stmt_select_report_by_state;
			$this->page_num = ($n_page - 1) * $this->count_per_page;
			$this->state = $n_state;
			if (!$stmt_select_report_by_state->execute())
				echo "Execute failed: (" . $stmt_select_report_by_state->errno . ") " . $stmt_select_report_by_state->error;
			return array($stmt_select_report_by_state->get_result(), $count->fetch_array()[0]);
		}

		public function filter_report_reason($n_reason, $n_page = 1) {
			$conn =& $this->conn;
			$count = $conn->query("SELECT COUNT(*) FROM reports WHERE reason_id=$n_reason");
			$stmt_select_report_by_reason =& $this->stmt_select_report_by_reason;
			$this->page_num = ($n_page - 1) * $this->count_per_page;
			$this->reason_id = $n_reason;
			if (!$stmt_select_report_by_reason->execute())
				echo "Execute failed: (" . $stmt_select_report_by_reason->errno . ") " . $stmt_select_report_by_reason->error;
			return array($stmt_select_report_by_reason->get_result(), $count->fetch_array()[0]);
		}
	}
?>
