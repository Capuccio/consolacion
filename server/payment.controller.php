<?php
$data = file_get_contents("php://input");
$input = json_decode($data, true);

require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/payment.service.php';
$payment = new PaymentService();

switch ($input["route"]) {
	case 'create':
		session_start();
		$response = $payment->createPayment($input, $_SESSION["user"]);
		break;
	case 'delete':
		$response = $payment->deletePayment($input);
		break;
	default:
		# code...
		break;
}

echo json_encode($response);
?>