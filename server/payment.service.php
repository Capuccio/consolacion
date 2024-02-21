<?php
include_once("connect_db.php");

class PaymentService {
	protected $conn;
	protected $per_pages = 5;

	function __construct() {
		$db = ConnectDB::getInstance();
		$this->conn = $db->getConnection();
	}

	function __destruct() {
		$this->conn = null;
	}

	function createPayment($payment, $user) {
		$response = new stdClass();

		try {
			$sql_pay = "INSERT INTO payments (parent, type, reference_number, bank, unit_price, total, date, observations) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt_pay = $this->conn->prepare($sql_pay);
			$stmt_pay->execute(array($user, $payment["type"], $payment["reference_number"], $payment["bank"], $payment["unit_price"], $payment["total"], $payment["date"], $payment["observations"]));
			$billId = $this->conn->lastInsertId();

			$sql_monthspay = "INSERT INTO student_month_payment (student_month_payment_id_month, student_month_payment_id_students, student_month_payment_bill) VALUES (?, ?, ?)";
			$stmt_monthspay = $this->conn->prepare($sql_monthspay);
			for ($i = 0; $i < count($payment["students"]); $i++) { 
				for ($j = 0; $j < count($payment["months"]); $j++) {
					$stmt_monthspay->execute(array($payment["months"][$j], $payment["students"][$i], $billId));
				}
			}

			$response->data = $billId;
			$response->message = "Pago registrado correctamente";
			$response->statusCode = 201;
		} catch (\Throwable $th) {
			$response->statusCode = 500;
			$response->message = array($payment);
			$response->message = $th->getMessage();
		}

		return $response;
	}

	function updatePayment($payment) {}

	function deletePayment($payment) {
		$response = new stdClass();

		try {
			$sql_reference = "DELETE FROM student_month_payment WHERE bill = :bill";
			$stmt_ref = $this->conn->prepare($sql_reference);
			$stmt_ref->execute(array(":bill" => $payment['id']));
			$sql = "DELETE FROM payments WHERE bill = :bill";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array(":bill" => $payment['id']));
			$response->message = "Pago eliminado correctamente";
			$response->statusCode = 204;
		}
		catch (\Throwable $th) {
			$response->message = $th->getMessage();
			$response->statusCode = 500;
		}

		return $response;
	}

	function getPayment($payment_id) {
		$response = new stdClass();
		$sql = "SELECT *, (SELECT GROUP_CONCAT(DISTINCT students.student_name, ' ', students.student_last_name)) AS students_names, (SELECT GROUP_CONCAT(DISTINCT months.month)) AS months_payed FROM students INNER JOIN student_month_payment ON students.id_students = student_month_payment.student_month_payment_id_students INNER JOIN payments ON student_month_payment.student_month_payment_bill = payments.bill INNER JOIN parents ON payments.parent = parents.id_parents INNER JOIN months ON student_month_payment.student_month_payment_id_month = months.id_month WHERE payments.bill = :bill GROUP BY payments.bill";

		try {
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array(':bill' => $payment_id));
			$response->data = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (\Throwable $th) {
			$response->message = $th->getMessage();
			$response->statusCode = 500;
		}

		return $response;
	}

	function searchPayments($sort, $q, $date, $page = 1) {
		$response = new stdClass();

		try {
			$sql = "SELECT *, (SELECT GROUP_CONCAT(DISTINCT students.student_name, ' ', students.student_last_name)) AS students_names, (SELECT GROUP_CONCAT(DISTINCT months.month)) AS months_payed FROM students INNER JOIN student_month_payment ON students.id_students = student_month_payment.student_month_payment_id_students INNER JOIN payments ON student_month_payment.student_month_payment_bill = payments.bill INNER JOIN parents ON payments.parent = parents.id_parents INNER JOIN months ON student_month_payment.student_month_payment_id_month = months.id_month";

			if (!empty($q) || !empty($date)) {
				$sql .= " WHERE";
			}

			if (!empty($q)) {
				$sql .= " {$sort} LIKE :q";
				$params[":q"] = "%{$q}%";
			}

			if (!empty($date)) {
				if (!empty($q)) {
					$sql .= " AND";
				}
				$sql .= " date = :date";
				$params[":date"] = $date;
			}

			$sql .= " GROUP BY payments.bill LIMIT :limit OFFSET :offset";
			$params[":limit"] = $this->per_pages;
			$params[":offset"] = ($page - 1) * $this->per_pages;

			$stmt = $this->conn->prepare($sql);
			$stmt->execute($params);
			$pays = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$response->data = $pays;
			$response->message = "Todos los pagos";
			$response->statusCode = 200;
		} catch (\Throwable $th) {
			$response->data = [];
			$response->message = $th->getMessage();
			$response->statusCode = 500;
		}

		return $response;
	}

	function countSearchPayments($sort, $q, $date) {
		$response = new stdClass();
		$params = [];

//		$sql = "SELECT COUNT(*) FROM payments";
		$sql = "SELECT *, (SELECT GROUP_CONCAT(DISTINCT students.student_name, ' ', students.student_last_name)) AS students_names, (SELECT GROUP_CONCAT(DISTINCT months.month)) AS months_payed FROM students INNER JOIN student_month_payment ON students.id_students = student_month_payment.student_month_payment_id_students INNER JOIN payments ON student_month_payment.student_month_payment_bill = payments.bill INNER JOIN parents ON payments.parent = parents.id_parents INNER JOIN months ON student_month_payment.student_month_payment_id_month = months.id_month";

		if (!empty($q) || !empty($date)) {
			$sql .= " WHERE";
		}

		if (!empty($q)) {
			$sql .= " :sort LIKE :q";
			$params[':sort'] = $sort;
			$params[':q'] = "%{$q}%";
		}

		if (!empty($date)) {
			if (!empty($q)) {
				$sql .= " AND";
			}
			$sql .= " date = :date";
			$params[":date"] = $date;
		}

		$sql .= " GROUP BY payments.bill";

		try {
			$total_stmt = $this->conn->prepare($sql);
			$total_stmt->execute($params);
			$total_payments = $total_stmt->fetchAll(PDO::FETCH_ASSOC);
			$response->data = ceil(count($total_payments) / $this->per_pages);
		} catch (\Throwable $th) {
			$response->message = $th->getMessage();
			$response->data = 0;
			$response->sql = $sql;
			$response->statusCode = 500;
		}

		return $response;
	}

	function getMonths() {
		$response = new stdClass();

		try {
			$sql = "SELECT * FROM months ORDER BY id_month ASC";
			$query = $this->conn->query($sql);
			$months = $query->fetchAll(PDO::FETCH_ASSOC);
			$response->data = $months;
			$response->message = "Todos los meses";
			$response->statusCode = 201;
			return $response;
		} catch (PDOException $th) {
			$response->statusCode = 500;
			$response->message = $th->getMessage();
		}
		$result = $this->conn->query($sql);
		return $months;
	}
}
?>