<?php
	include_once("service.user.php");

	$data = file_get_contents("php://input");
	$user_data = json_decode($data, true);
	$user = new User($user_data["table"]);

	switch ($user_data["route"]) {
		case 'login':
			$response = $user->loginUser($user_data["user"], $user_data["password"]);
			if ($response->statusCode == 200) {
				session_start();
				$_SESSION["user"] = $response->data["id_parents"];
			}
			break;
		case 'register':
			if ($user_data["table"] == "parents") {
				$values = $user_data["ci"] . $user_data["name"] . $user_data["last_name"];
				$password = '';
				for ($i=0; $i < 10; $i++) {
					$index = rand(0, strlen($values) - 1);
					$password .= $values[$index];
				}
				$response = $user->registerUser($user_data, $password);
				$response->password = $password;
			} else {
				$response = $user->registerUser($user_data, "");
			}
			break;
		case 'getUser':
			$response = $user->getUser($user_data["id"]);
			break;
		case 'update':
			$response = $user->updateUser($user_data);
			break;
		case 'logout':
			session_start();
			$response = new stdClass();
			$response->statusCode = 200;
			$response->message = "Sesión cerrada";
			session_unset();
			session_destroy();
			break;
		case 'resetPass':
			$response = $user->resetPass($user_data["id"]);
			break;
		default:
			# code...
			break;
	}

	echo json_encode($response);
?>