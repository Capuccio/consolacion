<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/confirmLogin.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/payment.service.php';

$page = (isset($_GET["page"])) ? $_GET["page"] : 1;
$sort= (isset($_GET["sort"])) ? $_GET["sort"] : "";
$q = (isset($_GET["q"])) ? $_GET["q"] : "";
$date = (isset($_GET["date"])) ? $_GET["date"] : "";

$payment = new PaymentService();
$allPayments = $payment->searchPayments($sort, $q, $date, $page);
$total_pages = $payment->countSearchPayments($sort, $q, $date);
//print_r($allPayments);
//print_r($total_pages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Consolacion | Registros de Pagos</title>
	<link rel="stylesheet" href="../../../global.css" />
	<link rel="stylesheet" href="../styles.css" />
	<link rel="stylesheet" href="./records-bill.css" />
	<script src="../../../js/moveBack.js"></script>
</head>
<body>
	<main class="main">
		<header class="page-header">
			<button class="button-back" onclick="moveBack('../records-menu.php')">
				<img src="../../../public/back.png" class="image-back" draggable="false" />
			</button>
			<h1 class="title">Pagos</h1>
		</header>
		<section class="section section-table">
			<div class="search-container">
				<form>
					<input name="date" type="date" class="select" />
					<select name="sort" class="select">
						<option value="bill">factura</option>
						<option value="name">Nombre</option>
						<option value="last_name">Apellido</option>
						<option value="type">Tipo</option>
						<option value="reference_number">Número de referencia</option>
						<option value="bank">Banco</option>
						<option value="unit_price">Precio unitario</option>
						<option value="total">Total</option>
					</select>
					<input name="q" type="text" class="input w-full" placeholder="Buscar" />
					<button class="button button-primary">Buscar</button>
				</form>
			</div>
			<table>
				<thead>
					<tr>
						<th>N° factura</th>
						<th>Representante</th>
						<th>Tipo</th>
						<th>Banco</th>
						<th>Referencia</th>
						<th>Precio unitario</th>
						<th>Total</th>
						<th>Hijos</th>
						<th>Meses</th>
						<th>Observaciones</th>
						<th>Fecha</th>
						<th>PDF</th>
						<th>Eliminar</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($allPayments->data as $payment) { ?>
						<tr>
							<td><?php print_r($payment["bill"]); ?></td>
							<td><?php echo $payment["name"] . ' ' . $payment["last_name"]; ?></td>
							<td><?php print_r($payment["type"]); ?></td>
							<td><?php print_r($payment["bank"]); ?></td>
							<td><?php print_r($payment["reference_number"]); ?></td>
							<td><?php print_r($payment["unit_price"]); ?></td>
							<td><?php print_r($payment["total"]); ?></td>
							<td>
								<?php
									$students = explode(",", $payment["students_names"]);
									if (count($students) > 1) {
								?>
									<details>
										<summary>Hijos</summary>
										<?php for ($i=0; $i < count($students); $i++) { ?>
											<p><?php echo $students[$i]; ?></p>
										<?php } ?>
									</details>
								<?php
									} else {
								?>
									<p><?php echo $students[0]; ?></p>
								<?php } ?>
							</td>
							<td>
								<?php
									$months = explode(",", $payment["months_payed"]);
									if (count($months) > 1) {
								?>
									<details>
										<summary>Meses</summary>
										<?php for ($i=0; $i < count($months); $i++) { ?>
											<p><?php echo $months[$i]; ?></p>
										<?php } ?>
									</details>
								<?php
									} else {
								?>
									<p><?php echo $months[0]; ?></p>
								<?php } ?>
							</td>
							<td><?php print_r($payment["observations"]); ?></td>
							<td><?php print_r($payment["date"]); ?></td>
							<td>
								<a href="/consolacion/pages/pdf/bill.php?id=<?php print_r($payment["bill"]); ?>">
									<button class="button button-primary button-icon">
										<img src="/consolacion/public/pdf.png" class="w-full" />
									</button>
								</a>
							</td>
							<td>
								<button onclick="openModal(<?php echo $payment['bill'] ?>)" class="button button-danger button-icon">
									<img src="/consolacion/public/expediente.png" class="w-full" />
								</button>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</section>
		<div class="pagination">
			<?php if ($page - 1 != 0) { ?>
			<a href="?page=<?php echo $page - 1 . (!empty($_GET["q"]) ? "&sort={$_GET['sort']}&q={$_GET['q']}" : ""); ?>">
				<button class="button button-pagination">
					<?php echo $page - 1; ?>
				</button>
			</a>
			<?php } ?>
			<a href="?page=<?php echo $page . (!empty($_GET["q"]) ? "&sort={$_GET['sort']}&q={$_GET['q']}" : ""); ?>">
				<button class="button button-pagination button-pagination--active">
					<?php echo $page; ?>
				</button>
			</a>
			<?php if ($total_pages->data > $page) { ?>
			<a href="?page=<?php echo $page + 1 . (!empty($_GET["q"]) ? "&sort={$_GET['sort']}&q={$_GET['q']}" : ""); ?>">
				<button class="button button-pagination">
					<?php echo $page + 1; ?>
				</button>
			</a>
			<?php } ?>
		</div>
		<div id="modal" class="modal">
			<section class="modal-content section modal-section">
				<div class="form__title modal-title">
					<h3>Eliminar factura</h3>
					<span id="closeModal" class="close-modal">&times;</span>
				</div>
				<div id="modal-delete">
					¿Seguro que desea eliminar este registro?
				</div>
				<div class="modal-buttons">
					<button onclick="modalDelete()" class="button button-danger w-full">Eliminar</button>
				</div>
			</section>
		</div>
	</main>
	<script>
		const modal = document.getElementById("modal");
		const openBtn = document.getElementById("openModal");
		const closeBtn = document.getElementById("closeModal");

		function openModal(id) {
			const div = document.getElementById("modal-delete");
			div.innerHTML = `¿Seguro que desea eliminar la factura nro <b>${id}</b>?`;
			sessionStorage.setItem("id", id);
			modal.style.display = "block";
		}

		function modalDelete() {
			const id = sessionStorage.getItem("id");

			fetch("/consolacion/server/payment.controller.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({ id, route: 'delete' })
			})
			.then(text => text.text())
			.then(json => {
				const data = JSON.parse(json)
				console.log(data);
				if (data.statusCode === 201) {
					alert(data.message);
					window.location.reload();
				} else {
					alert("Hubo un error al intentar registrar el pago");
				}
			})
		}

		closeBtn.onclick = function() {
			modal.style.display = "none";
		}

		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			} 
		}
	</script>
</body>
</html>