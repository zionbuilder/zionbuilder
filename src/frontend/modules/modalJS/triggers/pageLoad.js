function applyTrigger(instance, options) {
	const optionsWithDefaults = {
		delay: 0,
		...options,
	};

	window.addEventListener('load', onPageLoad);

	function onPageLoad() {
		setTimeout(() => {
			instance.open();
		}, optionsWithDefaults.delay);
	}
}

export default {
	name: 'pageLoad',
	fn: applyTrigger,
};
