<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/confirmLogin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Principal</title>
	<link rel="stylesheet" href="./index.css" />
	<link rel="stylesheet" href="./global.css" />
	<script src="js/logout.js"></script>
</head>
<body>
	<div class="logout">
		<button class="button-logout" onclick="logout()">
			<img src="/consolacion/public/logout2.png" class="image-back" draggable="false" />
		</button>
	</div>
	<main class="container">
		<section class="nav">
			<a class="nav__option" href="./pages/register/register.php" draggable="false">
				<img src="./public/register.png" height="64" width="64" alt="records" draggable="false" />
				<div class="info">
					<h2 class="info__title">Registrar</h2>
					<span class="info__description">Registrar representantes y alumnos</span>
				</div>
			</a>
			<a class="nav__option" href="./pages/records/records-menu.php" draggable="false">
				<img src="./public/records.png" height="64" width="64" alt="search" draggable="false" />
				<div class="info">
					<h2 class="info__title">Registros</h2>
					<span class="info__description">Consultar información</span>
				</div>
			</a>
			<a class="nav__option" href="./pages/bcv/rate.php" draggable="false">
				<img src="./public/records.png" height="64" width="64" alt="search" draggable="false" />
				<div class="info">
					<h2 class="info__title">BCV</h2>
					<span class="info__description">Registrar o editar el precio del dólar</span>
				</div>
			</a>
		</section>
	</main>
</body>
</html>