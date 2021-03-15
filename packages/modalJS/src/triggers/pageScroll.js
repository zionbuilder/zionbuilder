function applyTrigger(instance, options) {

	const optionsWithDefaults = {
		scrollAmmount: 0,
		direction: 'down',
		...options
	}
	let lastScrollTop = 0
	let pageHeight = 0

	window.addEventListener('load', onPageLoad)
	window.addEventListener('scroll', onPageScroll, false)

	function onPageLoad() {
		pageHeight = window.innerHeight
	}

	function onPageScroll(event) {
		const scrollPercentage = pageHeight * optionsWithDefaults.scrollAmmount / 100
		const directionDown = window.scrollY > lastScrollTop

		if (optionsWithDefaults.direction === 'down' && directionDown && window.scrollY > scrollPercentage) {
			instance.open()
			window.removeEventListener('scroll', onPageScroll, false)
		} else if (optionsWithDefaults.direction === 'up' && !directionDown) {
			instance.open()
			window.removeEventListener('scroll', onPageScroll, false)
		}

		// Update scroll position
		lastScrollTop = window.scrollY
	}
}

export default {
	name: 'pageScroll',
	fn: applyTrigger
}
