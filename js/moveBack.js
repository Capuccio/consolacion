function moveBack(route) {
//	return document.referrer ? window.history.back() : window.location.href = 'home.html';
	window.location.href = route || '/consolacion/index.php';
}