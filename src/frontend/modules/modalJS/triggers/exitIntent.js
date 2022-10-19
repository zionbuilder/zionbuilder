import {
	isMobile
} from "../utils/index.js";

function applyTrigger(instance, options) {
	let lastPosition = null,
		newPosition, timer, delta = 0,
		delay = 50,
		lastScrollPosition = 0

	function clear() {
		lastPosition = null
		delta = 0
	}

	if (isMobile()) {
		document.addEventListener('scroll', exitIntentMobile)
	} else {
		document.addEventListener('mouseout', exitIntentDesktop)
	}

	function getScrollSpeed() {
		if (lastPosition != null) {
			delta = newPosition - lastPosition;
		}

		lastPosition = newPosition
		clearTimeout(timer)
		timer = setTimeout(clear, delay)
		return delta
	}

	function exitIntentMobile(e) {
		const directionDown = window.scrollY > lastScrollPosition
		newPosition = window.scrollY
		lastScrollPosition = window.scrollY

		if (!directionDown && getScrollSpeed() <= -100) {
			instance.open()
			document.removeEventListener('scroll', exitIntentMobile);
		}
	}

	function exitIntentDesktop(e) {
		const shouldShowExitIntent = !e.toElement && !e.relatedTarget && e.clientY < 10;

		if (shouldShowExitIntent) {
			instance.open()
			document.removeEventListener('mouseout', exitIntentDesktop);
		}
	}
}

export default {
	name: 'exitIntent',
	fn: applyTrigger
}
