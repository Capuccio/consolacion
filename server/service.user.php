<?php
include_once("connect_db.php");

class User {
	protected $table;
	protected $conn;
	public $per_view = 5;

	public function __construct($table) {
		$this->table = $table;
		$db = ConnectDB::getInstance();
		$this->conn = $db->getConnection();
	}

	public function __destruct() {
		$this->conn = null;
	}

	public function loginUser($ci, $password) {
		$response = new stdClass();

		if ($ci == "admin" && $password == "4dm1n#G3n3r4l") {
			$response->data = array("id_parents" => "admin");
			$response->message = "Usuario administrador encontrado";
			$response->statusCode = 200;
			return $response;
		}

		try {
			$sql = "SELECT * FROM {$this->table} WHERE id_parents = ? AND password = ?";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($ci, $password));
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if (empty($user)) {
				throw new Exception("Usuario no encontrado");
			} else {
				$response->data = $user;
				$response->message = "Usuario encontrado";
				$response->statusCode = 200;
			}
		} catch (\Throwable $th) {
			$response->message = $th->getMessage();
			$response->statusCode = 404;
		}
		return $response;
	}

	public function getAllUsers($page) {
		$response = new stdClass();
		$response->total_users = $this->conn->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
		$response->total_pages = ceil($response->total_users / $this->per_view);
		$response->data = $this->conn->query("SELECT * FROM {$this->table} LIMIT {$this->per_view} OFFSET " . ($page - 1) * $this->per_view)->fetchAll(PDO::FETCH_ASSOC);
		return $response;
	}

	public function getUsersByField($field, $value, $page) {
		$response = new stdClass();

		try {
			$users_filtered = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$field} LIKE :value");
			$users_filtered->execute([':value' => "%$value%"]);
			$response->total_users = $users_filtered->fetchColumn();
			$response->total_pages = ceil($response->total_users / $this->per_view);
			$stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE {$field} LIKE ? LIMIT {$this->per_view} OFFSET " . ($page - 1) * $this->per_view);
			$stmt->execute(array("%$value%"));
			$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (empty($users)) {
				throw new Exception("");
			} else {
				$response->data = $users;
				$response->message = "Usuarios encontrados";
				$response->statusCode = 200;
			}
		} catch (\Throwable $th) {
			$response->data = [];
			$response->total_pages = 0;
			$response->message = $th->getMessage();
			$response->statusCode = 404;
		}

		return $response;
	}

	public function getUser($ci) {
		$response = new stdClass();
		try {
			$sql = "SELECT * FROM {$this->table} WHERE";
			$sql .= $this->table == "parents" ? " id_parents = ?" : " id_students = ?";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($ci));
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if (empty($user)) {
				throw new Exception("Usuario no encontrado");
			} else {
				$response->data = $user;
				$response->message = "Usuario encontrado";
				$response->statusCode = 200;
			}
		} catch (\Throwable $th) {
			$response->message = $th->getMessage();
			$response->statusCode = 404;
		}
		return $response;
	}

	public function registerUser($user_data, $password) {
		$response = new stdClass();
		if ($this->table == "parents") {
			$sql = "INSERT INTO {$this->table} (id_parents, name, last_name, phone, address, password) VALUES (?, ?, ?, ?, ?, ?)";
			$data = array($user_data["ci"], $user_data["name"], $user_data["last_name"], $user_data["phone"], $user_data["address"], $password);
		} else {
			$sql = "INSERT INTO {$this->table} (id_students, parent, student_name, student_last_name, year, year_and_mention) VALUES (?, ?, ?, ?, ?, ?)";
			$data = array($user_data["ci"], $user_data["ci_parent"], $user_data["student_name"], $user_data["student_last_name"], $user_data["school_year"], $user_data["year_and_mention"]);
		}

		try {
			$this->conn->prepare($sql)->execute($data);
			$response->message = "Usuario registrado correctamente";
			$response->statusCode = 201;
			return $response;
		} catch (PDOException $th) {
			$response->statusCode = 500;
			$response->message = $th->getMessage();
		}

		return $response;
	}

	public function updateUser($user_data) {
		$response = new stdClass();
		if ($this->table == "parents") {
			$sql = "UPDATE {$this->table} SET name = ?, last_name = ?, phone = ?, address = ? WHERE id_parents = ?";
			$data = array($user_data["name"], $user_data["last_name"], $user_data["phone"], $user_data["address"], $user_data["ci"]);
		} else {
			$sql = "UPDATE {$this->table} SET student_name = ?, student_last_name = ?, year = ?, year_and_mention = ? WHERE id_students = ?";
			$data = array($user_data["student_name"], $user_data["student_last_name"], $user_data["year"], $user_data["year_and_mention"], $user_data["ci"]);
		}

		try {
			$this->conn->prepare($sql)->execute($data);
			$response->message = "Usuario actualizado correctamente";
			$response->statusCode = 204;
			return $response;
		} catch (PDOException $th) {
			$response->statusCode = 500;
			$response->message = $th->getMessage();
		}

		return $response;
	}

	public function resetPass($id) {
		$response = new stdClass();
		$sql_select = "SELECT * FROM parents WHERE id_parents = :id";
		$sql_update = "UPDATE {$this->table} SET password = :password WHERE id_parents = :id";
		$password = '';

		try {
			$stmt = $this->conn->prepare($sql_select);
			$stmt->execute([':id' => $id]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			$values = $user["id_parents"] . $user["name"] . $user["last_name"];
			for ($i=0; $i < 10; $i++) {
				$index = rand(0, strlen($values) - 1);
				$password .= $values[$index];
			}

			$this->conn->prepare($sql_update)->execute(array(':password' => $password, ':id' => $id));
			$response->message = "Contraseña reseteada correctamente: " . $password;
			$response->statusCode = 200;
			$response->password = $password;
			return $response;
		} catch (PDOException $th) {
			$response->statusCode = 500;
			$response->message = $th->getMessage();
		}

		return $response;
	}

	public function getStudentsOfParents($parent) {
		$response = new stdClass();
		try {
			$sql = "SELECT * FROM students WHERE parent = ?";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute(array($parent));
			$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (empty($students)) {
				throw new Exception("No hay alumnos registrados");
			} else {
				$response->data = $students;
				$response->message = "Alumnos encontrados";
				$response->statusCode = 200;
			}
		} catch (\Throwable $th) {
			$response->message = $th->getMessage();
			$response->statusCode = 404;
		}
		return $response;
	}
}
?>