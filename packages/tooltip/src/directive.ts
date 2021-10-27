import { createPopper } from "@popperjs/core";

export const PopperDirective = {
	mounted(el, { value, arg }, vnode) {
		el.__ZnPbTooltip__ = initTooltip(el, value, arg)
	},
	beforeUnmount(el) {
		if (el.__ZnPbTooltip__) {
			el.__ZnPbTooltip__.destroy()
		}
	},
	updated(el, { value, arg }) {
		if (el.__ZnPbTooltip__) {
			el.__ZnPbTooltip__.setContent(value)

			const popperPosition = arg || 'top'
			el.__ZnPbTooltip__.updatePosition(popperPosition)
		}
	}
}

function initTooltip(element, content, arg) {
	const tooltipObject = {}

	const doc = element.ownerDocument

	// Create popper content
	const popperContent = doc.createElement('span')
	popperContent.classList.add('hg-popper', 'hg-popper-tooltip')
	popperContent.innerHTML = content
	popperContent.setAttribute('show-popper', 'true')

	// Create popper arrows
	const arrow = doc.createElement('span')
	arrow.classList.add('hg-popper--with-arrows')
	arrow.setAttribute('data-popper-arrow', 'true')

	popperContent.appendChild(arrow)

	// Set element and tooltip
	tooltipObject.element = element
	tooltipObject.content = popperContent
	const popperPosition = arg || 'top'

	function showPopper() {
		doc.body.appendChild(popperContent)
		tooltipObject.popper = createPopper(
			element,
			popperContent,
			{
				placement: popperPosition,
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
	}

	function updatePosition(placement) {

		if (tooltipObject.popper) {
			console.log(placement);
			tooltipObject.popper.setOptions({ placement: 'bottom' });

			console.log(tooltipObject.popper);
		}
	}

	function hidePopper() {
		// Remove from dom if is inserted
		if (popperContent.parentNode) {
			popperContent.parentNode.removeChild(popperContent)
		}

		if (tooltipObject.popper) {
			tooltipObject.popper.destroy()
		}
	}

	function setContent(content) {
		if (popperContent.innerHTML !== content) {
			popperContent.innerHTML = content
			popperContent.appendChild(arrow)
		}
	}

	// Add event listeners
	element.addEventListener('mouseenter', showPopper)
	element.addEventListener('mouseleave', hidePopper)

	function destroy() {
		hidePopper()
		element.removeEventListener('mouseenter', showPopper)
		element.removeEventListener('mouseleave', hidePopper)
	}

	return {
		...tooltipObject,
		showPopper,
		hidePopper,
		destroy,
		setContent,
		updatePosition
	}
}