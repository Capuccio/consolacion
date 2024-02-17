<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/confirmLogin.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Consolacion | Registro representante</title>
	<link rel="stylesheet" href="../../global.css" />
	<link rel="stylesheet" href="./register.css" />
	<script src="../../js/moveBack.js"></script>
	<script src="../../js/form.js"></script>
</head>
<body>
	<main class="main">
		<header class="page-header">
			<button class="button-back" onclick="moveBack()">
				<img src="../../public/back.png" class="image-back" draggable="false" />
			</button>
			<h1 class="title">Registro de representante</h1>
		</header>
		<section class="section">
			<div class="form__title">
				<h3>Representante</h3>
			</div>
			<form id="form-parent" action="#" class="form" onsubmit="formValidationParent(event)" method="POST">
				<div>
					<div class="label">
						<label>Cédula de identidad</label>
					</div>
					<input name="ci" placeholder="01234567" class="input w-full" maxlength="8" onchange="searchParent(this.value)" />
					<span class="msg-error">Debe contener ocho dígitos y solamente números</span>
				</div>
				<div>
					<div class="label">
						<label>Domicilio fiscal</label>
					</div>
					<input name="address" class="input w-full" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Nombres</label>
					</div>
					<input name="name" class="input w-full" />
						<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Apellidos</label>
					</div>
					<input name="last_name" class="input w-full" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div>
					<div class="label">
						<label>Teléfono</label>
					</div>
					<input name="phone" placeholder="04121234567" class="input w-full" maxlength="11" />
					<span class="msg-error">Debe contener once dígitos y solamente números</span>
				</div>
				<div class="submit-right">
					<button class="button button-primary button-width">Registrar</button>
				</div>
			</form>
		</section>
		<div id="student-container" class="student">
			<div class="add-student">
				<button id="openModal" class="button button-success">Registrar estudiante</button>
			</div>
			<div class="student-list">
			</div>
		</div>
		<div id="modal" class="modal">
			<section class="section modal-content">
				<div class="form__title modal-title">
					<h3>Estudiante</h3>
					<span id="closeModal" class="close-modal">&times;</span>
				</div>
				<form action="#" class="form" onsubmit="formValidationStudent(event)" method="POST" class="form">
					<div>
						<div class="label">
							<label>Cédula de identidad</label>
						</div>
						<input name="ci" placeholder="01234567" class="input w-full" maxlength="8" />
					</div>
					<div>
						<div class="label">
							<label>Nombres</label>
						</div>
						<input name="student_name" class="input w-full" />
					</div>
					<div>
						<div class="label">
							<label>Apellidos</label>
						</div>
						<input name="student_last_name" class="input w-full" />
					</div>
					<div>
						<div class="label">
							<label>Año escolar</label>
						</div>
						<input name="school_year" class="input w-full input-disabled" maxlength="11" value="2023-2024" />
					</div>
					<div>
						<div class="label">
							<label>Año y mención</label>
						</div>
						<input name="year_and_mention" class="input w-full" maxlength="11" />
					</div>
					<div class="submit-right">
						<button class="button button-primary button-width">Registrar</button>
					</div>
				</form>
			</section>
		</div>
	</main>
	<script>
		async function searchParent(id) {
			if (id.length < 8 || isNaN(id)) return;
			const studentContainer = document.getElementById("student-container");
			const response = await fetch("../../server/controller.user.php", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ id, table: "parents", route: "getUser" })
			});
			const json = await response.text();
			const data = JSON.parse(json);
			if (data.statusCode === 200) {
				const form = document.getElementById("form-parent");
				form.address.value = data.data.address;
				form.name.value = data.data.name;
				form.last_name.value = data.data.last_name;
				form.phone.value = data.data.phone;
				studentContainer.style.display = "block";
			} else {
				studentContainer.style.display = "none";
			}
		}

		async function register(form) {
			const response = await fetch("../../server/controller.user.php", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(form)
			});
			const json = await response.text();
			const data = JSON.parse(json);
			return data;
		}

		function formValidationParent(event) {
			const form = formEvent(event);
			const errors = document.getElementsByClassName("msg-error");
			const studentContainer = document.getElementById("student-container");

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

			register({...form, table: 'parents', route: 'register'})
				.then(response => {
					if (response.statusCode === 201) {
						sessionStorage.setItem("ci_parent", form.ci);
						alert("La clave del representante es: " + response.password);
						studentContainer.style.display = "block";
					} else if (response.statusCode === 500 && response.message.includes("Duplicate")) {
						alert("La cédula ingresada ya está registrada")
					}
				})
		}

		function formValidationStudent(event) {
			const form = formEvent(event);
			const errors = document.getElementsByClassName("msg-error");
			const studentList = document.getElementsByClassName("student-list");
			const modal = document.getElementById("modal");

			if (form.ci.length < 8 || isNaN(form.ci)) return errors[0].style.display = "block";

			register({ ...form, table: 'students', ci_parent: sessionStorage.getItem("ci_parent"), route: 'register' })
				.then(response => {
					if (response.statusCode === 201) {
						const studentDetail = document.createElement("details");
						const studentSummary = document.createElement("summary");
						const studentInfo = document.createElement("span");

						studentDetail.className = "student-detail";
						studentSummary.className = "student-summary";
						studentInfo.className = "student-info";

						studentSummary.textContent = `${form.student_name} ${form.student_last_name}`;
						studentInfo.textContent = `${form.id_students} | ${form.school_year} | ${form.student_name} ${form.student_last_name} | ${form.year_and_mention}`;
						studentDetail.appendChild(studentSummary);
						studentDetail.appendChild(studentInfo);
						studentList[0].appendChild(studentDetail);
						modal.style.display = "none";
					} else if (response.statusCode === 500 && response.message.includes("Duplicate")) {
						alert("La cédula ingresada ya está registrada")
					}
				})
		}
	</script>
	<script src="../../js/modal.js"></script>
</body>
</html>