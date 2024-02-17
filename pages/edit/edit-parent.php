<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/service.user.php';
$userInstance = new User("parents");
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
	<link rel="stylesheet" href="./edit-parent.css" />
	<script src="../../js/moveBack.js"></script>
	<script src="../../js/form.js"></script>
</head>
<body>
	<main class="main">
		<header class="page-header">
			<button class="button-back" onclick="moveBack('/consolacion/pages/records/parents/records-parent.php')">
				<img src="/consolacion/public/back.png" class="image-back" draggable="false" />
			</button>
			<h1 class="title">Editar representante</h1>
		</header>
		<section class="section">
			<div class="form__title">
				<h3>Representante</h3>
			</div>
			<form action="" class="form" onsubmit="formValidationParent(event)" method="POST">
				<div>
					<div class="label">
						<label>Cédula de identidad</label>
					</div>
					<input name="ci" placeholder="01234567" class="input w-full input-disabled" maxlength="8" value="<?php echo $user->data["id_parents"] ?>" />
					<span class="msg-error">Debe contener ocho dígitos y solamente números</span>
				</div>
				<div>
					<div class="label">
						<label>Domicilio fiscal</label>
					</div>
					<input name="address" class="input w-full" value="<?php echo $user->data["address"] ?>" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Nombres</label>
					</div>
					<input name="name" class="input w-full" value="<?php echo $user->data["name"] ?>" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Apellidos</label>
					</div>
					<input name="last_name" class="input w-full" value="<?php echo $user->data["last_name"] ?>" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Teléfono</label>
					</div>
					<input name="phone" placeholder="04121234567" class="input w-full" maxlength="11" value="<?php echo $user->data["phone"] ?>" />
					<span class="msg-error">Debe contener once dígitos y solamente números</span>
				</div>
				<div class="footer-buttons">
					<button type="button" class="button button-primary button-width" onclick="resetPassword(<?php echo $user->data['id_parents'] ?>)">Reset Clave</button>
					<button type="submit" class="button button-primary button-width">Editar</button>
				</div>
			</form>
		</section>
	</main>
	<script>
		async function formValidationParent(event) {
			const form = formEvent(event);
			const errors = document.getElementsByClassName("msg-error");

			if (form.ci.length < 8 || isNaN(form.ci)) errors[0].style.display = "block";
			if (form.phone.length < 8 || isNaN(form.phone)) errors[1].style.display = "block";
			if (form.address.length === 0) errors[2].style.display = "block";
			if (form.name.length === 0) errors[3].style.display = "block";
			if (form.last_name.length === 0) return errors[4].style.display = "block";

			errors[0].style.display = "none";
			errors[1].style.display = "none";
			errors[2].style.display = "none";
			errors[3].style.display = "none";
			errors[4].style.display = "none";

			const response = await fetch("/consolacion/server/controller.user.php", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ ...form, table: 'parents', route: 'update' })
			});
			const json = await response.text();
			const data = JSON.parse(json);

			if (data.statusCode === 204) {
				alert("El representante ha sido actualizado");
				moveBack('/consolacion/pages/records/parents/records-parent.php');
			} else if (response.statusCode === 500 && response.message.includes("Duplicate")) {
				alert("La cédula ingresada ya existe");
			}
		}

		async function resetPassword(id) {
			const response = await fetch("/consolacion/server/controller.user.php", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ id, table: 'parents', route: 'resetPass' })
			});
			const json = await response.text();
			const data = JSON.parse(json);

			if (data.statusCode === 200) {
				alert(data.message);
			} else {
				alert("Hubo un error al intentar resetear la contraseña");
			}
	}
	</script>
</body>
</html>