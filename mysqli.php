<?php
	require_once "conf/config.php";

	class Reporter_DB {
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
		private $count_per_page;

		public function start() {
			$conn =& $this->conn;
			$this->count_per_page = Config::$items_per_page;
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
				"SELECT rp.question, rp.theme_id, rp.comment, rp.state, DATE(rp.date) AS cdate, re.reason_name, rp.report_id " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=re.reason_id AND rp.report_id=?"
				);
			$stmt_select_report_by_reason = $conn->prepare(
				"SELECT rp.question, rp.theme_id, rp.comment, rp.state, DATE(rp.date), re.reason_name, rp.report_id " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=? AND rp.reason_id=re.reason_id " .
				"ORDER BY rp.report_id DESC LIMIT ?, ?"
				);
			$stmt_select_report_by_state = $conn->prepare(
				"SELECT rp.question, rp.theme_id, rp.comment, rp.state, DATE(rp.date), re.reason_name, rp.report_id " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=re.reason_id AND rp.state=? " .
				"ORDER BY rp.report_id DESC LIMIT ?, ?"
				);
			$stmt_select_report_by_reason_state = $conn->prepare(
				"SELECT rp.question, rp.theme_id, rp.comment, rp.state, DATE(rp.date), re.reason_name, rp.report_id " . 
				"FROM reports AS rp JOIN reasons AS re ON rp.reason_id=? AND rp.state=? " .
				"AND rp.reason_id=re.reason_id ORDER BY rp.report_id DESC LIMIT ?, ?"
				);
			$stmt_select_reason_name = $conn->prepare("SELECT reason_name FROM reasons WHERE reason_id=?");
			$stmt_insert_report = $conn->prepare("INSERT INTO reports (reason_id, question, comment, state, theme_id) VALUES (?, ?, ?, 0, ?)");
			$stmt_delete_report = $conn->prepare("DELETE FROM reports WHERE report_id=?");
			$stmt_update_report_state = $conn->prepare("UPDATE reports SET state=? WHERE report_id=?");

			$stmt_select_report_by_id->bind_param("i", $this->report_id);
			$stmt_select_report_by_reason->bind_param("iii", $this->reason_id, $this->page_num, $this->count_per_page);
			$stmt_select_report_by_state->bind_param("iii", $this->state, $this->page_num, $this->count_per_page);
			$stmt_select_report_by_reason_state->bind_param("iiii", $this->reason_id, $this->state, $this->page_num, $this->count_per_page);
			$stmt_select_reason_name->bind_param("i", $this->reason_id);
			$stmt_insert_report->bind_param("issi", $this->reason_id, $this->question, $this->comment, $this->theme);
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
			if (!$stmt_insert_report->execute()) {
				echo "Execute failed: (" . $stmt_insert_report->errno . ") " . $stmt_insert_report->error;
				return false;
			}
			return true;
		}

		public function delete_report($n_report) {
			$stmt_delete_report =& $this->stmt_delete_report;
			$this->report_id = $n_report;
			if(!$stmt_delete_report->execute()) {
				echo "Execute failed: (" . $stmt_delete_report->errno . ") " . $stmt_delete_report->error;
				return false;
			}
			return true;
		}

		public function update_state($n_report, $n_state) {
			$stmt_update_report_state =& $this->stmt_update_report_state;
			$this->report_id = $n_report;
			$this->state = $n_state;
			if (!$stmt_update_report_state->execute()) {
				echo "Execute failed: (" . $stmt_update_report_state->errno . ") " . $stmt_update_report_state->error;
				return false;
			}
			return true;
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

		public function get_single_report($n_report) {
			$stmt_select_report_by_id =& $this->stmt_select_report_by_id;
			$this->report_id = $n_report;
			if (!$stmt_select_report_by_id->execute())
				echo "Execute failed: (" . $stmt_select_report_by_id->errno . ") " . $stmt_select_report_by_id->error;
			return $stmt_select_report_by_id->get_result();
		}

		public function get_reports($n_page = 1) {
			$conn =& $this->conn;
			$per_page = $this->count_per_page;
			$start = ($n_page - 1) * $per_page;
			$result = $conn->query("SELECT rp.question, rp.theme_id, rp.comment, rp.state, DATE(rp.date), re.reason_name, " .
				"rp.report_id FROM reports AS rp JOIN reasons AS re ON rp.reason_id = re.reason_id " .
				"ORDER BY rp.report_id DESC LIMIT $start, $per_page");
			$count = $conn->query("SELECT COUNT(*) FROM reports");
			return array($result, $count->fetch_array()[0]);
		}

		public function filter_report_state($n_state, $n_page = 1) {
			$conn =& $this->conn;
			$this_page_num = ($n_page - 1) * $this->count_per_page;
			$count = $conn->query("SELECT COUNT(*) FROM reports WHERE state=$n_state");
			$stmt_select_report_by_state =& $this->stmt_select_report_by_state;
			$this->state = $n_state;
			if (!$stmt_select_report_by_state->execute())
				echo "Execute failed: (" . $stmt_select_report_by_state->errno . ") " . $stmt_select_report_by_state->error;
			return array($stmt_select_report_by_state->get_result(), $count->fetch_array()[0]);
		}

		public function filter_report_reason($n_reason, $n_page = 1) {
			$conn =& $this->conn;
			$this_page_num = ($n_page - 1) * $this->count_per_page;
			$count = $conn->query("SELECT COUNT(*) FROM reports WHERE reason_id=$n_reason");
			$stmt_select_report_by_reason =& $this->stmt_select_report_by_reason;
			$this->page_num = ($n_page - 1) * $this->count_per_page;
			$this->reason_id = $n_reason;
			if (!$stmt_select_report_by_reason->execute())
				echo "Execute failed: (" . $stmt_select_report_by_reason->errno . ") " . $stmt_select_report_by_reason->error;
			return array($stmt_select_report_by_reason->get_result(), $count->fetch_array()[0]);
		}
	}

	class Trivia_DB {
		private $conn;
		private $stmt_select_question;
		private $stmt_insert_question;
		private $stmt_update_question;
		private $stmt_delete_question;
		private $stmt_search_question;
		private $stmt_insert_theme;
		private $stmt_update_theme;
		private $stmt_delete_theme;
		private $question;
		private $answer;
		private $theme;
		private $theme_name;
		private $question_id;
		private $count_per_page;

		public function start() {
			$conn =& $this->conn;
			$this->count_per_page = Config::$items_per_page;
			$stmt_select_question =& $this->stmt_select_question;
			$stmt_insert_question =& $this->stmt_insert_question;
			$stmt_update_question =& $this->stmt_update_question;
			$stmt_delete_question =& $this->stmt_delete_question;
			$stmt_search_question =& $this->stmt_search_question;
			$stmt_insert_theme =& $this->stmt_insert_theme;
			$stmt_update_theme =& $this->stmt_update_theme;
			$stmt_delete_theme =& $this->stmt_delete_theme;

			$conn = new mysqli(Config::$conn_trivia_host, Config::$conn_trivia_user, Config::$conn_trivia_pass, Config::$conn_trivia_db);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			$stmt_select_question = $conn->prepare("SELECT * FROM `trivia_questions` WHERE `question` LIKE ?");
			$stmt_insert_question = $conn->prepare("INSERT INTO `trivia_questions` (`question`, `answer`, `theme_id`) " .
				"VALUES (?, ?, ?)");
			$stmt_update_question = $conn->prepare("UPDATE `trivia_questions` SET `question`=?, `answer`=?, `theme_id`=? WHERE `id`=?");
			$stmt_delete_question = $conn->prepare("DELETE FROM `trivia_questions` WHERE `id`=?");
			$stmt_search_question = $conn->prepare("SELECT q.id, q.question, q.answer, t.theme_name, t.theme_id FROM trivia_questions AS q " .
				"JOIN trivia_themes AS t ON q.theme_id=t.theme_id AND q.question LIKE ? ORDER BY q.id DESC LIMIT ?, ?");
			$stmt_insert_theme = $conn->prepare("INSERT INTO `trivia_themes` (`theme_name`) VALUES (?)");
			$stmt_update_theme = $conn->prepare("UPDATE `trivia_themes` SET `theme_name`=? WHERE `theme_id`=?");
			$stmt_delete_theme = $conn->prepare("DELETE FROM `trivia_themes` WHERE `theme_id`=?");

			$stmt_select_question->bind_param("s", $this->question);
			$stmt_insert_question->bind_param("ssi", $this->question, $this->answer, $this->theme);
			$stmt_update_question->bind_param("ssii", $this->question, $this->answer, $this->theme, $this->question_id);
			$stmt_delete_question->bind_param("i", $this->question_id);
			$stmt_search_question->bind_param("sii", $this->question, $this->page_num, $this->count_per_page);
			$stmt_insert_theme->bind_param("s", $this->theme_name);
			$stmt_update_theme->bind_param("si", $this->theme_name, $this->theme);
			$stmt_delete_theme->bind_param("i", $this->theme);
		}

		public function stop() {
			$conn =& $this->conn;
			$stmt_select_question =& $this->stmt_select_question;
			$stmt_insert_question =& $this->stmt_insert_question;
			$stmt_update_question =& $this->stmt_update_question;
			$stmt_delete_question =& $this->stmt_delete_question;
			$stmt_search_question =& $this->stmt_search_question;
			$stmt_insert_theme =& $this->stmt_insert_theme;
			$stmt_update_theme =& $this->stmt_update_theme;
			$stmt_delete_theme =& $this->stmt_delete_theme;
			$stmt_select_question->close();
			$stmt_insert_question->close();
			$stmt_update_question->close();
			$stmt_delete_question->close();
			$stmt_search_question->close();
			$stmt_insert_theme->close();
			$stmt_update_theme->close();
			$stmt_delete_theme->close();
			$conn->close();
		}

		public function get_question($n_question) {
			$stmt_select_question =& $this->stmt_select_question;
			$this->question = trim($n_question, ' \t\n\r\0\x0B.!,;:?[](){}<>%');
			$this->question = "%" . $this->question . "%";
			if (!$stmt_select_question->execute())
				echo "Execute failed: (" . $stmt_select_question->errno . ") " . $stmt_select_question->error;
			return $stmt_select_question->get_result();
		}

		public function search_question($n_question, $n_page) {
			if (!$n_page) {
				$n_page = 1;
			}
			$conn =& $this->conn;
			$stmt_search_question =& $this->stmt_search_question;
			$per_page = $this->count_per_page;
			$this->page_num = ($n_page - 1) * $per_page;
			$this->question = trim($n_question);
			$this->question = "%" . $this->question . "%";
			$question = $this->question;
			$count = $conn->query("SELECT COUNT(*) FROM `trivia_questions` WHERE `question` LIKE '$question'");
			if (!$stmt_search_question->execute())
				echo "Execute failed: (" . $stmt_search_question->errno . ") " . $stmt_search_question->error;
			$result = $stmt_search_question->get_result();
			return array($result, $count->fetch_array()[0]);
		}

		public function update_question($n_id, $n_question, $n_answer, $n_theme) {
			$stmt_update_question =& $this->stmt_update_question;
			$this->question_id = $n_id;
			$this->question = $n_question;
			$this->answer = $n_answer;
			$this->theme = $n_theme;
			if (!$stmt_update_question->execute()) {
				echo "Execute failed: (" . $stmt_update_question->errno . ") " . $stmt_update_question->error;
				return false;
			}
			return true;
		}

		public function add_question($n_question, $n_answer, $n_theme) {
			$stmt_insert_question =& $this->stmt_insert_question;
			$this->question = $n_question;
			$this->answer = $n_answer;
			$this->theme = $n_theme;
			if (!$stmt_insert_question->execute()) {
				echo "Execute failed: (" . $stmt_insert_question->errno . ") " . $stmt_insert_question->error;
				return false;
			}
			return true;
		}

		public function delete_question($n_id) {
			$stmt_delete_question =& $this->stmt_delete_question;
			$this->question_id = $n_id;
			if (!$stmt_delete_question->execute()) {
				echo "Execute failed: (" . $stmt_delete_question->errno . ") " . $stmt_delete_question->error;
				return false;
			}
			return true;
		}

		public function get_questions($n_page) {
			if (!$n_page) {
				$n_page = 1;
			}
			$conn =& $this->conn;
			$per_page = $this->count_per_page;
			$start = ($n_page - 1) * $per_page;
			$result = $conn->query("SELECT q.id, q.question, q.answer, t.theme_name, t.theme_id FROM trivia_questions AS q " .
				"JOIN trivia_themes AS t ON q.theme_id=t.theme_id " .
				"ORDER BY q.id DESC LIMIT $start, $per_page");
			$count = $conn->query("SELECT COUNT(*) FROM trivia_questions");
			return array($result, $count->fetch_array()[0]);
		}

		public function get_all_themes() {
			$conn =& $this->conn;
			return $conn->query("SELECT * FROM `trivia_themes` ORDER BY `theme_id` ASC");
		}

		public function get_themes_array() {
			$themes = $this->get_all_themes();
			while ($theme = $themes->fetch_array()) {
				$theme_id = $theme[0];
				$all_themes_names[$theme_id] = $theme[1];
			}
			return $all_themes_names;
		}

		public function delete_theme($n_theme) {
			$stmt_delete_theme =& $this->stmt_delete_theme;
			$this->theme = $n_theme;
			if (!$stmt_delete_theme->execute()) {
				echo "Execute failed: (" . $stmt_delete_theme->errno . ") " . $stmt_delete_theme->error;
				return false;
			}
			return true;
		}

		public function add_theme($n_theme_name) {
			$stmt_insert_theme =& $this->stmt_insert_theme;
			$this->theme_name = $n_theme_name;
			if (!$stmt_insert_theme->execute()) {
				echo "Execute failed: (" . $stmt_insert_theme->errno . ") " . $stmt_insert_theme->error;
				return false;
			}
			return true;
		}

		public function edit_theme($n_theme, $n_theme_name) {
			$stmt_update_theme =& $this->stmt_update_theme;
			$this->theme_name = $n_theme_name;
			$this->theme = $n_theme;
			if (!$stmt_update_theme->execute()) {
				echo "Execute failed: (" . $stmt_update_theme->errno . ") " . $stmt_update_theme->error;
				return false;
			}
			return true;
		}
	}
?>
