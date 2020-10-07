
export default {
	install (app) {
		app.directive('click-outside', this)
	},
	beforeMount: function (el, binding, vNode) {
		if (process.env.NODE_ENV !== 'production' && typeof binding.value !== 'function') {
			const compName = vNode.context.name
			let warn = `[Vue Outside Click:] provided expression '${binding.expression}' is not a function!`
			if (compName) { warn += `Found in component '${compName}'` }

			// eslint-disable-next-line
			console.warn(warn)
			return
		}

		const clickOutsideHandler = (event) => {
			if (!el.contains(event.target)) {
				binding.value.call(event)
			}
		}

		el.__CLICK_OUTSIDE_HANDLER = clickOutsideHandler
		// Set the event on capture so we can work with elements that are displayed with v-if on click
		document.addEventListener('contextmenu', el.__CLICK_OUTSIDE_HANDLER, true)
		document.addEventListener('click', el.__CLICK_OUTSIDE_HANDLER, true)
		document.addEventListener('touchstart', el.__CLICK_OUTSIDE_HANDLER, true)
	},
	beforeUnmount: function (el) {
		document.removeEventListener('click', el.__CLICK_OUTSIDE_HANDLER, true)
		document.removeEventListener('touchstart', el.__CLICK_OUTSIDE_HANDLER, true)
		document.removeEventListener('contextmenu', el.__CLICK_OUTSIDE_HANDLER, true)
		el.__CLICK_OUTSIDE_HANDLER = null
	}
}
