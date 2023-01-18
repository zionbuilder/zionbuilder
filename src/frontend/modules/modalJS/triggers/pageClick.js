function applyTrigger(instance, options) {
	const optionsWithDefaults = {
		clicks: 1,
		...options,
	};

	let clicks = 1;

	document.addEventListener('click', onPageLoad);

	function onPageLoad() {
		if (clicks >= optionsWithDefaults.clicks) {
			instance.open();
			document.removeEventListener('click', onPageLoad);
		}
		clicks++;
	}
}

export default {
	name: 'click',
	fn: applyTrigger,
};
