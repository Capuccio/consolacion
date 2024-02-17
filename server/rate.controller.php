<?php
$data = file_get_contents("php://input");
$input = json_decode($data, true);

require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/rate.service.php';
$payment = new RateService();

switch ($input["route"]) {
	case 'get':
		$response = $payment->getRate($input["date"]);
		break;
	case 'create':
		$response = $payment->createRate($input);
		break;
	case 'update':
		$response = $payment->updateRate($input);
		break;
	default:
		# code...
		break;
}

echo json_encode($response);
?>