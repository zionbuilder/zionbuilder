import { ref, Ref } from 'vue'

const activePopup: Ref<null | object> = ref(null)

// TODO: move to utils. It is also used in sortable
const getOffset = (currentDocument) => {
	const iframes = []
	const DOMIframes = document.querySelectorAll('iframe')

	DOMIframes.forEach((iframe) => {
		if (iframe.contentDocument) {
			iframes.push(iframe)
		}
	})

	const frameElement = iframes.find(iframe => iframe.contentDocument === currentDocument)

	if (undefined !== frameElement) {
		const { left, top } = frameElement.getBoundingClientRect()

		return {
			left,
			top
		}
	}

	return {
		left: 0,
		top: 0
	}
}

export function useAddElementsPopup () {
	const showAddElementsPopup = (element, selector) => {
		let modifiers = {}
		// Check if the event comes from iframe
		if (selector.value.ownerDocument !== window.document) {
			// Get the offset
			const offsets = getOffset(selector.value.ownerDocument)
			const { left, top } = offsets
			const positionInIframe = {
				name: 'fixIframePopper',
				enabled: true,
				phase: 'afterRead',
				fn({ state }) {
					const { x, y } = state.rects.reference
					state.rects.reference = {
						...state.rects.reference,
						x: x + left,
						y: y + top
					}
				},
			};


			modifiers = [positionInIframe]
		}


		activePopup.value = {
			element,
			selector,
			modifiers
		}
	}

	const hideAddElementsPopup = () => {
		activePopup.value = null
	}

	return {
		showAddElementsPopup,
		hideAddElementsPopup,
		activePopup
	}
}