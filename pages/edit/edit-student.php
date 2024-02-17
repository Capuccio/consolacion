<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/service.user.php';
$userInstance = new User("students");
$user = $userInstance->getUser($_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Editar usuario</title>
	<link rel="stylesheet" href="../../global.css" />
	<link rel="stylesheet" href="/consolacion/pages/register/register.css" />
	<script src="../../js/moveBack.js"></script>
	<script src="../../js/form.js"></script>
</head>
<body>
	<main class="main">
		<header class="page-header">
			<button class="button-back" onclick="moveBack('/consolacion/pages/records/students/records-student.php')">
				<img src="/consolacion/public/back.png" class="image-back" draggable="false" />
			</button>
			<h1 class="title">Editar estudiante</h1>
		</header>
		<section class="section">
			<div class="form__title">
				<h3>Representante</h3>
			</div>
			<form action="" class="form" onsubmit="formValidationStudent(event)" method="POST">
				<div>
					<div class="label">
						<label>Cédula de identidad</label>
					</div>
					<input name="ci" placeholder="01234567" class="input w-full input-disabled" maxlength="8" value="<?php echo $user->data["id_students"] ?>" />
					<span class="msg-error">Debe contener ocho dígitos y solamente números</span>
				</div>
				<div>
					<div class="label">
						<label>Nombres</label>
					</div>
					<input name="student_name" class="input w-full" value="<?php echo $user->data["student_name"] ?>" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Apellidos</label>
					</div>
					<input name="student_last_name" class="input w-full" value="<?php echo $user->data["student_last_name"] ?>" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Año</label>
					</div>
					<input name="year" class="input w-full" value="<?php echo $user->data["year"] ?>" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Año y mención</label>
					</div>
					<input name="year_and_mention" placeholder="04121234567" class="input w-full" maxlength="11" value="<?php echo $user->data["year_and_mention"] ?>" />
					<span class="msg-error">Debe contener once dígitos y solamente números</span>
				</div>
				<div class="submit-right">
					<button class="button button-primary button-width">Editar</button>
				</div>
			</form>
		</section>
	</main>
	<script>
		async function formValidationStudent(event) {
			const form = formEvent(event);
			const errors = document.getElementsByClassName("msg-error");
			const studentList = document.getElementsByClassName("student-list");
			const modal = document.getElementById("modal");

			if (form.ci.length < 8 || isNaN(form.ci)) errors[0].style.display = "block";
			if (form.student_name.length === 0) errors[1].style.display = "block";
			if (form.student_last_name.length === 0) errors[2].style.display = "block";
			if (form.year.length < 8) errors[3].style.display = "block";
			if (form.year_and_mention.length === 0) return errors[4].style.display = "block";

			for (let i = 0; i < errors.length; i++) errors[i].style.display = "none";

			const response = await fetch("/consolacion/server/controller.user.php", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ ...form, table: 'students', route: 'update' })
			});
			const json = await response.text();
			const data = JSON.parse(json);

			if (data.statusCode === 204) {
				alert("El estudiante ha sido actualizado");
				moveBack('/consolacion/pages/records/students/records-student.php');
			} else if (response.statusCode === 500 && response.message.includes("Duplicate")) {
				alert("La cédula ingresada ya existe");
			}
			
		}
	</script>
</body>
</html>