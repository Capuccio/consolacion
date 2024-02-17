<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/consolacion/server/confirmLogin.php';
$page = (isset($_GET["page"])) ? $_GET["page"] : 1;

require_once("../../../server/service.user.php");
$user = new User("parents");

if (isset($_GET['q']) && !empty($_GET["q"]) ) {
	$usersList = $user->getUsersByField($_GET["sort"], $_GET["q"], $page);
} else {
	$usersList = $user->getAllUsers($page);
}
//print_r($usersList);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Consolacion | Registro representantes</title>
	<link rel="stylesheet" href="../../../global.css" />
	<link rel="stylesheet" href="../styles.css" />
	<script src="../../../js/moveBack.js"></script>
</head>
<body>
	<main class="main">
		<header class="page-header">
			<button class="button-back" onclick="moveBack('../records-menu.php')">
				<img src="../../../public/back.png" class="image-back" draggable="false" />
			</button>
			<h1 class="title">Representantes</h1>
		</header>
		<section class="section">
			<div class="search-container">
				<form action="" class="form" method="GET">
					<select name="sort" class="select">
						<option value="id_parents">cédula</option>
						<option value="name">nombres</option>
						<option value="last_name">apellidos</option>
						<option value="phone">Teléfono</option>
					</select>
					<input type="text" name="q" class="input w-full" placeholder="Buscar" />
					<button type="submit" class="button button-primary">Buscar</button>
				</form>
			</div>
			<table>
				<thead>
					<tr>
						<th>Cédula</th>
						<th>Domicilio</th>
						<th>Nombres</th>
						<th>Apellidos</th>
						<th>Teléfono</th>
						<th>Editar</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($usersList->data as $user) { ?>
					<tr>
						<td><?php print_r($user["id_parents"]); ?></td>
						<td><?php print_r($user["address"]); ?></td>
						<td><?php print_r($user["name"]); ?></td>
						<td><?php print_r($user["last_name"]); ?></td>
						<td><?php print_r($user["phone"]); ?></td>
						<td>
							<a href="/consolacion/pages/edit/edit-parent.php?id=<?php print_r($user["id_parents"]); ?>">
								<button class="button button-success button-icon">
									<img src="../../../public/pencil.png" class="w-full" />
								</button>
							</a>
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
			<?php if ($usersList->total_pages > $page) { ?>
			<a href="?page=<?php echo $page + 1 . (!empty($_GET["q"]) ? "&sort={$_GET['sort']}&q={$_GET['q']}" : ""); ?>">
				<button class="button button-pagination">
					<?php echo $page + 1; ?>
				</button>
			</a>
			<?php } ?>
		</div>
	</main>
</body>
</html>