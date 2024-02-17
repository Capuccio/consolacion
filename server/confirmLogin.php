<?php
session_start();
if (!isset($_SESSION['user'])) {
	header('Location: /consolacion/login.html');
	exit();
}

if ($_SERVER['REQUEST_URI'] == '/consolacion/pages/payment/payment.php' && $_SESSION['user'] == 'admin') {
	header('Location: /consolacion/index.php');
	exit();
} else if ($_SERVER['REQUEST_URI'] != '/consolacion/pages/payment/payment.php' && $_SESSION['user'] != 'admin') {
	header('Location: http://localhost/consolacion/pages/payment/payment.php');
	exit();
}
?>