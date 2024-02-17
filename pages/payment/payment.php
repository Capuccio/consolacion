<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/confirmLogin.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/payment.service.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/service.user.php';

$payment = new PaymentService();
$user = new User("students");
$months = $payment->getMonths();
$students = $user->getStudentsOfParents($_SESSION["user"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Consolacion | Registrar pago</title>
	<link rel="stylesheet" href="/consolacion/global.css" />
	<link rel="stylesheet" href="/consolacion/pages/register/register.css" />
	<link rel="stylesheet" href="./payment.css" />
	<script src="/consolacion/js/moveBack.js"></script>
	<script src="/consolacion/js/form.js"></script>
	<script src="/consolacion/js/logout.js"></script>
</head>
<body>
	<div class="logout">
		<button class="button-logout" onclick="logout()">
			<img src="/consolacion/public/logout2.png" class="image-back" draggable="false" />
		</button>
	</div>
	<main class="main">
		<header class="page-header">
			<h1 class="title">Registro de pago</h1>
		</header>
		<section class="section">
			<form id="form-payment" action="#" class="form" onsubmit="formValidationParent(event)" method="POST">
				<div>
					<div class="label">
						<label>Tipo de operación</label>
					</div>
					<select name="type" class="select input w-full">
						<option value="Pago Movil">Pago móvil</option>
						<option value="Transferencia">Transferencia</option>
					</select>
				</div>
				<div>
					<div class="label">
						<label>Banco</label>
					</div>
					<input name="bank" class="input w-full" />
					<span class="msg-error">Banco del cuál se realizó el pago</span>
				</div>
				<div>
					<div class="label">
						<label>N° de movimiento</label>
					</div>
					<input name="reference_number" class="input w-full" />
					<span class="msg-error">Introducir número de referencia del pago</span>
				</div>
				<div>
					<div class="label">
						<label>Fecha de emisión</label>
					</div>
					<input name="date" type="date" class="select w-full" onchange="searchDollar(this.value)" />
					<span class="msg-error">Debe seleccionar la fecha que realizó el pago</span>
				</div>
				<div>
					<div class="label">
						<label>Tasa BCV del día</label>
					</div>
					<input name="exchange_rate" class="input w-full input-disabled" maxlength="11" disabled />
				</div>
				<div>
					<div class="label">
						<label>Meses</label>
					</div>
					<select id="monthSelect" name="months" class="select input w-full">
						<option value="0">Selecciona los meses a pagar</option>
						<?php foreach ($months->data as $month) { ?>
							<option value="<?php echo $month['id_month']; ?>, <?php echo $month['month']; ?>"><?php echo $month['month']; ?></option>
						<?php } ?>
					</select>
					<div id="selected-months" class="selected-tags"></div>
					<span class="msg-error">Debes seleccionar mínimo un mes</span>
				</div>
				<div>
					<div class="label">
						<label>Estudiantes</label>
					</div>
					<select id="studentSelect" name="students" class="select input w-full">
						<option value="0">Selecciona los estudiantes</option>
						<?php foreach ($students->data as $student) { ?>
							<option value="<?php echo $student['id_students']; ?>, <?php echo $student['student_name'] . ' ' . $student["student_last_name"]; ?>"><?php echo $student['student_name'] . ' ' . $student["student_last_name"]; ?></option>
						<?php } ?>
					</select>
					<div id="selected-students" class="selected-tags" style="grid-template-columns: repeat(3, 1fr);"></div>
					<span class="msg-error">Debes seleccionar mínimo un estudiante</span>
				</div>
				<div>
					<div class="label">
						<label>Precio Unitario</label>
					</div>
					<input name="unit_price" placeholder="500.00" class="input w-full" maxlength="11" />
					<span class="msg-error">Precio de cada mes</span>
				</div>
				<div>
					<div class="label">
						<label>Total</label>
					</div>
					<input name="total" placeholder="1000.00" class="input w-full" maxlength="11" />
					<span class="msg-error">Precio total pagado</span>
				</div>
				<div>
					<div class="label">
						<label>Observaciones</label>
					</div>
					<textarea name="observations" class="input w-full"></textarea>
				</div>
				<div class="submit-right">
					<button class="button button-primary button-width">Registrar pago</button>
				</div>
			</form>
		</section>
	</main>
	<script>
		const monthSelect = document.getElementById('monthSelect');
		const studentSelect = document.getElementById('studentSelect');
		const monthsTagContainer = document.getElementById('selected-months');
		const studentsTagContainer = document.getElementById('selected-students');
		const monthsValue = [];
		const studentsValue = [];

		function createTag(event, tagContainer, whatValue) {
			const tagText = event.target.value.replace(/^\[\'|\'\]$/g,'').split(", ");

			if (tagText[0] === "0") return;
			if (tagContainer.querySelector(`[data-value="${tagText[0]}"]`) !== null) return;
			(whatValue === "months") ? monthsValue.push(tagText[0]) : studentsValue.push(tagText[0]);

			const tagElement = document.createElement('span');
			tagElement.classList.add('tag');
			tagElement.setAttribute('data-value', tagText[0]);

			const tagTextElement = document.createElement('span');
			tagTextElement.textContent = tagText[1];
			tagElement.appendChild(tagTextElement);

			const removeButton = document.createElement('button');
			removeButton.textContent = '×';
			removeButton.addEventListener('click', () => {
				(whatValue === "months") ? monthsValue.splice(monthsValue.indexOf(tagText[0]), 1) : studentsValue.splice(studentsValue.indexOf(tagText[0]), 1);
				tagContainer.removeChild(tagElement);
			});
			tagElement.appendChild(removeButton);

			tagContainer.appendChild(tagElement);
		}

		monthSelect.addEventListener('change', (event) => {
			createTag(event, monthsTagContainer, "months");
			monthSelect.selectedIndex = 0;
		});

		studentSelect.addEventListener('change', (event) => {
			createTag(event, studentsTagContainer, "students");
			studentSelect.selectedIndex = 0;
		});

		function formValidationParent(event) {
			const form = formEvent(event);
			const errors = document.getElementsByClassName("msg-error");
			const studentContainer = document.getElementById("student-container");

			if (form.bank.trim() === "") errors[0].style.display = "block";
			if (form.reference_number.trim() === "" || isNaN(form.reference_number)) errors[1].style.display = "block";
			if (form.date === "") errors[2].style.display = "block";
			if (monthsValue.length == 0) errors[3].style.display = "block";
			if (studentsValue.length == 0) errors[4].style.display = "block";
			if (form.unit_price.trim() === "") errors[5].style.display = "block";
			if (form.total.trim() === "") return errors[6].style.display = "block";

			for (let index = 0; index < errors.length; index++) errors[index].style.display = "none";

			fetch("/consolacion/server/payment.controller.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({ ...form, months: monthsValue, students: studentsValue, route: 'create' })
			})
			.then(text => text.text())
			.then(json => {
				const data = JSON.parse(json)
				if (data.statusCode === 201) {
					alert(data.message);
					window.location.href = `/consolacion/pages/pdf/bill.php?id=${data.data}`;
				} else {
					alert("Hubo un error al intentar registrar el pago");
				}
			})
		}

		async function searchDollar(date) {
			const form = document.getElementById("form-payment");
			const response = await fetch("../../server/rate.controller.php", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ date, route: 'get' })
			});
			const json = await response.text();
			const data = JSON.parse(json);

			if (data.statusCode === 200 && data.data.hasOwnProperty('id_rate_of_day')) {
				form.exchange_rate.value = data.data.rate;
				isCreateOrEdit = true;
			} else {
				form.exchange_rate.value = '';
				isCreateOrEdit = false;
			}
		}
	</script>
</body>
</html>