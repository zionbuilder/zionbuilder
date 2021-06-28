window.znpb_set_editor_theme = function (newValue) {
	console.log('asdasd', newValue);
	if (document.body.classList.contains('toplevel_page_zionbuilder')) {
		if (newValue === 'dark') {
			document.body.classList.add('znpb-theme-dark')
		} else {
			document.body.classList.remove('znpb-theme-dark')
		}
	}
}
