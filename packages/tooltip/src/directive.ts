import { createPopper } from "@popperjs/core";

export const PopperDirective = {
	mounted(el, binding, vnode) {
		const doc = el.ownerDocument

		// Create popper content
		const popperContent = doc.createElement('span')
		popperContent.classList.add('hg-popper', 'hg-popper-tooltip')
		popperContent.innerHTML = binding.value
		popperContent.setAttribute('show-popper', 'true')

		// Create popper arrows
		const arrow = doc.createElement('span')
		arrow.classList.add('hg-popper--with-arrows')
		arrow.setAttribute('data-popper-arrow', 'true')

		popperContent.appendChild(arrow)

		el.__ZnPbHoverEvent__ = function (event) {
			doc.body.appendChild(popperContent)

			// Attach the popper instance to element
			el.__ZnPbPopperInstance__ = createPopper(
				el,
				popperContent,
				{
					placement: 'top',
					modifiers: [
						{
							name: 'offset',
							options: {
								offset: [0, 10],
							},
						},
					],
				}
			);

			// Attach the destroy method to element
			el.__ZnPbDestroyPopper__ = function () {
				// Remove from dom if is inserted
				if (popperContent.parentNode) {
					popperContent.parentNode.removeChild(popperContent)
				}

				if (el.__ZnPbPopperInstance__) {
					el.__ZnPbPopperInstance__.destroy()
					delete el.__ZnPbPopperInstance__
				}
			}
		}

		el.__ZnPbLeaveEvent__ = function (event) {
			// Destroy popper
			if (el.__ZnPbDestroyPopper__) {
				el.__ZnPbDestroyPopper__()
			}
		}

		// Add event listeners
		el.addEventListener('mouseenter', el.__ZnPbHoverEvent__)
		el.addEventListener('mouseleave', el.__ZnPbLeaveEvent__)
	},
	beforeUnmount(el) {
		el.removeEventListener('mouseover', el.__ZnPbHoverEvent__)
		el.removeEventListener('mouseleave', el.__ZnPbLeaveEvent__)

		// Cleanup el
		delete el.__ZnPbHoverEvent__
		delete el.__ZnPbLeaveEvent__

		// Destroy popper
		if (el.__ZnPbDestroyPopper__) {
			el.__ZnPbDestroyPopper__()
		}
	}
}