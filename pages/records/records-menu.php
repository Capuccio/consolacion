<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/confirmLogin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Consolacion | Registros</title>
	<link rel="stylesheet" href="../../index.css" />
	<link rel="stylesheet" href="../../global.css" />
	<script src="../../js/moveBack.js"></script>
	<script src="/consolacion/js/logout.js"></script>
</head>
<body>
	<div class="logout">
		<button class="button-logout" onclick="logout()">
			<img src="/consolacion/public/logout2.png" class="image-back" draggable="false" />
		</button>
	</div>
	<main class="container">
		<header class="page-header">
			<button class="button-back" onclick="moveBack('../../index.php')">
				<img src="../../public/back.png" class="image-back" draggable="false" />
			</button>
			<h1 class="title">Registros</h1>
		</header>
		<section class="nav">
			<a class="nav__option" href="./bill/records-bill.php" draggable="false">
				<img src="../../public/bill.png" height="64" width="64" alt="records" draggable="false" />
				<div class="info">
					<h2 class="info__title">Pagos</h2>
					<span class="info__description">Pagos realizados a la institución</span>
				</div>
			</a>
			<a class="nav__option" href="./parents/records-parent.php" draggable="false">
				<img src="../../public/users.png" height="64" width="64" alt="search" draggable="false" />
				<div class="info">
					<h2 class="info__title">Representantes</h2>
					<span class="info__description">Lista de representantes registrados</span>
				</div>
			</a>
			<a class="nav__option" href="./students/records-student.php" draggable="false">
				<img src="../../public/users2.png" height="64" width="64" alt="search" draggable="false" />
				<div class="info">
					<h2 class="info__title">Estudiantes</h2>
					<span class="info__description">Lista de estudiantes registrados</span>
				</div>
			</a>
		</section>
	</main>
</body>
</html>