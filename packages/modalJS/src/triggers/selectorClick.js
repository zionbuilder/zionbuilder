function applyTrigger(instance, options) {
	const optionsWithDefaults = {
		selector: null,
		...options
	}

	if (!optionsWithDefaults.selector) {
		return
	}

	const selectors = document.querySelectorAll(optionsWithDefaults.selector)

	if (selectors.length === 0) {
		return
	}

	selectors.forEach(selector => {
		selector.addEventListener('click', instance.open)
	})
}

export default {
	name: 'selector_click',
	fn: applyTrigger
}
