<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/confirmLogin.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/payment.service.php';

$paymentService = new PaymentService();
$payment = $paymentService->getPayment($_GET["id"]);
//print_r($payment);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Consolacion | Factura</title>
	<link rel="stylesheet" href="./bill.css" />
	<link rel="stylesheet" href="./bill_pdf.css" media="print" />
</head>
<body>
	<main>
		<div class="container">
			<div>
				<h1 class="text-color">A.C. Unidad Edu Privada Nuestra Señora de la Consolación</h1>
				<p class="titles text-color">CALLE RIVAS OESTE LOCAL NRO. 43, ZONA CASCO CENTRAL MARACEDO. ARAGUA. ZONA POSTAL 2101. TELÉFONO: (0243) 245.32.97 / 245.57.97</p>
				<p class="titles text-color">PERMISO M.P.P.E. S-0166-D-0503 / WEB: www.colegioconsolacionmaracay.org.ve / E-mail: consoladmi@gmail.com / RIF: J-29446228-6</p>
			</div>
			<div class="bill-square">
				<div class="parent m-bottom">
					<div style="padding: 8px;">
						<p class="sub-titles">NOMBRE Y APELLIDO:</p>
						<span class="span-data"><?php echo $payment->data["name"] . ' ' . $payment->data["last_name"]; ?></span>
					</div>
					<div style="flex: 1; padding: 8px;">
						<p class="sub-titles">CI:</p>
						<span class="span-data"><?php echo $payment->data["id_parents"]; ?></span>
					</div>
					<div class="bill-date">
						<div class="bill-text">FACTURA <span style="color: #e01116;">1</span></div>
						<div>FECHA DE EMISIÓN</div>
						<div>13-02-2024</div>
					</div>
				</div>
				<div class="m-bottom" style="padding: 8px;">
					<p class="sub-titles">Alumnos:</p>
					<div class="students">
						<?php foreach (explode(",", $payment->data["students_names"]) as $student) { ?>
							<span class="span-data"><?php echo $student; ?></span>
						<?php } ?>
					</div>
				</div>
				<div class="parent">
					<div>
						<p class="sub-titles">Domicilio fiscal:</p>
						<span class="span-data"><?php echo $payment->data["address"]; ?></span>
					</div>
					<div>
						<p class="sub-titles">Periodo:</p>
						<span class="span-data"><?php echo $payment->data["year"]; ?></span>
					</div>
					<div>
						<p class="sub-titles">Tlf</p>
						<span class="span-data"><?php echo $payment->data["phone"]; ?></span>
					</div>
				</div>
				<div>
					<table class="w-full" style="text-align: center;">
						<thead>
							<tr style="background-color: #2f5a8d;">
								<th>Descripcion</th>
								<th>Precio Unitario</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach (explode(",", $payment->data["months_payed"]) as $month) { ?>
							<tr>
								<td style="text-transform: uppercase;">MES DE <?php echo $month; ?></td>
								<td><?php echo $payment->data["unit_price"]; ?></td> 
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="bill-footer">
				<div class="method-pay">
					<div class="method-pay__title">
						<span>FORMA DE PAGO: </span>
					</div>
					<div class="method-pay__body">
						<?php echo $payment->data["type"]; ?>
					</div>
				</div>
				<div class="method-pay f-1">
					<div class="observations-title">
						<p class="text-color p-8">OBSERVACIONES: </p>
					</div>
					<div class="observations-body">
						<?php echo $payment->data["observations"]; ?>
					</div>
				</div>
				<div class="total">
					<div class="total-title">
						<p>TOTAL: </p>
					</div>
					<div class="">
						<?php echo $payment->data["total"]; ?>
					</div>
				</div>
			</div>
		</div>
	</main>
	<script>
		window.onload = function() {
			window.print();
		}

		window.onafterprint = function() {
			return document.referrer ? window.history.back() : window.location.href = 'home.html';
		}
	</script>
</body>
</html>