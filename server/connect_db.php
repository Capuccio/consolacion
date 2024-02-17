<?php
class ConnectDB {
  private static $instance;
  private $conn;

  private $host = "localhost";
  private $db_name = "consolacion";
  private $username = "root";
  private $password = "";

  private function __construct() {
    try {
      $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (\Throwable $th) {
      echo "Error: " . $th->getMessage();
      die();
    }

  }
  protected function __clone() {}

  public function __wakeup() {
    throw new \Exception("Cannot unserialize a singleton.");
  }

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new ConnectDB();
    }
    return self::$instance;
  }

  public function getConnection() {
    return $this->conn;
  }
}
?>