function formEvent(event) {
	event.preventDefault();
	const info = Object.fromEntries(new FormData(event.target));
	return info;
}