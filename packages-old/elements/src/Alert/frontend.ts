import './scss/main.scss';

document.addEventListener('click', function (event) {
	const element = <HTMLElement>event.target;
	if (element && element.classList.contains('zb-el-alert__closeIcon')) {
		const alert = element.closest<HTMLElement>('.zb-el-alert');
		alert ? (alert.style.display = 'none') : null;
	}
});
