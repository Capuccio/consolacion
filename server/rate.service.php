<?php
include_once("connect_db.php");

class RateService {
	protected $conn;
	protected $per_pages= 5;

	function __construct() {
		$db = ConnectDB::getInstance();
		$this->conn = $db->getConnection();
	}

	function __destruct() {
		$this->conn = null;
	}

	function getRate($date) {
		$response = new stdClass();

		try {
			$sql = "SELECT * FROM rate_of_day WHERE date = :date";
			$query = $this->conn->prepare($sql);
			$query->execute(array(':date' => $date));
			$rate = $query->fetch(PDO::FETCH_ASSOC);
			$response->data = $rate;
			$response->message = "Precio dólar";
			$response->statusCode = 200;
			return $response;
		} catch (PDOException $th) {
			$response->statusCode = 500;
			$response->message = $th->getMessage();
		}
		return $response;
	}

	function createRate($data) {
		$response = new stdClass();

		try {
			$sql = "INSERT INTO rate_of_day (rate, date) VALUES (:rate, :date)";
			$query = $this->conn->prepare($sql);
			$query->execute(array(':rate' => $data['rate'], ':date' => $data['date']));
			$response->message = "Se registró el precio del dolar";
			$response->statusCode = 201;
			return $response;
		} catch (PDOException $th) {
			$response->statusCode = 500;
			$response->message = $th->getMessage();
		}
		return $response;
	}

	function updateRate($data) {
		$response = new stdClass();

		try {
			$sql = "UPDATE rate_of_day SET rate = :rate WHERE date = :date";
			$query = $this->conn->prepare($sql);
			$query->execute(array(':rate' => $data['rate'], ':date' => $data['date']));
			$response->message = "Se actualizó el precio del dolar";
			$response->statusCode = 201;
			return $response;
		} catch (PDOException $th) {
			$response->statusCode = 500;
			$response->message = $th->getMessage();
		}
		return $response;
	}
}
?>