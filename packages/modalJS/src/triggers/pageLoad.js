function applyTrigger(instance, options) {
	const optionsWithDefaults = {
		delay: 0,
		...options
	}

	window.addEventListener('load', onPageLoad)

	function onPageLoad() {
		console.log('on page load');
		setTimeout(() => {
			instance.open()
		}, optionsWithDefaults.delay);
	}
}

export default {
	name: 'pageLoad',
	fn: applyTrigger
}
