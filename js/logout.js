async function logout() {
	const response = await fetch('/consolacion/server/controller.user.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ route: 'logout', table: 'parents' })
  });
	const text = await response.text();
	const data = JSON.parse(text);
  if (data.statusCode === 200) {
		window.location.replace('/consolacion/login.html');
  }
}