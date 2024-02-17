<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="/consolacion/index.css" />
	<link rel="stylesheet" href="/consolacion/global.css" />
	<link rel="stylesheet" href="./rate.css" />
	<script src="/consolacion/js/moveBack.js"></script>
	<script src="/consolacion/js/form.js"></script>
</head>
<body>
	<main class="main">
		<header class="page-header">
			<button class="button-back" onclick="moveBack()">
				<img src="../../public/back.png" class="image-back" draggable="false" />
			</button>
			<h1 class="title">Registro BCV</h1>
		</header>
		<section class="section">
			<form id="form-bcv" action="" class="form" onsubmit="formValidation(event)" method="POST">
				<div>
					<div class="label">
						<label></label>
					</div>
					<input name="date" type="date" class="select w-full" onchange="searchDollar(this.value)" />
					<span class="msg-error">Debes seleccionar una fecha</span>
				</div>
				<div>
					<div class="label">
						<label>Dólar</label>
					</div>
					<input name="rate" class="input w-full" />
					<span class="msg-error">Este campo no puede estar vacío</span>
				</div>
				<div class="submit-right">
					<button type="submit" class="button button-primary button-width w-full">Registrar</button>
				</div>
			</form>
		</section>
	</main>
	<script>
		let isCreateOrEdit = false;
		async function searchDollar(date) {
			const form = document.getElementById("form-bcv");
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
				form.rate.value = data.data.rate;
				isCreateOrEdit = true;
			} else {
				form.rate.value = '';
				isCreateOrEdit = false;
			}
		}

		async function formValidation(event) {
			const form = formEvent(event);
			const errors = document.getElementsByClassName("msg-error");
			const studentContainer = document.getElementById("student-container");

			if (form.date.length === 0) errors[0].style.display = "block";
			if (form.rate.length === 0) return errors[1].style.display = "block";

			errors[0].style.display = "none";
			errors[1].style.display = "none";

			const response = await fetch("../../server/rate.controller.php", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ ...form, route: isCreateOrEdit ? 'update' : 'create' })
			});

			const json = await response.text();
			const data = JSON.parse(json);

			if (data.statusCode === 201) {
				alert(data.message);
				isCreateOrEdit = true;
			} else {
				alert("Hubo un error al intentar registrar el precio del dólar");
			}
		}
	</script>
</body>
</html>